<template>
    <AppLayout :breadcrumbs="breadcrumb">
        <Head title="Admin Dashboard"/>
        <div class="container mx-auto p-6">
            <!-- Welcome Section -->
            <div class="text-left mb-6 bg-gray-50 p-6 rounded-2xl shadow-md">
                <h1 class="text-4xl font-extrabold text-gray-800">Welcome, {{ page.props.auth.user.name }}</h1>
                <h4 class="text-lg text-gray-600 mt-2"> {{ page.props.auth.user.email }}</h4>
                <p class="text-sm text-gray-500 mt-1 capitalize font-black">{{ page.props.auth.user.roles?.[0]?.name || 'No role assigned' }}</p>
                <div class="flex justify-end">
                    <small class="italic"><strong>Quote of Today:</strong> {{ page.props.quote.message }}</small>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <Card 
                    title="Total Doctors" 
                    :value="stats.doctors" 
                    icon="stethoscope" 
                    class="bg-blue-800 text-blue-100 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
                />
                <Card 
                    title="Total Pharmacists" 
                    :value="stats.pharmacists" 
                    icon="pill" 
                    class="bg-green-800 text-green-100 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
                />
                <Card 
                    title="Total Patients" 
                    :value="stats.patients" 
                    icon="users" 
                    class="bg-yellow-800 text-yellow-100 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
                />
            </div>

            <!-- Quick Access Section -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-6 shadow-md mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Quick Access</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <Link 
                        v-for="link in quickLinks" 
                        :key="link.title" 
                        :href="link.href"
                        class="flex flex-col items-center justify-center px-4 py-6 rounded-lg bg-white hover:bg-blue-50 text-gray-700 font-medium shadow-md hover:shadow-lg transition-all duration-300"
                    >
                        <component :is="link.icon" class="w-8 h-8 mb-2 text-blue-500" />
                        <span class="text-sm text-center">{{ link.title }}</span>
                    </Link>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Activity</h2>
                <ul class="text-gray-700 space-y-2">
                    <li 
                        v-for="(item, index) in activity" 
                        :key="index" 
                        class="flex items-center space-x-2"
                    >
                        <span class="text-blue-500 font-medium">•</span>
                        <span>{{ item }}</span>
                    </li>
                </ul>
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

const quickLinks = [
    { title: 'Manage Users', href: '/users', icon: Users },
    { title: 'Add New Doctor', href: '/doctors/create', icon: UserPlus },
    { title: 'System Reports', href: '/reports', icon: FileBarChart2 },
    { title: 'Audit Logs', href: '/admin/logs', icon: ClipboardList },
]

const breadcrumb = [
    {title:'Admin Dashboard', href:'/admin/dashboard'}
]
</script>
