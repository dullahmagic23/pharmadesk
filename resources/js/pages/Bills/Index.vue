<template>
  <AppLayout title="Bills">
    <template #header>
      
    </template>

    <div class="container p-6 mx-auto">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold leading-tight">Bills</h2>
        <Link :href="route('bills.create')" class="btn btn-primary">+ New Bill</Link>
      </div>
      <!-- Filter Section -->
      <div class="flex space-x-3 mb-4">
        <Input
          v-model="filters.entity"
          type="text"
          placeholder="Filter by Entity"
          class="input input-bordered"
        />
        <Input
          v-model="filters.type"
          type="text"
          placeholder="Filter by Type"
          class="input input-bordered"
        />
        <Input
          v-model="filters.status"
          type="text"
          placeholder="Filter by Status"
          class="input input-bordered"
        />
      </div>
      <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Date</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="bill in filteredBills" :key="bill.id">
              <td class="px-4 py-2">{{ bill.billable?.name }}</td>
              <td class="px-4 py-2 capitalize">{{ bill.type }}</td>
              <td class="px-4 py-2"> {{ currency(bill.amount) }}</td>
              <td class="px-4 py-2">{{ bill.billing_date }}</td>
              <td class="px-4 py-2 capitalize">{{ bill.status }}</td>
              <td class="px-4 py-2 text-right">
                <Link :href="route('bills.show', bill.id)" class="text-blue-600 hover:underline">View</Link>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!filteredBills.length" class="p-4 text-center text-gray-500">No bills found.</div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import currency from '@/modules/currecyFormatter'
import { ref, computed } from 'vue'
import Input from '@/components/ui/input/Input.vue'

const props = defineProps({
  bills: Array,
})

const filters = ref({
  entity: '',
  type: '',
  status: '',
})

const filteredBills = computed(() => {
  return props.bills.filter(bill => {
    const entityMatch = bill.billable?.name?.toLowerCase().includes(filters.value.entity.toLowerCase())
    const typeMatch = bill.billable_type.split('\\').pop().toLowerCase().includes(filters.value.type.toLowerCase())
    const statusMatch = bill.status.toLowerCase().includes(filters.value.status.toLowerCase())
    return entityMatch && typeMatch && statusMatch
  })
})
</script>