<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import currency from '@/modules/currecyFormatter';
import { ArrowLeft, AlertTriangle, CheckCircle, Clock, Package, TrendingUp, ChevronDown } from 'lucide-vue-next';

const props = defineProps({
    medicine: {
        type: Object,
        required: true,
    },
});

const showStock = ref(true);
const showSales = ref(true);

const formatPrice = (price) => (price ? `$${parseFloat(price).toFixed(2)}` : '-');

const stockStatus = (stock) => {
    const today = new Date();
    const expDate = new Date(stock.expiration_date);
    if (expDate < today) return 'expired';
    if (expDate - today <= 30 * 24 * 60 * 60 * 1000) return 'expiring-soon';
    if (stock.quantity <= 10) return 'low-stock';
    return 'available';
};

const getStatusConfig = (status) => {
    const configs = {
        expired: { icon: AlertTriangle, color: 'bg-red-50 border-red-200', text: 'text-red-700', badge: 'bg-red-100 text-red-800', label: 'Expired' },
        'expiring-soon': { icon: Clock, color: 'bg-amber-50 border-amber-200', text: 'text-amber-700', badge: 'bg-amber-100 text-amber-800', label: 'Expiring Soon' },
        'low-stock': { icon: AlertTriangle, color: 'bg-orange-50 border-orange-200', text: 'text-orange-700', badge: 'bg-orange-100 text-orange-800', label: 'Low Stock' },
        available: { icon: CheckCircle, color: 'bg-green-50 border-green-200', text: 'text-green-700', badge: 'bg-green-100 text-green-800', label: 'Available' },
    };
    return configs[status] || configs.available;
};

