<template>
  <AppLayout>
    <Head title="Stocks" />

    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Stock List</h1>
        <Link :href="route('stocks.create')" class="flex bg-gray-900  text-white px-2 py-2 rounded">
          <PlusCircleIcon class="mr-2"/>
          Add Stock</Link>
      </div>

      <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retail Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wholesale Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="stock in stocks" :key="stock.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ stock.stockable?.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ stock.stockable_type.includes('Product') ? 'Product' : 'Medicine' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ stock.quantity }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ currency(stock.retail_price) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ currency(stock.wholesale_price) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(stock.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import currency from '@/modules/currecyFormatter'
import { PlusCircleIcon } from 'lucide-vue-next'

// Props
defineProps({
  stocks: Object,
})


const formatDate = (date) => new Date(date).toLocaleDateString()
</script>
