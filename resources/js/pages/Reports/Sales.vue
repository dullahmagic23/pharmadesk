<template>
    <AppLayout  :breadcrumbs="breadcrumbs">
        <Head title="Sales Report"/>
        <div class="p-6 bg-white rounded-xl shadow space-y-6">
            <div class="flex justify-end space-x-4">
                <form @submit.prevent="getReport()" class="flex w-full items-center space-x-4">
                    <Input v-model="filters.from" type="date" placeholder="From" />
                    <Input v-model="filters.to" type="date" placeholder="To" />
                    <Button type="submit">
                        <SearchIcon class="w-5 h-5" />
                        Filter</Button>
                </form>
                <Button variant="destructive" @click="clearFilters">
                    <DeleteIcon class="w-5 h-5" />
                    Clear filters
                </Button>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-lg font-semibold text-gray-700">
                    Total Sales: 
                    <span class="text-green-600">{{ currency(flattenedItems.reduce((sum, item) => sum + (item.price * item.quantity), 0)) }}</span>
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
            <div v-if="flattenedItems.length" class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b">
                        <tr class="text-left">
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Item</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Quantity</th>
                            <th class="px-4 py-2">Price</th>
                            <th class="px-4 py-2">Total</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in flattenedItems" :key="item.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ i + 1 }}</td>
                            <td class="px-4 py-2">{{ item.sellable?.name || '—' }}</td>
                            <td class="px-4 py-2">{{ readableType(item.sellable_type) }}</td>
                            <td class="px-4 py-2">{{ item.quantity }}</td>
                            <td class="px-4 py-2">{{ currency(item.price) }}</td>
                            <td class="px-4 py-2">{{ currency(item.price * item.quantity) }}</td>
                            <td class="px-4 py-2">{{ formatDate(item.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center text-gray-500">No sales found for the selected range.</div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import { router, useForm, usePage, Head } from '@inertiajs/vue3'
import currency from '@/modules/currecyFormatter'
import axios from 'axios'
import { DeleteIcon, SearchIcon, DownloadIcon, FileTextIcon } from 'lucide-vue-next'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'


const page = usePage()
const sales = ref(page.props.sales || [])
const filters = ref({ ...page.props.filters })


const form = useForm({});

function getReport() {
    if (filters.value.from && filters.value.to) {
        const fromDate = new Date(filters.value.from);
        const toDate = new Date(filters.value.to);

        sales.value = page.props.sales.filter(sale => {
            const saleDate = new Date(sale.created_at);
            return saleDate >= fromDate && saleDate <= new Date(toDate.setHours(23, 59, 59, 999));
        });
    }
}

function clearFilters() {
    filters.value = { from: '', to: '' };
    sales.value = page.props.sales || [];
}

function readableType(type) {
    return type?.split('\\').pop() || 'Unknown'
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString()
}

// Flatten sale items for display
const flattenedItems = computed(() =>
    sales.value.flatMap(sale =>
        (sale.items || []).map(item => ({
            ...item,
            created_at: sale.created_at
        }))
    )
)

const breadcrumbs = [
    {title:'Reports',href:'/reports'},
    {title:'Sales Report',href:'/reports/sales'},
]



function exportToCSV() {
    const headers = ['#', 'Item', 'Type', 'Quantity', 'Price', 'Total', 'Date']
    const rows = flattenedItems.value.map((item, index) => [
        index + 1,
        item.sellable?.name || '—',
        readableType(item.sellable_type),
        item.quantity,
        currency(item.price),
        currency(item.price * item.quantity),
        formatDate(item.created_at)
    ])

    const csvContent = [headers, ...rows]
        .map(row => row.map(cell => `"${cell}"`).join(','))
        .join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'sales_report.csv')
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

function exportToPDF() {
    const doc = new jsPDF()
    const headers = [['#', 'Item', 'Type', 'Quantity', 'Price', 'Total', 'Date']]
    const rows = flattenedItems.value.map((item, index) => [
        index + 1,
        item.sellable?.name || '—',
        readableType(item.sellable_type),
        item.quantity,
        currency(item.price),
        currency(item.price * item.quantity),
        formatDate(item.created_at)
    ])

    autoTable(doc, {
        head: headers,
        body: rows,
        startY: 10,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [22, 160, 133] }
    })

    doc.save('sales_report.pdf')
}
</script>
