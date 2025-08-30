<template>
    <AppLayout :breadcrumbs="breadcrumb">
        <Head title="Cashier Dashboard" />

        <div class="container mx-auto px-4 py-8">
            <!-- Greeting Section -->
            <div class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-xl font-extrabold text-gray-900 leading-tight">
                            Welcome, {{ user.name }}
                        </h1>
                        <p class="mt-2 text-lg text-blue-700 font-medium capitalize">
                            {{ user.roles?.[0]?.name+'\'s' || 'No Role Assigned' }} Dashboard
                        </p>
                    </div>
                    <div class="text-right italic text-gray-600 max-w-sm">
                        <small class="font-semibold">Quote of the Day:</small>
                        <p class="mt-1 font-light text-sm">{{ quote.message }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="p-4 bg-white rounded-xl shadow flex flex-col items-center">
                    <span class="text-gray-500 text-sm">Sales Today</span>
                    <span class="text-2xl font-bold text-green-600">{{ formattedSalesToday }}</span>
                </div>
                <div class="p-4 bg-white rounded-xl shadow flex flex-col items-center">
                    <span class="text-gray-500 text-sm">Transactions</span>
                    <span class="text-2xl font-bold text-blue-600">{{ transactions }}</span>
                </div>
                <div class="p-4 bg-white rounded-xl shadow flex flex-col items-center">
                    <span class="text-gray-500 text-sm">Pending Orders</span>
                    <span class="text-2xl font-bold text-yellow-600">{{pendingOrders }}</span>
                </div>
                <div class="p-4 bg-white rounded-xl shadow flex flex-col items-center">
                    <span class="text-gray-500 text-sm">Low Stock</span>
                    <span class="text-2xl font-bold text-red-600">{{lowStock }}</span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <Link href="/sales/create" class="p-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold shadow text-center">
                    New Sale
                </Link>
                <Link href="/customers" class="p-4 bg-green-600 text-white rounded-xl hover:bg-green-700 font-semibold shadow text-center">
                    Lookup Customer
                </Link>
                <Link href="/sales" class="p-4 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 font-semibold shadow text-center">
                    Process Return
                </Link>
                <Link class="p-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold shadow text-center">
                    Print Receipt
                </Link>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold mb-4">Recent Transactions</h2>
                <table class="w-full table-auto text-left">
                    <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-2">Sale ID</th>
                        <th class="py-2">Customer</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="transaction in recentTransactions" :key="transaction.id" class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2">{{ transaction.id }}</td>
                        <td class="py-2">{{ transaction.customer?.name || 'Walk-in' }}</td>
                        <td class="py-2">{{ formatCurrency(transaction.total) }}</td>
                        <td class="py-2 px-4 text-center">
                                    <span :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-semibold',
                                        {
                                            'bg-green-100 text-green-800': transaction.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': transaction.status === 'partial',
                                            'bg-red-100 text-red-800': transaction.status === 'unpaid',
                                        },
                                    ]">
                                        {{ transaction.status === 'paid' ? 'Paid' : transaction.status === 'partial' ? 'Partial' : 'Unpaid' }}
                                    </span>
                        </td>
                        <td class="py-2">{{ formatDate(transaction.created_at) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    salesToday: Number,
    transactions: Number,
    pendingOrders: Number,
    lowStock: Number,
    recentTransactions: Array,
});

const page = usePage();
const user = page.props.auth.user;
const quote = page.props.quote;

const breadcrumb = [{ title: 'Cashier Dashboard', href: '/cashier/dashboard' }];

// Using a computed property for formatted sales
const formattedSalesToday = computed(() => {
    return props.salesToday ? formatCurrency(props.salesToday) : '0.00';
});

// Helper functions for formatting
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-TZ', { style: 'currency', currency: 'TZS' }).format(value);
};

const formatDate = (value) => {
    return new Date(value).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>
