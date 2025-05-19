<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Stocks" />

        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Stock List</h1>
                <div class="flex items-center gap-4">
                    <input type="text" v-model="searchQuery" placeholder="Search stocks..." class="rounded border px-4 py-2" />
                    <select v-model="selectedType" class="rounded border px-4 py-2">
                        <option value="">All Types</option>
                        <option value="Product">Product</option>
                        <option value="Medicine">Medicine</option>
                    </select>
                    <Link :href="route('stocks.create')" class="flex rounded bg-gray-900 px-2 py-2 text-white">
                        <PlusCircleIcon class="mr-2" />
                        Add Stock
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto rounded bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Retail Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Wholesale Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Last Updated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="stock in filteredStocks" :key="stock.id">
                            <td class="px-6 py-4 whitespace-nowrap">{{ stock.stockable?.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ stock.stockable_type.includes('Product') ? 'Product' : 'Medicine' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ stock.unit?.unit_name || '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ formatQuantity(stock.quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ currency(stock.retail_price) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ currency(stock.wholesale_price) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(stock.updated_at) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <Link :href="route('stocks.edit',stock.id)" class="flex">
                                    <PencilIcon class="w-4 h-4 mr-2"/>
                                    Edit
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import currency from '@/modules/currecyFormatter';
import { PencilIcon, PlusCircleIcon } from 'lucide-vue-next';
import { ref, computed } from 'vue';

// Props
const props = defineProps({
    stocks: Object,
});

const searchQuery = ref('');
const selectedType = ref('');

const filteredStocks = computed(() => {
    return props.stocks.filter((stock: object) => {
        const matchesSearch = stock.stockable?.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesType = !selectedType.value || stock.stockable_type.includes(selectedType.value);
        return matchesSearch && matchesType;
    });
});

const formatQuantity = (quantity: number): string => {
    return Math.trunc(quantity);
};

const formatDate = (date) => new Date(date).toLocaleDateString();

const breadcrumbs = [{ title: 'All Stocks', href: '/stocks' }];
</script>
