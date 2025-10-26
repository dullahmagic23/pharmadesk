<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stock Inventory" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
            <!-- Sticky Header -->
            <div class="sticky top-0 z-40 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <Package class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                                </div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Stock Inventory</h1>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm ml-13">Track and manage your inventory levels in real-time</p>
                        </div>
                        <Link :href="route('stocks.create')" class="flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-4 py-2.5 h-11 text-white font-medium transition-all gap-2 shadow-lg hover:shadow-xl whitespace-nowrap">
                            <PlusCircleIcon class="h-5 w-5" />
                            <span class="hidden sm:inline">Add Stock</span>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <!-- Enhanced Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Total Items -->
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all hover:border-blue-200 dark:hover:border-blue-700">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Items</p>
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <Package class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ stocks.length }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Active stock items</p>
                    </div>

                    <!-- Expired Items -->
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-900/30 p-6 hover:shadow-md transition-all hover:border-red-300 dark:hover:border-red-800">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Expired</p>
                            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <AlertCircleIcon class="h-5 w-5 text-red-600 dark:text-red-400" />
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-red-600 dark:text-red-400">{{ expiredCount }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Requires attention</p>
                    </div>

                    <!-- Low Stock -->
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-amber-200 dark:border-amber-900/30 p-6 hover:shadow-md transition-all hover:border-amber-300 dark:hover:border-amber-800">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Low Stock</p>
                            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <TrendingDownIcon class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-amber-600 dark:text-amber-400">{{ lowStockCount }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Need reordering</p>
                    </div>

                    <!-- In Stock -->
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-emerald-200 dark:border-emerald-900/30 p-6 hover:shadow-md transition-all hover:border-emerald-300 dark:hover:border-emerald-800">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">In Stock</p>
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <CheckCircleIcon class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">{{ inStockCount }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Ready to sell</p>
                    </div>
                </div>

                <!-- Enhanced Filter Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <FilterIcon class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Filters</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">Search Item</label>
                            <div class="relative">
                                <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search by item name..."
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition-all"
                                />
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">Type</label>
                            <select
                                v-model="selectedType"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition-all bg-white"
                            >
                                <option value="">All Types</option>
                                <option value="Product">Product</option>
                                <option value="Medicine">Medicine</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">Status</label>
                            <select
                                v-model="selectedStatus"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition-all bg-white"
                            >
                                <option value="">All Status</option>
                                <option value="In Stock">In Stock</option>
                                <option value="Expiring Soon">Expiring Soon</option>
                                <option value="Expired">Expired</option>
                                <option value="Low">Low Stock</option>
                            </select>
                        </div>

                        <!-- Reset Filters -->
                        <div class="flex items-end">
                            <button
                                @click="resetFilters"
                                v-if="hasActiveFilters"
                                class="w-full px-4 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium transition-colors flex items-center justify-center gap-2"
                            >
                                <RotateCcwIcon class="h-4 w-4" />
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stock Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Empty State -->
                    <div v-if="filteredStocks.length === 0" class="p-16 text-center">
                        <div class="mb-4 flex justify-center">
                            <Package class="w-20 h-20 text-gray-300 dark:text-gray-600" />
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 font-semibold text-lg">No stocks found</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">Try adjusting your filters or add a new stock item</p>
                    </div>

                    <!-- Table -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-700 border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <th class="py-4 px-6">Item</th>
                                <th class="py-4 px-6">Type</th>
                                <th class="py-4 px-6">Quantity</th>
                                <th class="py-4 px-6 text-right">Buying Price</th>
                                <th class="py-4 px-6 text-right">Retail</th>
                                <th class="py-4 px-6 text-right">Wholesale</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Expires</th>
                                <th class="py-4 px-6 text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="stock in filteredStocks" :key="stock.id" class="hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <!-- Item Name & Batch -->
                                <td class="py-4 px-6">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ stock.stockable?.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ stock.batch_number ? `Batch: ${stock.batch_number}` : 'No batch' }}</p>
                                    </div>
                                </td>

                                <!-- Type Badge -->
                                <td class="py-4 px-6">
                                    <span :class="['inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold', stock.stockable_type.includes('Product') ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300']">
                                        <span class="w-2 h-2 rounded-full" :class="stock.stockable_type.includes('Product') ? 'bg-blue-600' : 'bg-purple-600'"></span>
                                        {{ stock.stockable_type.includes('Product') ? 'Product' : 'Medicine' }}
                                    </span>
                                </td>

                                <!-- Quantity with Visual Indicator -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <span :class="[
                                            'inline-block px-3 py-1.5 rounded-lg text-xs font-bold',
                                            {'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300': isLow(stock)},
                                            {'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300': !isLow(stock)}
                                        ]">
                                            {{ formatQuantity(stock.quantity) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ stock.unit?.unit_name || '' }}</span>
                                    </div>
                                </td>

                                <!-- Prices -->
                                <td class="py-4 px-6 text-right font-mono text-sm text-gray-900 dark:text-gray-100">{{ currency(stock.buying_price) }}</td>
                                <td class="py-4 px-6 text-right font-mono text-sm text-gray-900 dark:text-gray-100">{{ currency(stock.retail_price) }}</td>
                                <td class="py-4 px-6 text-right font-mono text-sm text-gray-900 dark:text-gray-100">{{ currency(stock.wholesale_price) }}</td>

                                <!-- Status Badge -->
                                <td class="py-4 px-6">
                                    <span :class="[
                                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border',
                                        {
                                            'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-300 dark:border-red-800': stock.status === 'Expired',
                                            'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border-amber-300 dark:border-amber-800': stock.status === 'Expiring Soon',
                                            'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border-emerald-300 dark:border-emerald-800': stock.status === 'In Stock',
                                        }
                                    ]">
                                        <span :class="[
                                            'w-2 h-2 rounded-full',
                                            {
                                                'bg-red-600': stock.status === 'Expired',
                                                'bg-amber-600': stock.status === 'Expiring Soon',
                                                'bg-emerald-600': stock.status === 'In Stock',
                                            }
                                        ]"></span>
                                        {{ stock.status }}
                                    </span>
                                </td>

                                <!-- Expiration Date -->
                                <td class="py-4 px-6 text-sm text-gray-600 dark:text-gray-400">
                                    {{ stock.expiration_date ? formatDate(stock.expiration_date) : '—' }}
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link :href="route('stocks.edit', stock.id)">
                                            <Button variant="ghost" size="icon" class="h-9 w-9 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Edit stock">
                                                <PencilIcon class="h-5 w-5" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="checkIfExpired(stock)"
                                            title="Mark as Expired"
                                            variant="ghost"
                                            size="icon"
                                            class="h-9 w-9 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            @click="showConfirmModal = true; stockToConfirm = stock"
                                        >
                                            <AlertCircleIcon class="h-5 w-5" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Results Counter -->
                <div v-if="filteredStocks.length > 0" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing <span class="font-bold text-gray-900 dark:text-white">{{ filteredStocks.length }}</span> of
                        <span class="font-bold text-gray-900 dark:text-white">{{ stocks.length }}</span> stock items
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 dark:bg-gray-950/70 p-4 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 px-6 py-4 border-b border-red-200 dark:border-red-900/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <AlertCircleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Mark as Expired</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">This action cannot be undone</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 dark:text-gray-300 mb-6">
                            Are you sure you want to mark <span class="font-semibold text-gray-900 dark:text-white">{{ stockToConfirm?.stockable?.name }}</span>
                            <span class="text-gray-600 dark:text-gray-400">(Batch: {{ stockToConfirm?.batch_number || 'N/A' }})</span> as expired?
                        </p>
                        <div class="flex justify-end gap-3">
                            <Button @click="showConfirmModal = false" variant="outline" class="rounded-lg">
                                Cancel
                            </Button>
                            <Button @click="handleConfirmExpire" class="bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg transition-colors">
                                Mark as Expired
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import currency from '@/modules/currecyFormatter';
import { PencilIcon, PlusCircleIcon, SearchIcon, AlertCircleIcon, Package, CheckCircleIcon, TrendingDownIcon, FilterIcon, RotateCcwIcon } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';

