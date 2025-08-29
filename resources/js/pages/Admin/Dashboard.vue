<template>
    <AppLayout :breadcrumbs="breadcrumb">
        <Head title="Admin Dashboard"/>
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">Welcome, {{ page.props.auth.user.name }}</h1>
                        <p class="mt-2 text-lg text-blue-700 font-medium capitalize">{{ page.props.auth.user.roles?.[0]?.name || 'No Role Assigned' }} Dashboard</p>
                    </div>
                    <div class="text-right italic text-gray-600 max-w-sm">
                        <small class="font-semibold">Quote of the Day:</small>
                        <p class="mt-1 font-light text-sm">{{ page.props.quote.message }}</p>
                    </div>
                </div>
            </div>

<!--            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">-->
<!--                <Card-->
<!--                    title="Total Doctors"-->
<!--                    :value="stats.doctors"-->
<!--                    icon="stethoscope"-->
<!--                    class="bg-blue-600 text-white"-->
<!--                />-->
<!--                <Card-->
<!--                    title="Total Pharmacists"-->
<!--                    :value="stats.pharmacists"-->
<!--                    icon="pill"-->
<!--                    class="bg-green-600 text-white"-->
<!--                />-->
<!--                <Card-->
<!--                    title="Total Patients"-->
<!--                    :value="stats.patients"-->
<!--                    icon="users"-->
<!--                    class="bg-yellow-600 text-white"-->
<!--                />-->
<!--            </div>-->

            <div
                v-if="expiredStocks.length || expiringStocks.length || lowStocks.length"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
            >
                <div v-if="expiredStocks.length" class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 shadow-md transition-shadow hover:shadow-lg">
                    <h2 class="flex items-center text-xl font-bold text-red-800 mb-3">
                        <span class="mr-2 text-2xl">⚠️</span> Expired Stock Batches ({{ expiredStocks.length }})
                    </h2>
                    <ul class="text-sm text-red-700 space-y-2">
                        <li v-for="stock in expiredStocks" :key="stock.id" class="flex items-start">
                            <span class="text-lg leading-none mr-2">•</span>
                            <div>
                                <span class="font-semibold">{{ stock.stockable.name }}</span>
                                <span class="text-xs italic">(Batch: {{ stock.batch_number }})</span>
                                <br> expired on <span class="font-mono font-bold">{{ stock.expiration_date }}</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-if="expiringStocks.length" class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-6 shadow-md transition-shadow hover:shadow-lg">
                    <h2 class="flex items-center text-xl font-bold text-yellow-800 mb-3">
                        <span class="mr-2 text-2xl">📅</span> Expiring Soon ({{ expiringStocks.length }})
                    </h2>
                    <ul class="text-sm text-yellow-700 space-y-2">
                        <li v-for="stock in expiringStocks" :key="stock.id" class="flex items-start">
                            <span class="text-lg leading-none mr-2">•</span>
                            <div>
                                <span class="font-semibold">{{ stock.stockable.name }}</span>
                                <span class="text-xs italic">(Batch: {{ stock.batch_number }})</span>
                                <br> expires on <span class="font-mono font-bold">{{ stock.expiration_date }}</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-if="lowStocks.length" class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-6 shadow-md transition-shadow hover:shadow-lg">
                    <h2 class="flex items-center text-xl font-bold text-orange-800 mb-3">
                        <span class="mr-2 text-2xl">📦</span> Low Stock Batches ({{ lowStocks.length }})
                    </h2>
                    <ul class="text-sm text-orange-700 space-y-2">
                        <li v-for="stock in lowStocks" :key="stock.id" class="flex items-start">
                            <span class="text-lg leading-none mr-2">•</span>
                            <div>
                                <span class="font-semibold">{{ stock.stockable.name }}</span>
                                <span class="text-xs italic">(Batch: {{ stock.batch_number }})</span>
                                <br> has only <span class="font-mono font-bold">{{ stock.quantity }}</span> left
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
                        <li
                            v-for="(item, index) in activity"
                            :key="index"
                            class="flex items-start text-sm"
                        >
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
import Card from '@/components/Dashboard/Card.vue'
import { usePage, Link, Head } from '@inertiajs/vue3'
import { Users, FileBarChart2, UserPlus, ClipboardList } from 'lucide-vue-next'

const page = usePage()

const stats = {
    doctors: page.props.stats.doctors,
    pharmacists: page.props.stats.pharmacists,
    patients: page.props.stats.patients,
}

const activity = page.props.activity
const expiredStocks = page.props.expiredStocks
const expiringStocks = page.props.expiringStocks
const lowStocks = page.props.lowStocks

const quickLinks = [
    { title: 'Manage Users', href: '/users', icon: Users },
    { title: 'Add New Doctor', href: '/doctors/create', icon: UserPlus },
    { title: 'System Reports', href: '/reports', icon: FileBarChart2 },
    { title: 'Audit Logs', href: '/admin/logs', icon: ClipboardList },
]

const breadcrumb = [
    { title: 'Admin Dashboard', href: '/admin/dashboard' }
]
</script>
