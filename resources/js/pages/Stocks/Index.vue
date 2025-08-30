<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stocks" />

        <div class="container mx-auto px-4 py-8">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Stock Inventory</h1>
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
                    <div class="relative flex-grow">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search by item name..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition-colors"
                        />
                        <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                    </div>
                    <select
                        v-model="selectedType"
                        class="w-full md:w-auto px-4 py-2 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 transition-colors bg-white appearance-none pr-8"
                    >
                        <option value="">All Types</option>
                        <option value="Product">Product</option>
                        <option value="Medicine">Medicine</option>
                    </select>
                    <Link :href="route('stocks.create')" class="w-full md:w-auto flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white font-medium transition-colors">
                        <PlusCircleIcon class="mr-2 h-5 w-5" />
                        Add New Stock
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead class="bg-gray-50">
                        <tr class="text-gray-600 uppercase text-xs">
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Item</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Type</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Quantity</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Retail Price</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Wholesale Price</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Status</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Expiration Date</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Batch Number</th>
                            <th class="py-1.5 px-3 text-left font-semibold tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="stock in filteredStocks" :key="stock.id" class="hover:bg-gray-50 transition-colors">
                            <td class="py-1.5 px-3 font-medium text-gray-800">{{ stock.stockable?.name }}</td>
                            <td class="py-1.5 px-3 text-gray-600">
                                {{ stock.stockable_type.includes('Product') ? 'Product' : 'Medicine' }}
                            </td>
                            <td class="py-1.5 px-3">
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full text-xs font-semibold',
                                        {'bg-red-100 text-red-800': isLow(stock)},
                                        {'bg-green-100 text-green-800': !isLow(stock)}
                                    ]">
                                        {{ formatQuantity(stock.quantity) }} {{ stock.unit?.unit_name || '' }}
                                    </span>
                            </td>
                            <td class="py-1.5 px-3 font-mono">{{ currency(stock.retail_price) }}</td>
                            <td class="py-1.5 px-3 font-mono">{{ currency(stock.wholesale_price) }}</td>
                            <td class="py-1.5 px-3">
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full text-xs font-semibold',
                                        {'bg-red-100 text-red-800': stock.status === 'Expired'},
                                        {'bg-yellow-100 text-yellow-800': stock.status === 'Expiring Soon'},
                                        {'bg-green-100 text-green-800': stock.status === 'In Stock'},
                                    ]">
                                        {{ stock.status }}
                                    </span>
                            </td>
                            <td class="py-1.5 px-3">{{ stock.expiration_date ? formatDate(stock.expiration_date) : '-' }}</td>
                            <td class="py-1.5 px-3">{{ stock.batch_number || '-' }}</td>
                            <td class="py-1.5 px-3">
                                <div class="flex items-center space-x-2">
                                    <Link :href="route('stocks.edit', stock.id)">
                                        <Button variant="ghost" size="icon" class="text-gray-500 hover:text-blue-600">
                                            <PencilIcon class="h-5 w-5" />
                                        </Button>
                                    </Link>
                                    <Button
                                        title="Mark as Expired"
                                        v-if="checkIfExpired(stock)"
                                        variant="ghost"
                                        size="icon"
                                        class="text-red-500 hover:bg-red-50 hover:text-red-600"
                                        @click="showConfirmModal = true; stockToConfirm = stock"
                                    >
                                        <AlertCircleIcon class="h-5 w-5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredStocks.length === 0">
                            <td colspan="9" class="py-8 text-center text-gray-500 italic">No stocks found.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white rounded-lg p-6 shadow-xl w-full max-w-sm mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Confirm Action</h3>
                        <button @click="showConfirmModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <p class="text-gray-700 mb-6">Are you sure you want to mark this stock as expired? This action cannot be undone.</p>
                    <div class="flex justify-end space-x-3">
                        <Button @click="showConfirmModal = false" variant="outline">Cancel</Button>
                        <Button @click="handleConfirmExpire" variant="default" class="bg-red-600 hover:bg-red-700">Confirm</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import currency from '@/modules/currecyFormatter';
import { PencilIcon, PlusCircleIcon, SearchIcon, AlertCircleIcon } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button';

// Props
interface Stock {
    id: string;
    stockable?: { name: string };
    stockable_type: string;
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
const showConfirmModal = ref(false);
const stockToConfirm = ref<Stock | null>(null);

const filteredStocks = computed(() => {
    if (!props.stocks) return [];
    return props.stocks.filter((stock) => {
        const matchesSearch = stock.stockable?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ?? false;
        const matchesType = !selectedType.value || stock.stockable_type.includes(selectedType.value);
        return matchesSearch && matchesType;
    });
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
