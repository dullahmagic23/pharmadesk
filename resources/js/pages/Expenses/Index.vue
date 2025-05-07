<template>
  <AppLayout>
    <div class="container p-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Expenses</h1>
        <Link :href="route('expenses.create')" class="btn btn-primary">
          New Expense
        </Link>
      </div>

      <table class="w-full border border-gray-200 rounded">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-2">Title</th>
            <th class="p-2">Amount</th>
            <th class="p-2">Date</th>
            <th class="p-2">Linked Bill</th>
            <th class="p-2">Notes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="expense in expenses" :key="expense.id" class="border-t">
            <td class="p-2">{{ expense.title }}</td>
            <td class="p-2">Ksh {{ expense.amount }}</td>
            <td class="p-2">{{ expense.expense_date }}</td>
            <td class="p-2">
              <span v-if="expense.bill">
                <Link :href="route('bills.show', expense.bill.id)" class="text-blue-600 underline">
                  {{ expense.bill.reference || 'View Bill' }}
                </Link>
              </span>
              <span v-else class="text-gray-500">—</span>
            </td>
            <td class="p-2 text-sm text-gray-600">{{ expense.notes || '—' }}</td>
          </tr>
        </tbody>
      </table>

      <div v-if="expenses.length === 0" class="text-center text-gray-500 mt-4">
        No expenses recorded yet.
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  expenses: Array
})
</script>
