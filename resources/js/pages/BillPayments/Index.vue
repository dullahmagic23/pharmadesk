<template>
  <AppLayout>
    <div class="container p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">All Bill Payments</h1>
        <Link :href="route('bill-payments.create')" class="btn btn-primary">
          New Payment
        </Link>
      </div>

      <table class="w-full border border-gray-200 rounded">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-2">Billable Name</th>
            <th class="p-2">Billable Type</th>
            <th class="p-2">Bill Amount</th>
            <th class="p-2">Amount Paid</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments" :key="payment.id" class="border-t">
            <td class="p-2">{{ payment.bill.billable.name }}</td>
            <td class="p-2">{{ payment.bill.billable_type }}</td>
            <td class="p-2">{{ payment.payment_date }}</td>
            <td class="p-2"> {{ currency(payment.amount) }}</td>
          </tr>
        </tbody>
      </table>

      <div v-if="payments.length === 0" class="text-center text-gray-500 mt-4">
        No bill payments recorded.
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import currency from '@/modules/currecyFormatter'

const props = defineProps({
  payments: Array
})
</script>
