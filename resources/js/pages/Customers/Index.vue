<script lang="ts" setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { computed, ref } from 'vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { SearchIcon, UserPlusIcon, MailIcon, PhoneIcon, EyeIcon, PencilIcon, TrashIcon } from 'lucide-vue-next'

interface Customer {
    id: number
    name: string
    phone: string
    email: string
    created_at?: string
}

const props = defineProps({ customers: Array<Customer> })

const search = ref('')
const sortBy = ref('name')
const sortOrder = ref('asc')

const filteredCustomers = computed(() => {
    let filtered = props.customers || []

    if (search.value.length > 0) {
        filtered = filtered.filter(customer =>
            customer.name.toLowerCase().includes(search.value.toLowerCase()) ||
            customer.email.toLowerCase().includes(search.value.toLowerCase()) ||
            customer.phone.includes(search.value)
        )
    }

    // Sort
    filtered.sort((a, b) => {
        let aVal, bVal
        switch (sortBy.value) {
            case 'phone':
                aVal = a.phone
                bVal = b.phone
                break
            case 'email':
                aVal = a.email.toLowerCase()
                bVal = b.email.toLowerCase()
                break
            case 'date':
                aVal = new Date(a.created_at || 0)
                bVal = new Date(b.created_at || 0)
                break
            default:
                aVal = a.name.toLowerCase()
                bVal = b.name.toLowerCase()
        }

        if (sortOrder.value === 'asc') {
            return aVal > bVal ? 1 : aVal < bVal ? -1 : 0
        } else {
            return aVal < bVal ? 1 : aVal > bVal ? -1 : 0
        }
    })

    return filtered
})

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

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
            <!-- Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Customers</h1>
                    <p class="text-slate-600 mt-1">Manage your customer database</p>
                </div>
                <Link
                    :href="route('customers.create')"
                    class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition font-medium"
                >
                    <UserPlusIcon class="w-5 h-5" />
                    Add Customer
                </Link>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <p class="text-sm font-medium text-slate-600 mb-2">Total Customers</p>
                    <p class="text-3xl font-bold text-slate-900">{{ props.customers?.length || 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <p class="text-sm font-medium text-slate-600 mb-2">Search Results</p>
                    <p class="text-3xl font-bold text-blue-600">{{ filteredCustomers.length }}</p>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="relative">
                        <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Search by name, email, or phone..."
                            class="pl-10 w-full"
                        />
                    </div>
                    <select
                        v-model="sortBy"
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                    >
                        <option value="name">Sort by Name</option>
                        <option value="email">Sort by Email</option>
                        <option value="phone">Sort by Phone</option>
                        <option value="date">Sort by Date Added</option>
                    </select>
                    <button
                        @click="sortOrder = sortOrder === 'asc' ? 'desc' : 'asc'"
                        class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 transition font-medium text-slate-700"
                    >
                        {{ sortOrder === 'asc' ? '↑ Ascending' : '↓ Descending' }}
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('name')">
                                    Name {{ getSortIcon('name') }}
                                </th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('phone')">
                                    Phone {{ getSortIcon('phone') }}
                                </th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 transition" @click="toggleSort('email')">
                                    Email {{ getSortIcon('email') }}
                                </th>
                                <th class="px-6 py-4 text-center font-semibold text-slate-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr
                                v-for="customer in filteredCustomers"
                                :key="customer.id"
                                class="hover:bg-slate-50 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900">{{ customer.name }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <PhoneIcon class="w-4 h-4 text-slate-400" />
                                        {{ customer.phone }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <MailIcon class="w-4 h-4 text-slate-400" />
                                        <a :href="`mailto:${customer.email}`" class="hover:text-blue-600 transition">
                                            {{ customer.email }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link
                                            :href="`/customers/${customer.id}`"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-slate-700 hover:bg-slate-100 rounded-lg transition text-sm font-medium"
                                        >
                                            <EyeIcon class="w-4 h-4" />
                                            <span class="hidden sm:inline">View</span>
                                        </Link>
                                        <Link
                                            :href="`/customers/${customer.id}/edit`"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-slate-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition text-sm font-medium"
                                        >
                                            <PencilIcon class="w-4 h-4" />
                                            <span class="hidden sm:inline">Edit</span>
                                        </Link>
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-slate-700 hover:bg-red-50 hover:text-red-600 rounded-lg transition text-sm font-medium"
                                        >
                                            <TrashIcon class="w-4 h-4" />
                                            <span class="hidden sm:inline">Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredCustomers.length === 0" class="px-6 py-16 text-center">
                    <UserPlusIcon class="w-12 h-12 text-slate-300 mx-auto mb-4" />
                    <p class="text-slate-500 text-lg">{{ search ? 'No customers found' : 'No customers yet' }}</p>
                    <p v-if="!search" class="text-slate-400 text-sm mt-2">Get started by adding your first customer</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
