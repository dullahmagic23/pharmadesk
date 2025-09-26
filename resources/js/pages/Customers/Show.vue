<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Customer Details" />

    <div class="space-y-6">
      <!-- Customer Info -->
      <section class="p-4 bg-white rounded shadow">
        <h1 class="text-xl font-bold">{{ customer.name }}</h1>
        <p v-if="customer.phone">Phone: {{ customer.phone }}</p>
        <p v-if="customer.email">Email: {{ customer.email }}</p>
        <p v-if="customer.address">Address: {{ customer.address }}</p>
      </section>

      <!-- Sales -->
      <section class="space-y-4">
        <h2 class="text-lg font-semibold ml-4">Sales</h2>
        <div v-for="sale in customer.sales" :key="sale.id" class="p-4 bg-gray-50 rounded shadow">
          <div class="flex justify-between items-center mb-2">
            <p><strong>Date:</strong> {{ sale.date }}</p>
            <p><strong>Total:</strong> {{ formatCurrency(sale.total) }}</p>
            <p><strong>Paid:</strong> {{ formatCurrency(sale.paid) }}</p>
            <p><strong>Balance:</strong> {{ formatCurrency(sale.balance) }}</p>
          </div>

          <!-- Sale Items -->
          <table class="w-full table-auto border-collapse border border-gray-200">
            <thead>
              <tr class="bg-gray-200">
                <th class="border px-2 py-1 text-left">Item</th>
                <th class="border px-2 py-1 text-left">Type</th>
                <th class="border px-2 py-1 text-right">Qty</th>
                <th class="border px-2 py-1 text-right">Price</th>
                <th class="border px-2 py-1 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in sale.items" :key="item.id">
                <td class="border px-2 py-1">{{ item.sellable.name }}</td>
                <td class="border px-2 py-1">{{ item.type }}</td>
                <td class="border px-2 py-1 text-right">{{ item.quantity }}</td>
                <td class="border px-2 py-1 text-right">{{ formatCurrency(item.price) }}</td>
                <td class="border px-2 py-1 text-right">{{ formatCurrency(item.subtotal) }}</td>
              </tr>
            </tbody>
          </table>

          <!-- Payments -->
          <div class="mt-2">
            <h3 class="font-semibold">Payments</h3>
            <ul class="list-disc list-inside">
              <li v-for="payment in sale.payments" :key="payment.id">
                {{ formatCurrency(payment.amount) }} — Paid at: {{ payment.paid_at }}
              </li>
            </ul>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  customer: Object
})

const breadcrumbs = ref([
    { title: 'Dashboard', href: '/' },
  { title: 'Customers', href: '/customers' },
  { title: 'Details', href: '/customers/' + props.customer.id }
])

function formatCurrency(value) {
  return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value)
}
</script>