const totalSalesQuantity = computed(() => {
    return props.medicine.sale_items?.reduce((sum, item) => sum + item.quantity, 0) || 0;
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Medicines', href: '/medicines' },
    { title: props.medicine.name, href: `/medicines/${props.medicine.id}` },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Medicine Details" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">{{ medicine.name }}</h1>
                            <p class="text-gray-500 text-sm mt-1">{{ medicine.brand }}</p>
                        </div>
                        <Link href="/medicines">
                            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition flex items-center gap-2">
                                <ArrowLeft class="w-4 h-4" />
                                Back to Medicines
                            </button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Category</p>
                        <p class="text-lg font-bold text-gray-900">{{ medicine.category?.name || '-' }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Retail Price</p>
                        <p class="text-lg font-bold text-green-600">{{ currency(medicine.stock?.retail_price) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-blue-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Wholesale Price</p>
                        <p class="text-lg font-bold text-blue-600">{{ currency(medicine.stock?.wholesale_price) }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-purple-200 p-4">
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Sold</p>
                        <p class="text-lg font-bold text-purple-600">{{ totalSalesQuantity }} units</p>
                    </div>
                </div>

                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Units</h3>
                            <div class="flex flex-wrap gap-2">
                <span
                    v-for="unit in medicine.units"
                    :key="unit.id"
                    class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-medium"
                >
                  {{ unit.unit_name }}
                </span>
                            </div>
                        </div>
                        <div v-if="medicine.description">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Description</h3>
                            <p class="text-gray-600 leading-relaxed">{{ medicine.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stock Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                    <div
                        class="flex items-center justify-between p-6 cursor-pointer border-b border-gray-200 hover:bg-gray-50 transition-colors"
                        @click="showStock = !showStock"
                    >
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <Package class="w-5 h-5 text-blue-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Stock Details</h2>
                                <p v-if="medicine.stock" class="text-xs text-gray-500 mt-0.5">Batch: {{ medicine.stock.batch_number }}</p>
                            </div>
                        </div>
                        <ChevronDown :class="['w-5 h-5 text-gray-400 transition-transform', showStock && 'rotate-180']" />
                    </div>

                    <div v-show="showStock && medicine.stock" class="p-6">
                        <div v-if="medicine.stock" class="space-y-6">
                            <!-- Stock Status Banner -->
                            <div :class="['rounded-lg border-2 p-4 flex items-start gap-3', getStatusConfig(stockStatus(medicine.stock)).color]">
                                <component :is="getStatusConfig(stockStatus(medicine.stock)).icon" :class="['w-5 h-5 mt-0.5 flex-shrink-0', getStatusConfig(stockStatus(medicine.stock)).text]" />
                                <div>
                                    <p :class="['font-semibold', getStatusConfig(stockStatus(medicine.stock)).text]">
                                        {{ getStatusConfig(stockStatus(medicine.stock)).label }}
                                    </p>
                                    <p class="text-sm mt-1 opacity-90">
                                        <span v-if="stockStatus(medicine.stock) === 'expired'">This stock has expired</span>
                                        <span v-else-if="stockStatus(medicine.stock) === 'expiring-soon'">Expires on {{ new Date(medicine.stock.expiration_date).toLocaleDateString() }}</span>
                                        <span v-else-if="stockStatus(medicine.stock) === 'low-stock'">Only {{ medicine.stock.quantity }} units remaining</span>
                                        <span v-else>Stock is in good condition</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Stock Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-600 font-medium mb-1">Quantity</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ medicine.stock.quantity }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-600 font-medium mb-1">Expiration</p>
                                    <p class="text-lg font-bold text-gray-900">{{ new Date(medicine.stock.expiration_date).toLocaleDateString() }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-600 font-medium mb-1">Location</p>
                                    <p class="text-lg font-bold text-gray-900">{{ medicine.stock.location_id || '-' }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-600 font-medium mb-1">Batch #</p>
                                    <p class="text-lg font-bold text-gray-900">{{ medicine.stock.batch_number }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <Package class="w-12 h-12 mx-auto mb-2 opacity-20" />
                            <p>No stock information available</p>
                        </div>
                    </div>
                </div>

                <!-- Related Sales -->
                <div v-if="medicine.sale_items && medicine.sale_items.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div
                        class="flex items-center justify-between p-6 cursor-pointer border-b border-gray-200 hover:bg-gray-50 transition-colors"
                        @click="showSales = !showSales"
                    >
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <TrendingUp class="w-5 h-5 text-purple-600" />
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Related Sales</h2>
                                <p class="text-xs text-gray-500 mt-0.5">{{ medicine.sale_items.length }} transactions</p>
                            </div>
                        </div>
                        <ChevronDown :class="['w-5 h-5 text-gray-400 transition-transform', showSales && 'rotate-180']" />
                    </div>

                    <div v-show="showSales" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <tr class="text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <th class="py-4 px-6">Sale ID</th>
                                <th class="py-4 px-6 text-center">Quantity</th>
                                <th class="py-4 px-6 text-right">Price</th>
                                <th class="py-4 px-6 text-right">Subtotal</th>
                                <th class="py-4 px-6">Date</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="item in medicine.sale_items"
                                :key="item.id"
                                class="hover:bg-purple-50 cursor-pointer transition-colors group"
                                @click="router.visit(`/sales/${item.sale_id}`)"
                            >
                                <td class="py-4 px-6">
                                    <Link :href="`/sales/${item.sale_id}`" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                                        #{{ item.sale_id }}
                                    </Link>
                                </td>
                                <td class="py-4 px-6 text-center font-medium text-gray-900">{{ item.quantity }}</td>
                                <td class="py-4 px-6 text-right text-gray-600">{{ currency(item.price) }}</td>
                                <td class="py-4 px-6 text-right font-semibold text-gray-900">{{ currency(item.subtotal) }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ new Date(item.created_at).toLocaleDateString() }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <TrendingUp class="w-12 h-12 mx-auto mb-4 text-gray-300" />
                    <p class="text-gray-600 font-medium">No sales records yet</p>
                    <p class="text-gray-500 text-sm mt-1">This medicine hasn't been sold yet</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
