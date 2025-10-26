<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import currency from '@/modules/currecyFormatter'
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue'

const props = defineProps({
    payments: Object,
    stats: Object,
    methods: Array,
    filters: Object,
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Sales', href: '/sales' },
    { title: 'Payments', href: '/payments' },
]

// Form state
const searchQuery = ref(props.filters.search || '')
const selectedMethod = ref(props.filters.method || '')
const selectedStatus = ref(props.filters.status || '')
const fromDate = ref(props.filters.from_date || '')
const toDate = ref(props.filters.to_date || '')
const isExporting = ref(false)

// Handle filter changes
const applyFilters = () => {
    router.get('/payments', {
        search: searchQuery.value,
        method: selectedMethod.value,
        status: selectedStatus.value,
        from_date: fromDate.value,
        to_date: toDate.value,
    }, { preserveScroll: true })
}

const resetFilters = () => {
    searchQuery.value = ''
    selectedMethod.value = ''
    selectedStatus.value = ''
    fromDate.value = ''
    toDate.value = ''
    router.get('/sales-payments', {}, { preserveScroll: true })
}

const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedMethod.value || selectedStatus.value || fromDate.value || toDate.value
})

// Export functionality
const exportToCSV = async () => {
    isExporting.value = true
    try {
        const response = await fetch('/payments/export', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({
                search: searchQuery.value,
                method: selectedMethod.value,
                status: selectedStatus.value,
                from_date: fromDate.value,
                to_date: toDate.value,
            }),
        })

        if (!response.ok) throw new Error('Export failed')

        const blob = await response.blob()
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `payments-${new Date().toISOString().split('T')[0]}.csv`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
    } catch (error) {
        console.error('Export error:', error)
        alert('Failed to export payments')
    } finally {
        isExporting.value = false
    }
}

// Status badge styling
const getStatusConfig = (status) => {
    const config = {
        paid: { bg: 'bg-green-100 dark:bg-green-900', text: 'text-green-800 dark:text-green-200' },
        partial: { bg: 'bg-yellow-100 dark:bg-yellow-900', text: 'text-yellow-800 dark:text-yellow-200' },
        unpaid: { bg: 'bg-red-100 dark:bg-red-900', text: 'text-red-800 dark:text-red-200' },
    }
    return config[status] || config.pending
}

const formatCurrency = (value) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)

const formatDate = (date) => new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Payments" />

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Sales Payments</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and track all payment transactions</p>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Payments</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.total_payments }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Amount</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ currency(stats.total_amount) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ stats.completed_count }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ stats.pending_count }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input
                                v-model="searchQuery"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="ID, Reference, Customer..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select
                                v-model="selectedStatus"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="partial">Partial</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                            <input
                                v-model="fromDate"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                            <input
                                v-model="toDate"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button
                            @click="applyFilters"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition"
                        >
                            <i class="fas fa-search mr-2"></i>Apply Filters
                        </button>
                        <button
                            v-if="hasActiveFilters"
                            @click="resetFilters"
                            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                        >
                            <i class="fas fa-redo mr-2"></i>Reset
                        </button>
                        <button
                            @click="exportToCSV"
                            :disabled="isExporting"
                            class="ml-auto px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white rounded-lg font-medium transition"
                        >
                            <i :class="['fas', isExporting ? 'fa-spinner fa-spin' : 'fa-download', 'mr-2']"></i>
                            {{ isExporting ? 'Exporting...' : 'Export CSV' }}
                        </button>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div v-if="payments.data.length" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Payment ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="payment in payments.data"
                                    :key="payment.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                >
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">#{{ payment.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ payment.sale?.buyer?.name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ currency(payment.amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span :class="[getStatusConfig(payment.sale.status).bg, getStatusConfig(payment.sale.status).text, 'px-3 py-1 rounded-full text-xs font-semibold']">
                                            {{ payment.sale.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ formatDate(payment.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <Link
                                            :href="route('sales-payments.show',  payment.id)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition text-xs font-medium"
                                        >
                                            <i class="fas fa-eye"></i>View
                                        </Link>
                                        <ConfirmDeleteDialog
                                            :title="`Delete Payment #${payment.id}`"
                                            :description="`Are you sure you want to delete payment #${payment.id}? This action cannot be undone.`"
                                            buttonLabel="Delete"
                                            button-label-text-color="text-red-600 hover:text-red-700"
                                            triggerVariant="ghost"
                                            :onConfirm="() => router.delete(`/sales-payments/${payment.id}`, { preserveScroll: true })"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="p-12 text-center">
                        <p class="text-4xl mb-3">💳</p>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No Payments Found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Try adjusting your filters or check back later</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="payments.links.length > 3" class="flex justify-center gap-2">
                    <Link
                        v-for="link in payments.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition',
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : link.url
                                ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
