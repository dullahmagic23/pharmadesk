<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import * as XLSX from 'xlsx'
import { Head } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { SearchIcon, DownloadIcon, AlertCircleIcon } from 'lucide-vue-next'

const props = defineProps({
    stocks: Array,
})

const searchQuery = ref('')
const filterStatus = ref('all')
const sortBy = ref('name') // 'name', 'quantity', 'stock-in', 'stock-out'
const sortOrder = ref('asc') // 'asc', 'desc'

const filteredStocks = computed(() => {
    let filtered = props.stocks.filter(stock => {
        const matchesSearch = stock.item_name.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesStatus = filterStatus.value === 'all'
            ? true
            : filterStatus.value === 'in-stock'
                ? stock.quantity > 0
                : stock.quantity <= 0

        return matchesSearch && matchesStatus
    })

    // Sort filtered results
    filtered.sort((a, b) => {
        let aVal, bVal

        switch (sortBy.value) {
            case 'quantity':
                aVal = a.quantity
                bVal = b.quantity
                break
            case 'stock-in':
                aVal = a.stock_in
                bVal = b.stock_in
                break
            case 'stock-out':
                aVal = a.stock_out
                bVal = b.stock_out
                break
            default:
                aVal = a.item_name.toLowerCase()
                bVal = b.item_name.toLowerCase()
        }

        if (sortOrder.value === 'asc') {
            return aVal > bVal ? 1 : aVal < bVal ? -1 : 0
        } else {
            return aVal < bVal ? 1 : aVal > bVal ? -1 : 0
        }
    })

    return filtered
})

const totalStockIn = computed(() => filteredStocks.value.reduce((sum, s) => sum + s.stock_in, 0))
const totalStockOut = computed(() => filteredStocks.value.reduce((sum, s) => sum + s.stock_out, 0))
const totalQuantity = computed(() => filteredStocks.value.reduce((sum, s) => sum + removeDecimalPoints(s.quantity), 0))

const outOfStockCount = computed(() => filteredStocks.value.filter(s => s.quantity <= 0).length)
const lowStockCount = computed(() => filteredStocks.value.filter(s => s.quantity > 0 && s.quantity < 10).length)

const stockHealthStatus = computed(() => {
    if (outOfStockCount.value > 0) return { status: 'critical', label: 'Critical', color: 'red' }
    if (lowStockCount.value > 0) return { status: 'warning', label: 'Low Stock', color: 'amber' }
    return { status: 'healthy', label: 'Healthy', color: 'green' }
})

const removeDecimalPoints = (value) => Math.trunc(value)

const toggleSort = (column) => {
    if (sortBy.value === column) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortBy.value = column
        sortOrder.value = 'asc'
    }
}

const getSortIcon = (column) => {
    if (sortBy.value !== column) return ' ⇅'
    return sortOrder.value === 'asc' ? ' ↑' : ' ↓'
}

const exportToExcel = () => {
    const headers = ['Item Name', 'Stock In', 'Stock Out', 'Current Quantity', 'Status']
    const rows = filteredStocks.value.map(stock => ({
        'Item Name': stock.item_name,
        'Stock In': stock.stock_in,
        'Stock Out': stock.stock_out,
        'Current Quantity': removeDecimalPoints(stock.quantity),
        'Status': stock.quantity <= 0 ? 'Out of Stock' : stock.quantity < 10 ? 'Low Stock' : 'In Stock'
    }))

    rows.push({
        'Item Name': 'TOTAL',
        'Stock In': totalStockIn.value,
        'Stock Out': totalStockOut.value,
        'Current Quantity': totalQuantity.value,
        'Status': ''
    })

    const worksheet = XLSX.utils.json_to_sheet(rows)
    const columnWidths = [25, 12, 12, 18, 12]
    worksheet['!cols'] = columnWidths.map(w => ({ wch: w }))

    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Stock Report')
    XLSX.writeFile(workbook, `stock-report-${new Date().toISOString().split('T')[0]}.xlsx`)
}

const getStatusBadge = (quantity) => {
    if (quantity <= 0) return { label: 'Out of Stock', color: 'red' }
    if (quantity < 10) return { label: 'Low Stock', color: 'amber' }
    return { label: 'In Stock', color: 'green' }
}

