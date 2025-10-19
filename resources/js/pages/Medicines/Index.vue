<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { Search, Filter, Plus, Pill, Edit2, Eye, ChevronDown, X } from 'lucide-vue-next'

const {medicines} = usePage().props

// Filter criteria
const filterQuery = ref('')
const filterCategory = ref('')
const filterBrand = ref('')
const showFilters = ref(false)

// Get unique categories and brands
const categories = computed(() => {
    const cats = medicines
        .map(m => m.category?.name)
        .filter((v, i, a) => v && a.indexOf(v) === i)
    return cats.sort()
})

const brands = computed(() => {
    const brandsList = medicines
        .map(m => m.brand)
        .filter((v, i, a) => v && a.indexOf(v) === i)
    return brandsList.sort()
})

// Computed filtered medicines based on filterQuery, filterCategory, and filterBrand
const filteredMedicines = computed(() => {
    return medicines.filter(medicine => {
        const matchesName = medicine.name.toLowerCase().includes(filterQuery.value.toLowerCase())
        const matchesCategory = filterCategory.value ? medicine.category?.name?.toLowerCase() === filterCategory.value.toLowerCase() : true
        const matchesBrand = filterBrand.value ? medicine.brand?.toLowerCase() === filterBrand.value.toLowerCase() : true
        return matchesName && matchesCategory && matchesBrand
    })
})

// Check if filters are active
const hasActiveFilters = computed(() => {
    return filterQuery.value || filterCategory.value || filterBrand.value
})

// Clear all filters
const clearFilters = () => {
    filterQuery.value = ''
    filterCategory.value = ''
    filterBrand.value = ''
}

const breadcrumbs = [
    { title: 'Medicines', href: '/medicines' },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Medicines</h1>
                            <p class="text-gray-500 text-sm mt-1">Manage your medicine inventory</p>
                        </div>
                        <Link href="/medicines/create">
                            <Button class="bg-blue-600 hover:bg-blue-700 rounded-lg gap-2 h-11">
                                <Plus class="w-5 h-5" />
                                <span class="hidden sm:inline">Add Medicine</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Medicines</p>
                        <p class="text-3xl font-bold text-gray-900">{{ medicines.length }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Categories</p>
                        <p class="text-3xl font-bold text-blue-600">{{ categories.length }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Brands</p>
                        <p class="text-3xl font-bold text-purple-600">{{ brands.length }}</p>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
                    <div
                        class="flex items-center justify-between p-4 md:p-6 cursor-pointer border-b border-gray-200 hover:bg-gray-50 transition-colors"
                        @click="showFilters = !showFilters"
                    >
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <Filter class="w-5 h-5 text-blue-600" />
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900">Search & Filter</h2>
                                <p v-if="hasActiveFilters" class="text-xs text-blue-600 font-medium">{{ filteredMedicines.length }} results</p>
                            </div>
                        </div>
                        <ChevronDown :class="['w-5 h-5 text-gray-400 transition-transform', showFilters && 'rotate-180']" />
                    </div>

                    <div v-show="showFilters" class="p-4 md:p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-t border-gray-200 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search by Name -->
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-2">Medicine Name</label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input
                                        v-model="filterQuery"
                                        type="text"
                                        placeholder="Search by name..."
                                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition"
                                    />
                                </div>
                            </div>

                            <!-- Filter by Category -->
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-2">Category</label>
                                <select
                                    v-model="filterCategory"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>

                            <!-- Filter by Brand -->
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-2">Brand</label>
                                <select
                                    v-model="filterBrand"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition"
                                >
                                    <option value="">All Brands</option>
                                    <option v-for="brand in brands" :key="brand" :value="brand">
                                        {{ brand }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Clear Filters Button -->
                        <div v-if="hasActiveFilters" class="flex justify-end">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="clearFilters"
                                class="gap-2"
                            >
                                <X class="w-4 h-4" />
                                Clear Filters
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Medicines Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div v-if="filteredMedicines.length === 0" class="p-12 text-center">
                        <Pill class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <p class="text-gray-600 font-medium text-lg">No medicines found</p>
                        <p class="text-gray-500 text-sm mt-1">Try adjusting your filters</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <th class="py-4 px-6">Name</th>
                                <th class="py-4 px-6">Brand</th>
                                <th class="py-4 px-6">Category</th>
                                <th class="py-4 px-6">Units</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="medicine in filteredMedicines"
                                :key="medicine.id"
                                class="hover:bg-blue-50 transition-colors group"
                            >
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <Pill class="w-5 h-5 text-blue-600" />
                                        </div>
                                        <p class="font-semibold text-gray-900">{{ medicine.name }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                        <span class="inline-block px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm font-medium">
                                            {{ medicine.brand }}
                                        </span>
                                </td>
                                <td class="py-4 px-6">
                                        <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium">
                                            {{ medicine.category?.name || 'Uncategorized' }}
                                        </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div v-if="medicine?.units && medicine.units.length > 0" class="flex flex-wrap gap-1">
                                            <span
                                                v-for="unit in medicine.units"
                                                :key="unit.id"
                                                class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-700 font-medium"
                                            >
                                                {{ unit?.unit_name }}
                                            </span>
                                    </div>
                                    <span v-else class="text-gray-400 text-sm italic">No units</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link :href="`/medicines/${medicine.id}/edit`">
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="h-9 w-9 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                                                title="Edit medicine"
                                            >
                                                <Edit2 class="w-5 h-5" />
                                            </Button>
                                        </Link>
                                        <Link :href="`/medicines/${medicine.id}`">
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="h-9 w-9 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg"
                                                title="View details"
                                            >
                                                <Eye class="w-5 h-5" />
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Results Counter -->
                <div v-if="filteredMedicines.length > 0" class="mt-6 text-sm text-gray-600">
                    Showing <span class="font-bold text-gray-900">{{ filteredMedicines.length }}</span> of
                    <span class="font-bold text-gray-900">{{ medicines.length }}</span> medicines
                </div>
            </div>
        </div>
    </AppLayout>
</template>
