<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Sales Report" />
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Sales Report</h1>
                <p class="text-slate-600 mt-1">Track and analyze your sales performance</p>
            </div>

            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Filters Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                        <div class="flex flex-col sm:flex-row gap-3 flex-1">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">From</label>
                                <Input v-model="filters.from" type="date" class="w-full" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-700 mb-2">To</label>
                                <Input v-model="filters.to" type="date" class="w-full" />
                            </div>
                        </div>
                        <Button
                            variant="outline"
                            @click="clearFilters"
                            class="flex items-center justify-center gap-2"
                        >
                            <DeleteIcon class="w-4 h-4" />
                            Clear
                        </Button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        title="Total Sales"
                        :value="currency(totalSales)"
                        icon="TrendingUp"
                        color="blue"
                    />
                    <StatCard
                        title="Total Profit"
                        :value="currency(totalProfit)"
                        icon="DollarSign"
                        color="green"
                    />
                    <StatCard
                        title="Items Sold"
                        :value="flattenedItems.length"
                        icon="ShoppingCart"
                        color="purple"
                    />
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Table Header with Export -->
                    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h2 class="text-lg font-semibold text-slate-900">Sales Details</h2>
                        <div class="flex gap-2">
                            <Button
                                @click="exportToCSV"
                                variant="outline"
                                class="flex items-center gap-2"
                            >
                                <DownloadIcon class="w-4 h-4" />
                                <span class="hidden sm:inline">CSV</span>
                            </Button>
                            <Button
                                @click="exportToPDF"
                                variant="outline"
                                class="flex items-center gap-2"
                            >
                                <FileTextIcon class="w-4 h-4" />
                                <span class="hidden sm:inline">PDF</span>
                            </Button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div v-if="flattenedItems.length" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-left font-semibold text-slate-700">#</th>
                                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Item</th>
                                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Type</th>
                                    <th class="px-6 py-4 text-right font-semibold text-slate-700">Qty</th>
                                    <th class="px-6 py-4 text-right font-semibold text-slate-700">Purchase</th>
                                    <th class="px-6 py-4 text-right font-semibold text-slate-700">Selling</th>
                                    <th class="px-6 py-4 text-right font-semibold text-slate-700">Profit</th>
                                    <th class="px-6 py-4 text-left font-semibold text-slate-700">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr
                                    v-for="(item, i) in flattenedItems"
                                    :key="item.id"
                                    class="hover:bg-slate-50 transition-colors"
                                >
                                    <td class="px-6 py-4 text-slate-900">{{ i + 1 }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ item.sellable?.name || '—' }}</td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">
                                            {{ readableType(item.sellable_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-900">{{ item.quantity }}</td>
                                    <td class="px-6 py-4 text-right text-slate-600">
                                        {{ currency(item.sellable?.stock?.buying_price * item.quantity) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-900 font-medium">
                                        {{ currency(item.price * item.quantity) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            :class="profitClass((item.price * item.quantity) - (item.sellable?.stock?.buying_price * item.quantity))"
                                        >
                                            {{ currency((item.price * item.quantity) - (item.sellable?.stock?.buying_price * item.quantity)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ formatDate(item.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="px-6 py-16 text-center">
                        <ShoppingCartIcon class="w-12 h-12 text-slate-300 mx-auto mb-4" />
                        <p class="text-slate-500 text-lg">No sales found for the selected range</p>
                    </div>

                    <!-- Summary Footer -->
                    <div v-if="flattenedItems.length" class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                            <div class="flex gap-8">
                                <div>
                                    <p class="text-sm text-slate-600">Total Sales</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ currency(totalSales) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-600">Total Profit</p>
                                    <p class="text-2xl font-bold text-green-600">{{ currency(totalProfit) }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-slate-600">
                                Profit Margin: <span class="font-semibold text-slate-900">{{ profitMargin }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
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
import { DeleteIcon, DownloadIcon, FileTextIcon, ShoppingCartIcon } from 'lucide-vue-next'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

const StatCard = {
    props: ['title', 'value', 'icon', 'color'],
    template: `
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 mb-2">{{ title }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ value }}</p>
                </div>
                <div :class="['w-12 h-12 rounded-lg flex items-center justify-center', {
                    'bg-blue-100': color === 'blue',
                    'bg-green-100': color === 'green',
                    'bg-purple-100': color === 'purple'
                }]">
                    <span :class="['text-xl', {
                        'text-blue-600': color === 'blue',
                        'text-green-600': color === 'green',
                        'text-purple-600': color === 'purple'
                    }]">📈</span>
                </div>
            </div>
        </div>
    `
}

const page = usePage()
const sales = ref(page.props.sales || [])
const filters = ref({ ...page.props.filters })

function clearFilters() {
    filters.value = { from: '', to: '' }
}

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

const flattenedItems = computed(() =>
    filteredSales.value.flatMap(sale =>
        (sale.items || []).map(item => ({
            ...item,
            created_at: sale.created_at
        }))
    )
)

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

const profitMargin = computed(() => {
    if (totalSales.value === 0) return 0
    return ((totalProfit.value / totalSales.value) * 100).toFixed(1)
})

function readableType(type) {
    return type?.split('\\').pop() || 'Unknown'
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

function profitClass(profit) {
    if (profit > 0) return 'text-green-600 font-semibold'
    if (profit < 0) return 'text-red-600 font-semibold'
    return 'text-slate-600'
}

const currencyValue = (value) => Number(value ?? 0).toFixed(2)

function exportToCSV() {
    const headers = ['#', 'Item', 'Type', 'Quantity', 'Purchase cost', 'Selling cost', 'Profit', 'Date']

    const rows = flattenedItems.value.map((item, index) => {
        const purchase = item.sellable?.stock?.buying_price * item.quantity || 0
        const selling = item.price * item.quantity
        const profit = selling - purchase

        return [
            index + 1,
            item.sellable?.name || '—',
            readableType(item.sellable_type),
            item.quantity,
            currencyValue(purchase),
            currencyValue(selling),
            currencyValue(profit),
            formatDate(item.created_at)
        ]
    })

    rows.push([])
    rows.push([
        '', '', '', '',
        'Total Sales', currencyValue(totalSales.value),
        'Total Profit', currencyValue(totalProfit.value)
    ])

    const csvContent = [headers, ...rows]
        .map(row => row.map(cell => `"${cell}"`).join(','))
        .join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `sales_report_${new Date().toISOString().split('T')[0]}.csv`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

function exportToPDF() {
    const doc = new jsPDF()
    const headers = [['#', 'Item', 'Type', 'Qty', 'Purchase', 'Selling', 'Profit', 'Date']]
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

    doc.setFont(undefined, 'bold')
    doc.setFontSize(16)
    doc.text('Sales Report', 14, 10)
    doc.setFont(undefined, 'normal')
    doc.setFontSize(10)
    doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 18)

    autoTable(doc, {
        head: headers,
        body: rows,
        startY: 25,
        styles: { fontSize: 9, cellPadding: 3 },
        headStyles: { fillColor: [15, 23, 42], textColor: [255, 255, 255], fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [248, 250, 252] }
    })

    const finalY = doc.lastAutoTable.finalY + 10
    doc.setFont(undefined, 'bold')
    doc.text(`Total Sales: ${currency(totalSales.value)}`, 14, finalY)
    doc.text(`Total Profit: ${currency(totalProfit.value)}`, 14, finalY + 7)
    doc.text(`Profit Margin: ${profitMargin.value}%`, 14, finalY + 14)

    doc.save(`sales_report_${new Date().toISOString().split('T')[0]}.pdf`)
}

const breadcrumbs = [
    { title: 'Reports', href: '/reports' },
    { title: 'Sales Report', href: '/reports/sales' },
]
</script>
