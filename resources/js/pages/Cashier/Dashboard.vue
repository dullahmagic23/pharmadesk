<template>
    <AppLayout :breadcrumbs="breadcrumb">
        <Head title="Cashier Dashboard" />

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-4 md:px-6 py-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold">Welcome back, {{ user.name }}</h1>
                            <p class="text-indigo-100 mt-2">{{ user.roles?.[0]?.name ? formatRole(user.roles[0].name) : 'Dashboard' }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-indigo-100 text-sm font-medium">Quote of the Day</p>
                                <p class="text-sm italic mt-1 max-w-xs line-clamp-2">{{ quote?.message || 'Keep working hard!' }}</p>
                            </div>
                            <div class="hidden sm:flex items-center justify-center w-12 h-12 bg-white/20 rounded-full">
                                <span class="text-xl">💡</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 space-y-8">
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Sales Today</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                                    {{ formattedSalesToday }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Current day total</p>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-xl">
                                💰
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Transactions</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                                    {{ transactions }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Today's count</p>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-xl">
                                📊
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Pending Orders</p>
                                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-2">
                                    {{ pendingOrders }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Awaiting payment</p>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center text-xl">
                                ⏱️
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Low Stock</p>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">
                                    {{ lowStock }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Items to restock</p>
                            </div>
                            <div class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-xl">
                                ⚠️
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            href="/sales/create"
                            class="relative overflow-hidden group rounded-lg p-6 bg-gradient-to-br from-blue-600 to-blue-700 text-white shadow-sm hover:shadow-lg transition-all hover:scale-105 active:scale-95"
                        >
                            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex flex-col items-center text-center gap-2">
                                <span class="text-3xl">📝</span>
                                <span class="font-semibold">New Sale</span>
                                <span class="text-xs text-blue-100">Create a new transaction</span>
                            </div>
                        </Link>

                        <Link
                            href="/customers"
                            class="relative overflow-hidden group rounded-lg p-6 bg-gradient-to-br from-green-600 to-green-700 text-white shadow-sm hover:shadow-lg transition-all hover:scale-105 active:scale-95"
                        >
                            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex flex-col items-center text-center gap-2">
                                <span class="text-3xl">👥</span>
                                <span class="font-semibold">Lookup Customer</span>
                                <span class="text-xs text-green-100">Find customer details</span>
                            </div>
                        </Link>

                        <Link
                            href="/sales"
                            class="relative overflow-hidden group rounded-lg p-6 bg-gradient-to-br from-amber-600 to-amber-700 text-white shadow-sm hover:shadow-lg transition-all hover:scale-105 active:scale-95"
                        >
                            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex flex-col items-center text-center gap-2">
                                <span class="text-3xl">↩️</span>
                                <span class="font-semibold">Process Return</span>
                                <span class="text-xs text-amber-100">Handle returns/refunds</span>
                            </div>
                        </Link>

                        <Link
                            :href="route('receipts.index')"
                            class="relative overflow-hidden group rounded-lg p-6 bg-gradient-to-br from-indigo-600 to-indigo-700 text-white shadow-sm hover:shadow-lg transition-all hover:scale-105 active:scale-95"
                        >
                            <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative flex flex-col items-center text-center gap-2">
                                <span class="text-3xl">🖨️</span>
                                <span class="font-semibold">Print Receipt</span>
                                <span class="text-xs text-indigo-100">Print or reprint receipts</span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Transactions</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Latest sales from today</p>
                    </div>

                    <div v-if="recentTransactions?.length" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr class="text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <th class="px-6 py-3">Sale ID</th>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                    <th class="px-6 py-3 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="transaction in recentTransactions"
                                    :key="transaction.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                >
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        #{{ transaction.id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ transaction.customer?.name || 'Walk-in' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900 dark:text-white">
                                        {{ formatCurrency(transaction.total) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <span :class="getStatusBadgeClass(transaction.status)">
                                            {{ formatStatus(transaction.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">
                                        {{ formatDate(transaction.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-12 text-center">
                        <p class="text-4xl mb-3">📭</p>
                        <p class="text-gray-600 dark:text-gray-400">No transactions yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Transactions will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    salesToday: Number,
    transactions: Number,
    pendingOrders: Number,
    lowStock: Number,
    recentTransactions: Array,
})

const page = usePage()
const user = page.props.auth.user
const quote = page.props.quote

const breadcrumb = [{ title: 'Cashier Dashboard', href: '/cashier/dashboard' }]

const formattedSalesToday = computed(() => {
    return props.salesToday ? formatCurrency(props.salesToday) : formatCurrency(0)
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-TZ', {
        style: 'currency',
        currency: 'TZS',
        minimumFractionDigits: 0
    }).format(value)
}

const formatDate = (value) => {
    return new Date(value).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatRole = (role) => {
    return role.charAt(0).toUpperCase() + role.slice(1) + "'s Dashboard"
}

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1)
}

const getTransactionStatus = (transaction) => {
    if (transaction.total === transaction.balance) {
        return 'unpaid'
    } else if (transaction.paid < transaction.total) {
        return 'partial'
    } else if (transaction.balance === 0) {
        return 'paid'
    }
    return 'unpaid'
}

const getStatusBadgeClass = (status) => {
    const baseClass = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border'
    const statusClasses = {
        paid: 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800',
        partial: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800',
        unpaid: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800'
    }
    return `${baseClass} ${statusClasses[status] || statusClasses.unpaid}`
}
</script>
