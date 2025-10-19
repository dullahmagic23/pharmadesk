<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Search, Printer, Trash2, Eye, Download, Filter, ChevronDown } from 'lucide-vue-next'
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue'

const props = defineProps({
    receipts: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const search = ref(props.filters?.search || '')
const showFilters = ref(false)
const sortBy = ref('issued_at')
const sortOrder = ref('desc')

const filteredReceipts = computed(() => {
    let results = props.receipts

    // Apply search filter
    if (search.value) {
        results = results.filter(r =>
            (r.reference?.toLowerCase().includes(search.value.toLowerCase())) ||
            (r.sale_id?.toString().includes(search.value)) ||
            (r.issued_by?.toLowerCase().includes(search.value.toLowerCase()))
        )
    }

    // Apply sorting
    results.sort((a, b) => {
        let aVal = a[sortBy.value]
        let bVal = b[sortBy.value]

        if (sortBy.value === 'issued_at') {
            aVal = new Date(aVal).getTime()
            bVal = new Date(bVal).getTime()
        }

        if (sortOrder.value === 'asc') {
            return aVal > bVal ? 1 : -1
        } else {
            return aVal < bVal ? 1 : -1
        }
    })

    return results
})

const stats = computed(() => {
    return {
        total: props.receipts.length,
        totalAmount: props.receipts.reduce((sum, r) => sum + Number(r.sale?.total || 0), 0),
        recent: props.receipts.slice(0, 5).length
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Receipts', href: '/receipts' }
]

const handleDelete = (id) => {
    router.delete(`/receipts/${id}`, {
        preserveScroll: true
    })
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-TZ', {
        style: 'currency',
        currency: 'TZS',
        minimumFractionDigits: 0
    }).format(value || 0)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Receipts" />

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-4 md:px-6 py-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Receipts</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Manage and print receipt records</p>
                        </div>
                        <Link :href="route('receipts.create')">
                            <Button class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 rounded-lg gap-2 h-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                <span>New Receipt</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-8 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Receipts</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.total }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">All time</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Amount</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">{{ formatCurrency(stats.totalAmount) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Combined value</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Filtered Results</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ filteredReceipts.length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Matching search</p>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <Search class="w-5 h-5 text-gray-400 flex-shrink-0" />
                            <Input
                                v-model="search"
                                placeholder="Search by reference, sale ID, or cashier name..."
                                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <!-- Sort Controls -->
                        <div class="flex flex-wrap gap-3 items-center">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Sort by:</span>
                                <select
                                    v-model="sortBy"
                                    class="px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="issued_at">Date</option>
                                    <option value="reference">Reference</option>
                                    <option value="issued_by">Cashier</option>
                                </select>
                                <button
                                    @click="sortOrder = sortOrder === 'asc' ? 'desc' : 'asc'"
                                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                                    :title="`Sort ${sortOrder === 'asc' ? 'descending' : 'ascending'}`"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipts Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div v-if="filteredReceipts.length" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr class="text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <th class="px-6 py-3">Reference</th>
                                    <th class="px-6 py-3">Sale ID</th>
                                    <th class="px-6 py-3 text-right">Amount</th>
                                    <th class="px-6 py-3">Issued By</th>
                                    <th class="px-6 py-3">Date & Time</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="receipt in filteredReceipts"
                                    :key="receipt.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group"
                                >
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ receipt.reference || '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <Link
                                            :href="`/sales/${receipt.sale_id}`"
                                            class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                                        >
                                            #{{ receipt.sale_id }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-900 dark:text-white">
                                        {{ formatCurrency(receipt.sale?.total) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ receipt.issued_by || '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="flex flex-col">
                                            <span>{{ formatDate(receipt.issued_at) }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-500">{{ formatTime(receipt.issued_at) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a :href="`/receipts/${receipt.id}`" target="_blank">
                                                <Button
                                                    title="View Receipt"
                                                    variant="ghost"
                                                    size="icon"
                                                    class="h-8 w-8 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded"
                                                >
                                                    <Eye class="w-4 h-4" />
                                                </Button>
                                            </a>
                                            <a :href="`/sales/${receipt.sale_id}/receipt`" target="_blank">
                                                <Button
                                                    title="Print Receipt"
                                                    variant="ghost"
                                                    size="icon"
                                                    class="h-8 w-8 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded"
                                                >
                                                    <Printer class="w-4 h-4" />
                                                </Button>
                                            </a>
                                            <ConfirmDeleteDialog
                                                :title="`Delete Receipt #${receipt.id}`"
                                                :description="'Are you sure you want to delete this receipt? This action cannot be undone.'"
                                                button-label="Delete"
                                                trigger-variant="ghost"
                                                :on-confirm="() => handleDelete(receipt.id)"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-4">No receipts found</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                <span v-if="search">Try adjusting your search terms</span>
                                <span v-else>Create your first receipt to get started</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
