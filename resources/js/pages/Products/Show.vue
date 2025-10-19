<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import currency from '@/modules/currecyFormatter';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const showStock = ref(true);
const showSales = ref(true);

const formatPrice = (price) => (price ? `$${parseFloat(price).toFixed(2)}` : '-');

const stockStatus = (stock) => {
  const today = new Date();
  const expDate = stock.expiration_date ? new Date(stock.expiration_date) : null;
  if (expDate && expDate < today) return 'expired';
  if (expDate && expDate - today <= 30 * 24 * 60 * 60 * 1000) return 'expiring-soon';
  if (stock.quantity <= 10) return 'low-stock';
  return 'available';
};

const getStatusConfig = (status) => {
  const config = {
    expired: {
      color: 'bg-red-50 border-red-200 dark:bg-red-950 dark:border-red-800',
      badge: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
      icon: '⚠️',
      label: 'Expired'
    },
    'expiring-soon': {
      color: 'bg-yellow-50 border-yellow-200 dark:bg-yellow-950 dark:border-yellow-800',
      badge: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
      icon: '⏱️',
      label: 'Expiring Soon'
    },
    'low-stock': {
      color: 'bg-orange-50 border-orange-200 dark:bg-orange-950 dark:border-orange-800',
      badge: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
      icon: '📉',
      label: 'Low Stock'
    },
    available: {
      color: 'bg-green-50 border-green-200 dark:bg-green-950 dark:border-green-800',
      badge: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
      icon: '✓',
      label: 'Available'
    }
  };
  return config[status] || config.available;
};

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Products', href: '/products' },
  { title: props.product.name, href: `/products/${props.product.id}` },
];

const totalSalesQuantity = computed(() =>
  props.product.sale_items.reduce((sum, item) => sum + item.quantity, 0)
);

const totalSalesRevenue = computed(() =>
  props.product.sale_items.reduce((sum, item) => sum + parseFloat(item.subtotal || 0), 0)
);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Product Details" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
      <div class="max-w-6xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ product.name }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Product ID: {{ product.id }}</p>
          </div>
          <button
            @click="Inertia.visit('/products')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Products
          </button>
        </div>

        <!-- Basic Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Category</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
              {{ product.category || '—' }}
            </p>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unit</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
              {{ product.unit || '—' }}
            </p>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Sales</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
              {{ totalSalesQuantity }} units
            </p>
          </div>
        </div>

        <!-- Description -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Description</h3>
          <p class="text-gray-900 dark:text-gray-100 mt-3 leading-relaxed">
            {{ product.description || 'No description available' }}
          </p>
        </div>

        <!-- Stock Info -->
        <div v-if="product.stock" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Stock Details</h2>
            <button
              @click="showStock = !showStock"
              class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition"
            >
              <i :class="['fas', showStock ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
              {{ showStock ? 'Hide' : 'Show' }}
            </button>
          </div>

          <transition name="expand">
            <div v-if="showStock" class="p-6 space-y-4">
              <!-- Status Alert -->
              <div :class="[getStatusConfig(stockStatus(product.stock)).color, 'border rounded-lg p-4 flex items-start gap-3']">
                <span class="text-xl flex-shrink-0">{{ getStatusConfig(stockStatus(product.stock)).icon }}</span>
                <div>
                  <p class="font-semibold text-gray-900 dark:text-white">
                    {{ getStatusConfig(stockStatus(product.stock)).label }}
                  </p>
                  <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                    <span v-if="product.stock.quantity">
                      Quantity: <strong>{{ product.stock.quantity }}</strong> units
                    </span>
                    <span v-if="product.stock.expiration_date" class="ml-4">
                      Expires: <strong>{{ new Date(product.stock.expiration_date).toLocaleDateString() }}</strong>
                    </span>
                  </p>
                </div>
              </div>

              <!-- Stock Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Batch Number</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ product.stock.batch_number }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Quantity</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ product.stock.quantity }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Buying Price</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ currency(product.stock.buying_price) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Retail Price</p>
                  <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-2">{{ currency(product.stock.retail_price) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Wholesale Price</p>
                  <p class="text-lg font-bold text-green-600 dark:text-green-400 mt-2">{{ currency(product.stock.wholesale_price) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Expiration Date</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white mt-2">
                    {{ product.stock.expiration_date ? new Date(product.stock.expiration_date).toLocaleDateString() : '—' }}
                  </p>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Related Sales -->
        <div v-if="product.sale_items && product.sale_items.length" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sales History</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ product.sale_items.length }} transactions • {{ totalSalesQuantity }} units sold • {{ currency(totalSalesRevenue) }} revenue
              </p>
            </div>
            <button
              @click="showSales = !showSales"
              class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition"
            >
              <i :class="['fas', showSales ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
              {{ showSales ? 'Hide' : 'Show' }}
            </button>
          </div>

          <transition name="expand">
            <div v-if="showSales" class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Sale ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Subtotal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  <tr
                    v-for="item in product.sale_items"
                    :key="item.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                  >
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                      #{{ item.sale_id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                      {{ item.quantity }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                      {{ currency(item.price) }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                      {{ currency(item.subtotal) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                      {{ new Date(item.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                      <button
                        @click="router.visit(`/sales/${item.sale_id}`)"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition text-xs font-medium"
                      >
                        <i class="fas fa-eye"></i>
                        View
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </transition>
        </div>

        <!-- Empty State for Sales -->
        <div v-if="!product.sale_items || !product.sale_items.length" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
          <div class="text-4xl mb-3">📦</div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No Sales Yet</h3>
          <p class="text-gray-600 dark:text-gray-400 mt-1">This product hasn't been sold yet.</p>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease;
}

.expand-enter-from {
  opacity: 0;
  max-height: 0;
}

.expand-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
