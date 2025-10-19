<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stocks" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Stock Inventory</h1>
                            <p class="text-gray-500 text-sm mt-1">Manage your inventory levels and pricing</p>
                        </div>
                        <Link :href="route('stocks.create')" class="flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 h-11 text-white font-medium transition-colors gap-2">
                            <PlusCircleIcon class="h-5 w-5" />
                            <span class="hidden sm:inline">Add Stock</span>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Items</p>
                        <p class="text-3xl font-bold text-gray-900">{{ stocks.length }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Expired</p>
                        <p class="text-3xl font-bold text-red-600">{{ expiredCount }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-amber-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Low Stock</p>
                        <p class="text-3xl font-bold text-amber-600">{{ lowStockCount }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">In Stock</p>
                        <p class="text-3xl font-bold text-green-600">{{ filteredStocks.length - expiredCount - lowStockCount }}</p>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-2">Search Item</label>
                            <div class="relative">
                                <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search by item name..."
                                    class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                />
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-2">Type</label>
                            <select
                                v-model="selectedType"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-white"
                            >
                                <option value="">All Types</option>
                                <option value="Product">Product</option>
                                <option value="Medicine">Medicine</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-2">Status</label>
                            <select
                                v-model="selectedStatus"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-white"
                            >
                                <option value="">All Status</option>
                                <option value="In Stock">In Stock</option>
                                <option value="Expiring Soon">Expiring Soon</option>
                                <option value="Expired">Expired</option>
                                <option value="Low">Low Stock</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stock Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div v-if="filteredStocks.length === 0" class="p-12 text-center">
                        <Package class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <p class="text-gray-600 font-medium text-lg">No stocks found</p>
                        <p class="text-gray-500 text-sm mt-1">Try adjusting your filters</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <th class="py-4 px-6">Item</th>
                                <th class="py-4 px-6">Type</th>
                                <th class="py-4 px-6">Quantity</th>
                                <th class="py-4 px-6 text-right">Buying Price</th>
                                <th class="py-4 px-6 text-right">Retail</th>
                                <th class="py-4 px-6 text-right">Wholesale</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Expiration</th>
                                <th class="py-4 px-6 text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr v-for="stock in filteredStocks" :key="stock.id" class="hover:bg-blue-50 transition-colors group">
                                <td class="py-4 px-6">
                                    <p class="font-semibold text-gray-900">{{ stock.stockable?.name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Batch: {{ stock.batch_number || '—' }}</p>
                                </td>
                                <td class="py-4 px-6">
                                        <span :class="['inline-block px-2 py-1 rounded-full text-xs font-medium', stock.stockable_type.includes('Product') ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700']">
                                            {{ stock.stockable_type.includes('Product') ? 'Product' : 'Medicine' }}
                                        </span>
                                </td>
                                <td class="py-4 px-6">
                                        <span :class="[
                                            'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                                            {'bg-red-100 text-red-800': isLow(stock)},
                                            {'bg-green-100 text-green-800': !isLow(stock)}
                                        ]">
                                            {{ formatQuantity(stock.quantity) }} {{ stock.unit?.unit_name || '' }}
                                        </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900">{{ currency(stock.buying_price) }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900">{{ currency(stock.retail_price) }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900">{{ currency(stock.wholesale_price) }}</td>
                                <td class="py-4 px-6">
                                        <span :class="[
                                            'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                                            {'bg-red-100 text-red-800 border border-red-300': stock.status === 'Expired'},
                                            {'bg-amber-100 text-amber-800 border border-amber-300': stock.status === 'Expiring Soon'},
                                            {'bg-green-100 text-green-800 border border-green-300': stock.status === 'In Stock'},
                                        ]">
                                            {{ stock.status }}
                                        </span>
                                </td>
                                <td class="py-4 px-6 text-gray-600">{{ stock.expiration_date ? formatDate(stock.expiration_date) : '—' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link :href="route('stocks.edit', stock.id)">
                                            <Button variant="ghost" size="icon" class="h-9 w-9 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit stock">
                                                <PencilIcon class="h-5 w-5" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="checkIfExpired(stock)"
                                            title="Mark as Expired"
                                            variant="ghost"
                                            size="icon"
                                            class="h-9 w-9 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg"
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
                <div v-if="filteredStocks.length > 0" class="mt-6 text-sm text-gray-600">
                    Showing <span class="font-bold text-gray-900">{{ filteredStocks.length }}</span> of
                    <span class="font-bold text-gray-900">{{ stocks.length }}</span> stock items
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-sm">
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-bold text-gray-900">Mark as Expired</h3>
                        <p class="text-sm text-gray-600 mt-1">This action cannot be undone</p>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-6">
                            Are you sure you want to mark <span class="font-semibold">{{ stockToConfirm?.stockable?.name }}</span> (Batch: {{ stockToConfirm?.batch_number }}) as expired?
                        </p>
                        <div class="flex justify-end gap-3">
                            <Button @click="showConfirmModal = false" variant="outline" class="rounded-lg">
                                Cancel
                            </Button>
                            <Button @click="handleConfirmExpire" class="bg-red-600 hover:bg-red-700 rounded-lg">
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
import { PencilIcon, PlusCircleIcon, SearchIcon, AlertCircleIcon, Package } from 'lucide-vue-next';
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

const handleConfirmExpire = () => {
    if (stockToConfirm.value) {
        router.patch(route('stocks.expire', stockToConfirm.value.id));
        showConfirmModal.value = false;
        stockToConfirm.value = null;
    }
}

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
