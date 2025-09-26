<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
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

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Products', href: '/products' },
  { title: props.product.name, href: `/products/${props.product.id}` },
];
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Product Details" />

    <div class="max-w-7xl mx-auto p-6 space-y-6">

      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">{{ product.name }}</h1>
        <button @click="Inertia.visit('/products')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Back</button>
      </div>

      <!-- Basic Info -->
      <div class="bg-white shadow rounded-lg p-6 space-y-4">
        <div class="flex items-center space-x-4">
          <span class="font-semibold text-gray-700">Category:</span>
          <span>{{ product.category || '-' }}</span>
        </div>
        <div class="flex items-center space-x-4">
          <span class="font-semibold text-gray-700">Description:</span>
          <span>{{ product.description || '-' }}</span>
        </div>
        <div class="flex items-center space-x-4">
          <span class="font-semibold text-gray-700">Unit:</span>
          <span>{{ product.unit || '-' }}</span>
        </div>
      </div>

      <!-- Stock Info -->
      <div v-if="showStock && product.stock" class="bg-white shadow rounded-lg p-6 space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-bold">Stock Details</h2>
          <button @click="showStock = !showStock" class="text-blue-500 hover:underline">
            {{ showStock ? 'Hide' : 'Show' }}
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-4 py-2 border">Batch</th>
                <th class="px-4 py-2 border">Quantity</th>
                <th class="px-4 py-2 border">Buying Price</th>
                <th class="px-4 py-2 border">Retail Price</th>
                <th class="px-4 py-2 border">Wholesale Price</th>
                <th class="px-4 py-2 border">Expiration Date</th>
                <th class="px-4 py-2 border">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr :class="{
                    'bg-red-100': stockStatus(product.stock) === 'expired',
                    'bg-yellow-100': stockStatus(product.stock) === 'expiring-soon',
                    'bg-orange-100': stockStatus(product.stock) === 'low-stock'
                  }"
                  class="hover:bg-gray-50">
                <td class="px-4 py-2 border">{{ product.stock.batch_number }}</td>
                <td class="px-4 py-2 border">{{ product.stock.quantity }}</td>
                <td class="px-4 py-2 border">{{ currency(product.stock.buying_price) }}</td>
                <td class="px-4 py-2 border">{{ currency(product.stock.retail_price) }}</td>
                <td class="px-4 py-2 border">{{ currency(product.stock.wholesale_price) }}</td>
                <td class="px-4 py-2 border">{{ product.stock.expiration_date || '-' }}</td>
                <td class="px-4 py-2 border capitalize">{{ stockStatus(product.stock).replace('-', ' ') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Related Sales -->
      <div v-if="showSales && product.sale_items.length" class="bg-white shadow rounded-lg p-6 space-y-4">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-bold">Related Sales</h2>
          <button @click="showSales = !showSales" class="text-blue-500 hover:underline">
            {{ showSales ? 'Hide' : 'Show' }}
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-4 py-2 border">Sale ID</th>
                <th class="px-4 py-2 border">Quantity</th>
                <th class="px-4 py-2 border">Price</th>
                <th class="px-4 py-2 border">Subtotal</th>
                <th class="px-4 py-2 border">Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in product.sale_items" :key="item.id" class="hover:bg-gray-50 cursor-pointer"
                  @click="router.visit(`/sales/${item.sale_id}`)">
                <td class="px-4 py-2 border">{{ item.sale_id }}</td>
                <td class="px-4 py-2 border">{{ item.quantity }}</td>
                <td class="px-4 py-2 border">{{ currency(item.price) }}</td>
                <td class="px-4 py-2 border">{{ currency(item.subtotal) }}</td>
                <td class="px-4 py-2 border">{{ new Date(item.created_at).toLocaleDateString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<style scoped>
table th,
table td {
  border: 1px solid #e5e7eb;
}
</style>
