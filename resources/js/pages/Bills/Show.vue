<template>
  <AppLayout>
    <div class="container p-6 space-y-6">
      <div>
        <h1 class="text-xl font-bold mb-2">Bill Details</h1>
        <div class="space-y-1 text-sm text-gray-700">
          <p><strong>Reference:</strong> {{ bill.id }}</p>
          <p><strong>Billable:</strong> {{ bill.billable.name }} ({{ bill.type }})</p>
          <p><strong>Amount:</strong>  {{ currency(bill.amount) }}</p>
          <p><strong>Billing Date:</strong> {{ bill.billing_date }}</p>
          <p><strong>Total Paid:</strong>  {{ currency(bill.total_paid )}}</p>
          <p><strong>Balance:</strong> {{ currency(bill.amount - bill.total_paid) }}</p>
        </div>
      </div>

      <div>
        <div class="flex justify-between items-center mb-2">
          <h2 class="text-lg font-semibold">Payments</h2>
          <Link :href="route('bill-payments.create', { bill: bill.id })" class="btn btn-primary">
            Add Payment
          </Link>
        </div>

        <table class="w-full border border-gray-200 rounded">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="p-2">Date</th>
              <th class="p-2">Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="payment in bill.payments" :key="payment.id" class="border-t">
              <td class="p-2">{{ payment.payment_date }}</td>
              <td class="p-2"> {{ currency(payment.amount) }}</td>
            </tr>
          </tbody>
        </table>

        <div v-if="bill.payments.length === 0" class="text-center text-gray-500 mt-4">
          No payments recorded for this bill.
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import currency from '@/modules/currecyFormatter'

const props = defineProps({
  bill: Object
})
</script>
