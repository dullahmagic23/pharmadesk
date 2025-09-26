<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
  products: Array,
});
</script>

<template>
  <AppLayout>
    <Head title="Product Report" />

    <div class="max-w-7xl mx-auto p-6 space-y-6">
      <h1 class="text-3xl font-bold mb-4">Product Report</h1>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md">
          <thead class="bg-green-500 text-white">
            <tr>
              <th class="p-3 text-left">#</th>
              <th class="p-3 text-left">Name</th>
              <th class="p-3 text-left">Stock Qty</th>
              <th class="p-3 text-left">Buying Price</th>
              <th class="p-3 text-left">Selling Price</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(prod, index) in products" :key="prod.id" class="border-b">
              <td class="p-3">{{ index + 1 }}</td>
              <td class="p-3">{{ prod.name }}</td>
              <td class="p-3">{{ prod.stock?.quantity || 0 }}</td>
              <td class="p-3">{{ prod.stock?.buying_price || 0 }}</td>
              <td class="p-3">{{ prod.stock?.selling_price || 0 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
