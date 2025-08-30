<template>
    <AppLayout :breadcrumbs="breadcrumb">
        <Head title="Admin Dashboard" />
        <div class="container mx-auto px-4 py-8">
            <!-- Greeting Section -->
            <div class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-xl font-extrabold text-gray-900 leading-tight">
                            Welcome, {{ user.name }}
                        </h1>
                        <p class="mt-2 text-lg text-blue-700 font-medium capitalize">
                            {{ user.roles?.[0]?.name || 'No Role Assigned' }} Dashboard
                        </p>
                    </div>
                    <div class="text-right italic text-gray-600 max-w-sm">
                        <small class="font-semibold">Quote of the Day:</small>
                        <p class="mt-1 font-light text-sm">{{ page.props.quote.message }}</p>
                    </div>
                </div>
            </div>

            <!-- Stock Alerts -->
            <div
                v-if="expiredStocks.length || expiringStocks.length || lowStocks.length"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
            >
                <!-- Expired -->
                <div v-if="expiredStocks.length" class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 shadow-md">
                    <h2 class="flex items-center text-xl font-bold text-red-800 mb-3">
                        <span class="mr-2 text-2xl">⚠️</span> Expired Stock Batches ({{ expiredStocks.length }})
                    </h2>
                    <ul class="text-sm text-red-700 space-y-2">
                        <li v-for="stock in expiredStocks" :key="stock.id" class="flex items-start">
                            <span class="text-lg mr-2">•</span>
                            <div>
                                <span class="font-semibold">{{ stock.stockable.name }}</span>
                                <small class="italic text-xs">(Batch: {{ stock.batch_number }})</small>
                                <br> expired on <span class="font-mono font-bold">{{ stock.expiration_date }}</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Expiring -->
                <div v-if="expiringStocks.length" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6 shadow-md">
                    <h2 class="flex items-center text-xl font-bold text-yellow-800 mb-3">
                        <span class="mr-2 text-2xl">📅</span> Expiring Soon ({{ expiringStocks.length }})
                    </h2>
                    <ul class="text-sm text-yellow-700 space-y-2">
                        <li v-for="stock in expiringStocks" :key="stock.id" class="flex items-start">
                            <span class="text-lg mr-2">•</span>
                            <div>
                                <span class="font-semibold">{{ stock.stockable.name }}</span>
                                <small class="italic text-xs">(Batch: {{ stock.batch_number }})</small>
                                <br> expires on <span class="font-mono font-bold">{{ stock.expiration_date }}</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Low Stock -->
                <div v-if="lowStocks.length" class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6 shadow-md">
                    <h2 class="flex items-center text-xl font-bold text-orange-800 mb-3">
                        <span class="mr-2 text-2xl">📦</span> Low Stock Batches ({{ lowStocks.length }})
                    </h2>
                    <ul class="text-sm text-orange-700 space-y-2">
                        <li v-for="stock in lowStocks" :key="stock.id" class="flex items-start">
                            <span class="text-lg mr-2">•</span>
                            <div>
                                <span class="font-semibold">{{ stock.stockable.name }}</span>
                                <small class="italic text-xs">(Batch: {{ stock.batch_number }})</small>
                                <br> has only <span class="font-mono font-bold">{{ stock.quantity }}</span> left
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Sales Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" v-if="sales">
                <!-- Line Chart -->
                <div class="bg-white rounded-3xl p-6 shadow-lg">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-5 border-b pb-3">Sales Over Time</h2>
                    <div class="w-full h-80 lg:h-96">
                        <LineChart :key="'sales-line-chart'" :data="salesLineData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="bg-white rounded-3xl p-6 shadow-lg">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-5 border-b pb-3">Top Products</h2>
                    <div class="w-full h-80 lg:h-96">
                        <BarChart :key="'sales-bar-chart'" :data="salesBarData" :options="chartOptions" />
                    </div>
                </div>
            </div>

            <!-- Quick Access + Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 mt-4">
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-lg">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-5 border-b pb-3">Quick Access</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <Link
                            v-for="link in quickLinks"
                            :key="link.title"
                            :href="link.href"
                            class="flex flex-col items-center justify-center p-4 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-700 font-medium shadow transition-all duration-300 transform hover:scale-105"
                        >
                            <component :is="link.icon" class="w-10 h-10 mb-2 text-blue-600" />
                            <span class="text-sm text-center">{{ link.title }}</span>
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-1 bg-white rounded-3xl p-6 shadow-lg">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-5 border-b pb-3">Recent Activity</h2>
                    <ul class="text-gray-700 space-y-3">
                        <li v-for="(item, index) in activity" :key="index" class="flex items-start text-sm">
                            <span class="text-blue-500 font-bold text-lg leading-none mr-2">•</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </div>


        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { Users, FileBarChart2, UserPlus, ClipboardList } from 'lucide-vue-next'
import { computed } from 'vue'

import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, BarElement, CategoryScale, LinearScale, PointElement } from 'chart.js'
import { Line as LineChart, Bar as BarChart } from 'vue-chartjs'

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, LineElement, BarElement, CategoryScale, LinearScale, PointElement)

// Props
const props = defineProps({
    stats: Object,
    activity: Array,
    user: Object,
    expiredStocks: Array,
    expiringStocks: Array,
    lowStocks: Array,
    sales: Object,
})

const page = usePage() // For quote

// Computed chart data
const salesLineData = computed(() => {
    if (!props.sales || !props.sales.labels || !props.sales.values) {
        return { labels: [], datasets: [] }
    }
    return {
        labels: props.sales.labels,
        datasets: [
            {
                label: 'Sales',
                data: props.sales.values,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.2)',
                tension: 0.4,
                fill: true,
            },
        ],
    }
})

const salesBarData = computed(() => {
    if (!props.sales || !props.sales.topProducts || !props.sales.topProducts.labels || !props.sales.topProducts.values) {
        return { labels: [], datasets: [] }
    }
    return {
        labels: props.sales.topProducts.labels,
        datasets: [
            {
                label: 'Units Sold',
                data: props.sales.topProducts.values,
                backgroundColor: '#6366f1',
            },
        ],
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: true } },
    scales: { y: { beginAtZero: true } },
}

const quickLinks = [
    { title: 'Manage Users', href: '/users', icon: Users },
    { title: 'Add New Doctor', href: '/doctors/create', icon: UserPlus },
    { title: 'System Reports', href: '/reports', icon: FileBarChart2 },
    { title: 'Audit Logs', href: '/users/activity-logs', icon: ClipboardList },
]

const breadcrumb = [{ title: 'Admin Dashboard', href: '/admin/dashboard' }]
</script>
