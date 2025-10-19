<script setup>
import { ref, computed } from 'vue'
import { Head, router, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import FlashMessage from '@/components/FlashMessage.vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Plus, Search, Edit2, Eye, Trash2, Package, Filter, X, ChevronDown } from 'lucide-vue-next'

const props = defineProps({
    products: Array,
})

const search = ref('')
const showFilters = ref(false)
const filterCategory = ref('')

// Get unique categories
const categories = computed(() => {
    const cats = props.products
        .map(p => p.category)
        .filter((v, i, a) => v && a.indexOf(v) === i)
    return cats.sort()
})

const filteredProducts = computed(() => {
    return props.products.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(search.value.toLowerCase()) ||
            p.category.toLowerCase().includes(search.value.toLowerCase())
        const matchesCategory = filterCategory.value ? p.category === filterCategory.value : true
        return matchesSearch && matchesCategory
    })
})

const hasActiveFilters = computed(() => {
    return search.value || filterCategory.value
})

const clearFilters = () => {
    search.value = ''
    filterCategory.value = ''
}

const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('products.destroy', id))
    }
}

const breadcrumbs = [
    { title: 'Products', href: '/products' },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Products" />
        <FlashMessage />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Products</h1>
                            <p class="text-gray-500 text-sm mt-1">Manage your product catalog</p>
                        </div>
                        <Button
                            @click="$inertia.visit(route('products.create'))"
                            class="bg-blue-600 hover:bg-blue-700 rounded-lg gap-2 h-11"
                        >
                            <Plus class="w-5 h-5" />
                            <span class="hidden sm:inline">Add Product</span>
                        </Button>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900">{{ products.length }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Categories</p>
                        <p class="text-3xl font-bold text-blue-600">{{ categories.length }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Visible Products</p>
                        <p class="text-3xl font-bold text-purple-600">{{ filteredProducts.length }}</p>
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
                                <Search class="w-5 h-5 text-blue-600" />
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900">Search & Filter</h2>
                                <p v-if="hasActiveFilters" class="text-xs text-blue-600 font-medium">{{ filteredProducts.length }} results</p>
                            </div>
                        </div>
                        <ChevronDown :class="['w-5 h-5 text-gray-400 transition-transform', showFilters && 'rotate-180']" />
                    </div>

                    <div v-show="showFilters" class="p-4 md:p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-t border-gray-200 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Search by Name -->
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-2">Product Name</label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <Input
                                        v-model="search"
                                        placeholder="Search by name or category..."
                                        class="pl-9 h-10 rounded-lg border-gray-200"
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

                <!-- Products Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div v-if="filteredProducts.length === 0" class="p-12 text-center">
                        <Package class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <p class="text-gray-600 font-medium text-lg">No products found</p>
                        <p class="text-gray-500 text-sm mt-1">Try adjusting your filters</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <th class="py-4 px-6">#</th>
                                <th class="py-4 px-6">Name</th>
                                <th class="py-4 px-6">Category</th>
                                <th class="py-4 px-6">Unit</th>
                                <th class="py-4 px-6">Description</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="(product, index) in filteredProducts"
                                :key="product.id"
                                class="hover:bg-blue-50 transition-colors group"
                            >
                                <td class="py-4 px-6 text-gray-600 font-medium">{{ index + 1 }}</td>
                                <td class="py-4 px-6">
                                    <p class="font-semibold text-gray-900">{{ product.name }}</p>
                                </td>
                                <td class="py-4 px-6">
                    <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium">
                      {{ product.category }}
                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-600">{{ product.unit || '—' }}</td>
                                <td class="py-4 px-6 text-gray-600 truncate max-w-xs">{{ product.description || '—' }}</td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            @click="$inertia.visit(route('products.edit', product.id))"
                                            class="h-9 w-9 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                                            title="Edit product"
                                        >
                                            <Edit2 class="w-5 h-5" />
                                        </Button>
                                        <Link :href="`/products/${product.id}`">
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="h-9 w-9 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg"
                                                title="View details"
                                            >
                                                <Eye class="w-5 h-5" />
                                            </Button>
                                        </Link>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            @click="deleteProduct(product.id)"
                                            class="h-9 w-9 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg"
                                            title="Delete product"
                                        >
                                            <Trash2 class="w-5 h-5" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Results Counter -->
                <div v-if="filteredProducts.length > 0" class="mt-6 text-sm text-gray-600">
                    Showing <span class="font-bold text-gray-900">{{ filteredProducts.length }}</span> of
                    <span class="font-bold text-gray-900">{{ products.length }}</span> products
                </div>
            </div>
        </div>
    </AppLayout>
</template>
