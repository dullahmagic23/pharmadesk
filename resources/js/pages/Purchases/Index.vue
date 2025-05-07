<template>
  <AppLayout title="Purchases">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Purchases</h1>
        <Link :href="route('purchases.create')" class="btn btn-primary">
          New Purchase
        </Link>
      </div>

      <div class="mb-3 flex flex-wrap items-center gap-4">
        <input
          v-model="search"
          type="text"
          placeholder="Search by date, vendor, or item..."
          class="border rounded px-3 py-2 w-72"
        />
      </div>

      <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full table-auto">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="px-4 py-2">Date</th>
              <th class="px-4 py-2">Vendor</th>
              <th class="px-4 py-2">Items</th>
              <th class="px-4 py-2">Total</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="purchase in filteredPurchases"
              :key="purchase.id"
              class="border-b hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ formatDate(purchase.created_at) }}</td>
              <td class="px-4 py-2">{{ purchase.vendor.name }}</td>
              <td class="px-4 py-2">
                <ul class="list-disc list-inside">
                  <li
                    v-for="item in purchase.purchasables"
                    :key="item.id"
                  >
                    {{ item.purchasable?.name || '-' }} (x{{ item.quantity }})
                  </li>
                </ul>
              </td>
              <td class="px-4 py-2 font-semibold text-right">
                {{ formatCurrency(getTotal(purchase.purchasables)) }}
              </td>
              <td class="px-4 py-2">
                <Link
                  :href="`/purchases/${purchase.id}`"
                  class="text-blue-600 hover:underline"
                >
                  View
                </Link>
              </td>
            </tr>
            <tr v-if="filteredPurchases.length === 0">
              <td class="px-4 py-8 text-center text-gray-500" colspan="5">
                No purchases found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  purchases: Array,
})

const search = ref('')

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}

function formatCurrency(amount) {
  return 'TZS ' + Number(amount).toLocaleString()
}

function getTotal(items) {
  return items.reduce((sum, item) => sum + Number(item.subtotal), 0)
}

const filteredPurchases = computed(() => {
  if (!search.value) return props.purchases
  const term = search.value.toLowerCase()
  return props.purchases.filter(purchase => {
    const date = formatDate(purchase.created_at).toLowerCase()
    const vendor = purchase.vendor?.name?.toLowerCase() || ''
    const items = (purchase.purchasables || [])
      .map(item => item.purchasable?.name?.toLowerCase() || '')
      .join(' ')
    return (
      date.includes(term) ||
      vendor.includes(term) ||
      items.includes(term)
    )
  })
})
</script>
