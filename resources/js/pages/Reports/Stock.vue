<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import * as XLSX from 'xlsx'
import { Head } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'

const props = defineProps({
    stocks: Array,
})

// Add search and filter states
const searchQuery = ref('')
const filterStatus = ref('all') // 'all', 'in-stock', 'out-of-stock'

// Filtered stocks computed property
const filteredStocks = computed(() => {
    return props.stocks.filter(stock => {
        const matchesSearch = stock.item_name.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesStatus = filterStatus.value === 'all' 
            ? true 
            : filterStatus.value === 'in-stock' 
                ? stock.quantity > 0 
                : stock.quantity <= 0

        return matchesSearch && matchesStatus
    })
})

// Update computed totals to use filtered stocks
const totalStockIn = computed(() => filteredStocks.value.reduce((sum, s) => sum + s.stock_in, 0))
const totalStockOut = computed(() => filteredStocks.value.reduce((sum, s) => sum + s.stock_out, 0))
const totalQuantity = computed(() => filteredStocks.value.reduce((sum, s) => sum + removeDecimalPoints(s.quantity), 0))

// Update export to use filtered data
const exportToExcel = () => {
    const rows = filteredStocks.value.map(stock => ({
        'Item Name': stock.item_name,
        'Stock In': stock.stock_in,
        'Stock Out': stock.stock_out,
        'Current Quantity': removeDecimalPoints(stock.quantity),
    }))

    const worksheet = XLSX.utils.json_to_sheet(rows)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Stock Report')
    XLSX.writeFile(workbook, 'stock-report.xlsx')
}

const removeDecimalPoints = (value) => {
    return Math.trunc(value);
}
const breadcrumbs = [
    {title:'Reports',href:'/reports'},
    {title:'Stock Report',href:'/reports/stock'},
]

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stock Report" />
        <div class="container p-6">
            <h1 class="text-2xl font-semibold mb-4">Stock Report</h1>

            <!-- Filters Section -->
            <div class="mb-6 flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/3">
                    <Input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search items..."
                        class="w-full"
                    />
                </div>
                <div class="w-full md:w-1/3">
                    <select
                        v-model="filterStatus"
                        class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="all">All Items</option>
                        <option value="in-stock">In Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                </div>
                <div class="flex-shrink-0">
                    <button 
                        @click="exportToExcel"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow"
                    >
                        Export to Excel
                    </button>
                </div>
            </div>

            <!-- Results Count -->
            <div class="mb-4 text-sm text-gray-600">
                Showing {{ filteredStocks.length }} of {{ props.stocks.length }} items
            </div>

            <!-- Table -->
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full text-sm text-left border">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 border">Item</th>
                            <th class="px-4 py-2 border">Stock In</th>
                            <th class="px-4 py-2 border">Stock Out</th>
                            <th class="px-4 py-2 border">Current Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stock in filteredStocks" :key="stock.id">
                            <td class="px-4 py-2 border">{{ stock.item_name }}</td>
                            <td class="px-4 py-2 border">{{ stock.stock_in }}</td>
                            <td class="px-4 py-2 border">{{ stock.stock_out }}</td>
                            <td class="px-4 py-2 border">{{ removeDecimalPoints(stock.quantity) }}</td>
                        </tr>
                        <!-- No results message -->
                        <tr v-if="filteredStocks.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                No items found matching your filters.
                            </td>
                        </tr>
                        <!-- Totals row -->
                        <tr v-else class="font-semibold bg-gray-50">
                            <td class="px-4 py-2 border">Total</td>
                            <td class="px-4 py-2 border">{{ totalStockIn }}</td>
                            <td class="px-4 py-2 border">{{ totalStockOut }}</td>
                            <td class="px-4 py-2 border">{{ totalQuantity }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>