const breadcrumbs = [
    { title: 'Reports', href: '/reports' },
    { title: 'Stock Report', href: '/reports/stock' },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stock Report" />
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Stock Report</h1>
                <p class="text-slate-600 mt-1">Monitor inventory levels and stock movements</p>
            </div>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <p class="text-sm font-medium text-slate-600 mb-2">Total Items</p>
                    <p class="text-2xl font-bold text-slate-900">{{ filteredStocks.length }}</p>
                    <p class="text-xs text-slate-500 mt-2">of {{ props.stocks.length }} total</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <p class="text-sm font-medium text-slate-600 mb-2">Total Stock In</p>
                    <p class="text-2xl font-bold text-blue-600">{{ totalStockIn }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <p class="text-sm font-medium text-slate-600 mb-2">Total Stock Out</p>
                    <p class="text-2xl font-bold text-orange-600">{{ totalStockOut }}</p>
                </div>

                <div :class="['bg-white rounded-xl shadow-sm border p-6', {
                    'border-green-200': stockHealthStatus.color === 'green',
                    'border-amber-200': stockHealthStatus.color === 'amber',
                    'border-red-200': stockHealthStatus.color === 'red'
                }]">
                    <p class="text-sm font-medium text-slate-600 mb-2">Inventory Status</p>
                    <p :class="['text-2xl font-bold', {
                        'text-green-600': stockHealthStatus.color === 'green',
                        'text-amber-600': stockHealthStatus.color === 'amber',
                        'text-red-600': stockHealthStatus.color === 'red'
                    }]">
                        {{ stockHealthStatus.label }}
                    </p>
                </div>
            </div>

            <!-- Alerts -->
            <div v-if="outOfStockCount > 0 || lowStockCount > 0" class="mb-6 space-y-3">
                <div v-if="outOfStockCount > 0" class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                    <AlertCircleIcon class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="font-semibold text-red-900">{{ outOfStockCount }} item(s) out of stock</p>
                        <p class="text-sm text-red-700">Immediate replenishment required</p>
                    </div>
                </div>
                <div v-if="lowStockCount > 0" class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">
                    <AlertCircleIcon class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="font-semibold text-amber-900">{{ lowStockCount }} item(s) with low stock</p>
                        <p class="text-sm text-amber-700">Consider placing orders soon</p>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="relative">
                        <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <Input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search items..."
                            class="pl-10 w-full"
                        />
                    </div>
                    <select
                        v-model="filterStatus"
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    >
                        <option value="all">All Items</option>
                        <option value="in-stock">In Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                    <select
                        v-model="sortBy"
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    >
                        <option value="name">Sort by Name</option>
                        <option value="quantity">Sort by Quantity</option>
                        <option value="stock-in">Sort by Stock In</option>
                        <option value="stock-out">Sort by Stock Out</option>
                    </select>
                    <Button @click="exportToExcel" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700">
                        <DownloadIcon class="w-4 h-4" />
                        Export Excel
                    </Button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('name')">
                                    Item {{ getSortIcon('name') }}
                                </th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('stock-in')">
                                    Stock In {{ getSortIcon('stock-in') }}
                                </th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('stock-out')">
                                    Stock Out {{ getSortIcon('stock-out') }}
                                </th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('quantity')">
                                    Current Qty {{ getSortIcon('quantity') }}
                                </th>
                                <th class="px-6 py-4 text-center font-semibold text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr v-for="stock in filteredStocks" :key="stock.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ stock.item_name }}</td>
                                <td class="px-6 py-4 text-right text-slate-600">{{ stock.stock_in }}</td>
                                <td class="px-6 py-4 text-right text-slate-600">{{ stock.stock_out }}</td>
                                <td class="px-6 py-4 text-right font-medium text-slate-900">{{ removeDecimalPoints(stock.quantity) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="['px-3 py-1 rounded-full text-xs font-semibold', {
                                        'bg-red-100 text-red-700': getStatusBadge(stock.quantity).color === 'red',
                                        'bg-amber-100 text-amber-700': getStatusBadge(stock.quantity).color === 'amber',
                                        'bg-green-100 text-green-700': getStatusBadge(stock.quantity).color === 'green'
                                    }]">
                                        {{ getStatusBadge(stock.quantity).label }}
                                    </span>
                                </td>
                            </tr>

                            <!-- No results message -->
                            <tr v-if="filteredStocks.length === 0">
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="text-slate-500">
                                        <p class="text-lg font-medium">No items found</p>
                                        <p class="text-sm mt-1">Try adjusting your search or filters</p>
                                    </div>
                                </td>
                            </tr>

                            <!-- Totals row -->
                            <tr v-else class="bg-slate-50 font-semibold border-t-2 border-slate-300">
                                <td class="px-6 py-4 text-slate-900">Total</td>
                                <td class="px-6 py-4 text-right text-blue-600">{{ totalStockIn }}</td>
                                <td class="px-6 py-4 text-right text-orange-600">{{ totalStockOut }}</td>
                                <td class="px-6 py-4 text-right text-slate-900">{{ totalQuantity }}</td>
                                <td class="px-6 py-4 text-center">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
