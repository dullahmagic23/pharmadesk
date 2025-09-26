<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Sales Report" />
        <div class="p-6 bg-white rounded-xl shadow space-y-6">
            <!-- Filters -->
            <div class="flex justify-end space-x-4">
                <Input v-model="filters.from" type="date" placeholder="From" />
                <Input v-model="filters.to" type="date" placeholder="To" />
                <Button variant="destructive" @click="clearFilters">
                    <DeleteIcon class="w-5 h-5" />
                    Clear filters
                </Button>
            </div>

            <!-- Totals -->
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="space-y-1 text-lg font-semibold text-gray-700">
                    <div>
                        Total Sales:
                        <span class="text-green-600">
                            {{ currency(totalSales) }}
                        </span>
                    </div>
                    <div>
                        Total Profit:
                        <span class="text-blue-600">
                            {{ currency(totalProfit) }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center md:justify-end space-x-2">
                    <Button @click="exportToCSV" variant="outline" class="flex items-center space-x-2">
                        <DownloadIcon class="w-5 h-5" />
                        <span>Export to CSV</span>
                    </Button>
                    <Button @click="exportToPDF" variant="outline" class="flex items-center space-x-2">
                        <FileTextIcon class="w-5 h-5" />
                        <span>Export to PDF</span>
                    </Button>
                </div>
            </div>

            <!-- Sales Table -->
            <div v-if="flattenedItems.length" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b">
                        <tr class="text-left">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Item</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Quantity</th>
                            <th class="px-4 py-2">Purchase cost</th>
                            <th class="px-4 py-2">Selling cost</th>
                            <th class="px-4 py-2">Profit made</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in flattenedItems" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ i + 1 }}</td>
                            <td class="px-4 py-2">{{ item.sellable?.name || '—' }}</td>
                            <td class="px-4 py-2">{{ readableType(item.sellable_type) }}</td>
                            <td class="px-4 py-2">{{ item.quantity }}</td>
                            <td class="px-4 py-2">
                                {{ currency(item.sellable?.stock?.buying_price * item.quantity) }}
                            </td>
                            <td class="px-4 py-2">{{ currency(item.price * item.quantity) }}</td>
                            <td class="px-4 py-2">
                                {{ currency((item.price * item.quantity) - (item.sellable?.stock?.buying_price * item.quantity)) }}
                            </td>
                            <td class="px-4 py-2">{{ formatDate(item.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center text-gray-500">
                No sales found for the selected range.
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import currency from '@/modules/currecyFormatter'
import { DeleteIcon, DownloadIcon, FileTextIcon } from 'lucide-vue-next'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

const page = usePage()
const sales = ref(page.props.sales || [])
const filters = ref({ ...page.props.filters })

function clearFilters() {
    filters.value = { from: '', to: '' }
}

// Filtered sales computed property
const filteredSales = computed(() => {
    if (!filters.value.from && !filters.value.to) return sales.value
    return sales.value.filter(sale => {
        const saleDate = new Date(sale.created_at)
        let fromOk = true
        let toOk = true
        if (filters.value.from) {
            const fromDate = new Date(filters.value.from)
            fromDate.setHours(0, 0, 0, 0)
            fromOk = saleDate >= fromDate
        }
        if (filters.value.to) {
            const toDate = new Date(filters.value.to)
            toDate.setHours(23, 59, 59, 999)
            toOk = saleDate <= toDate
        }
        return fromOk && toOk
    })
})

// Flatten sale items for display
const flattenedItems = computed(() =>
    filteredSales.value.flatMap(sale =>
        (sale.items || []).map(item => ({
            ...item,
            created_at: sale.created_at
        }))
    )
)

// Totals
const totalSales = computed(() =>
    flattenedItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
)

const totalProfit = computed(() =>
    flattenedItems.value.reduce(
        (sum, item) =>
            sum +
            ((item.price * item.quantity) -
            (item.sellable?.stock?.buying_price * item.quantity || 0)),
        0
    )
)

function readableType(type) {
    return type?.split('\\').pop() || 'Unknown'
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString()
}

const currencyValue = (value) => {
  return Number(value ?? 0).toFixed(2); // raw number, 2 decimals
}

function exportToCSV() {
    const headers = ['#', 'Item', 'Type', 'Quantity', 'Purchase cost', 'Selling cost', 'Profit', 'Date'];

    const rows = flattenedItems.value.map((item, index) => {
        const purchase = item.sellable?.stock?.buying_price * item.quantity || 0;
        const selling = item.price * item.quantity;
        const profit = selling - purchase;

        return [
            index + 1,
            item.sellable?.name || '—',
            readableType(item.sellable_type),
            item.quantity,
            currencyValue(purchase),
            currencyValue(selling),
            currencyValue(profit),
            formatDate(item.created_at)
        ];
    });

    // Summary row
    rows.push([]);
    rows.push([
        '', '', '', '',
        'Total Sales', currencyValue(totalSales.value),
        'Total Profit', currencyValue(totalProfit.value)
    ]);

    const csvContent = [headers, ...rows]
        .map(row => row.map(cell => `"${cell}"`).join(','))
        .join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'sales_report.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


// PDF Export
function exportToPDF() {
    const doc = new jsPDF()
    const headers = [['#', 'Item', 'Type', 'Quantity', 'Purchase cost', 'Selling cost', 'Profit', 'Date']]
    const rows = flattenedItems.value.map((item, index) => [
        index + 1,
        item.sellable?.name || '—',
        readableType(item.sellable_type),
        item.quantity,
        currency(item.sellable?.stock?.buying_price * item.quantity),
        currency(item.price * item.quantity),
        currency((item.price * item.quantity) - (item.sellable?.stock?.buying_price * item.quantity)),
        formatDate(item.created_at)
    ])

    autoTable(doc, {
        head: headers,
        body: rows,
        startY: 10,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [22, 160, 133] }
    })

    // Add summary rows below table
    const finalY = doc.lastAutoTable.finalY || 10
    doc.setFontSize(10)
    doc.text(`Total Sales: ${currency(totalSales.value)}`, 14, finalY + 10)
    doc.text(`Total Profit: ${currency(totalProfit.value)}`, 14, finalY + 20)

    doc.save('sales_report.pdf')
}

const breadcrumbs = [
    { title: 'Reports', href: '/reports' },
    { title: 'Sales Report', href: '/reports/sales' },
]
</script>