interface Stock {
    id: string;
    stockable?: { name: string };
    stockable_type: string;
    buying_price: number;
    unit?: { unit_name: string };
    quantity: number;
    retail_price: number;
    wholesale_price: number;
    status: string;
    expiration_date?: string;
    batch_number?: string;
    location_id?: string;
    created_by?: string;
    updated_by?: string;
    updated_at: string;
}

const props = defineProps<{ stocks: Stock[] }>();

const searchQuery = ref('');
const selectedType = ref('');
const selectedStatus = ref('');
const showConfirmModal = ref(false);
const stockToConfirm = ref<Stock | null>(null);

const filteredStocks = computed(() => {
    if (!props.stocks) return [];
    return props.stocks.filter((stock) => {
        const matchesSearch = stock.stockable?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ?? false;
        const matchesType = !selectedType.value || stock.stockable_type.includes(selectedType.value);

        let matchesStatus = true;
        if (selectedStatus.value === 'Low') {
            matchesStatus = stock.quantity <= 10;
        } else if (selectedStatus.value) {
            matchesStatus = stock.status === selectedStatus.value;
        }

        return matchesSearch && matchesType && matchesStatus;
    });
});

const expiredCount = computed(() => {
    return props.stocks.filter(s => s.status === 'Expired').length;
});

const lowStockCount = computed(() => {
    return props.stocks.filter(s => isLow(s)).length;
});

const inStockCount = computed(() => {
    return props.stocks.length - expiredCount.value - lowStockCount.value;
});

const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedType.value || selectedStatus.value;
});

const resetFilters = () => {
    searchQuery.value = '';
    selectedType.value = '';
    selectedStatus.value = '';
};

const handleConfirmExpire = () => {
    if (stockToConfirm.value) {
        router.patch(route('stocks.expire', stockToConfirm.value.id));
        showConfirmModal.value = false;
        stockToConfirm.value = null;
    }
};

const formatQuantity = (quantity: number): string => {
    return Math.trunc(quantity).toLocaleString();
};

const formatDate = (date: string): string => new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
});

const breadcrumbs = [{ title: 'All Stocks', href: '/stocks' }];

const checkIfExpired = (stock: any) => {
    if (!stock.expiration_date) return false;
    const currentDate = new Date();
    const expirationDate = new Date(stock.expiration_date);
    return currentDate > expirationDate;
};

const isLow = (stock: any): boolean => stock.quantity <= 10;
</script>
