<template>
  <AppLayout :title="`Purchase #${purchase.id}`">
    <div class="container p-6 mx-auto">
        <h1 class="text-2xl font-semibold">Purchase Details</h1>
        <div class="mb-6 flex justify-end space-x-6">
        <Link :href="route('purchases.index')" class="btn btn-secondary">Back</Link>
          <a :href="route('purchases.print', purchase.id)">Print</a>
      </div>

      <div class="bg-white rounded shadow p-6 mb-8">
        <div class="mb-3 flex flex-wrap gap-4">
          <div>
            <span class="font-semibold">Purchase Date:</span>
            <span>{{ formatDate(purchase.created_at) }}</span>
          </div>
          <div>
            <span class="font-semibold">Vendor:</span>
            <span>{{ purchase.vendor.name }}</span>
          </div>
          <div>
            <span class="font-semibold">Total:</span>
            <span>{{ currency(getTotal(purchase.purchasables)) }}</span>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full table-auto">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="px-3 py-2">Name</th>
              <th class="px-3 py-2">Type</th>
              <th class="px-3 py-2">Quantity</th>
              <th class="px-3 py-2">Unit Cost</th>
              <th class="px-3 py-2">Batch #</th>
              <th class="px-3 py-2">Expiry</th>
              <th class="px-3 py-2">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in purchase.purchasables"
              :key="item.id"
              class="border-b hover:bg-gray-50"
            >
              <td class="px-3 py-2">
                {{ item.purchasable?.name || '-' }}
              </td>
                <td class="px-3 py-2 capitalize">
                    {{ getPurchasableTypeLabel(item) }}
                </td>
                <td class="px-3 py-2">
                {{ item.quantity }}
              </td>
              <td class="px-3 py-2">
                {{ currency(item.unit_cost) }}
              </td>
              <td class="px-3 py-2">
                {{ item.batch_number || '-' }}
              </td>
              <td class="px-3 py-2">
                {{ item.expiry_date ? formatDate(item.expiry_date) : '-' }}
              </td>
              <td class="px-3 py-2 font-medium">
                {{ currency(item.subtotal) }}
              </td>
            </tr>
            <tr class="font-black">
                <td class="px-3 py-2" colspan="6">Total</td>
                <td class="px-3 py-2">{{currency(purchase.total_amount)}}</td>
            </tr>
            <tr v-if="!purchase.purchasables || purchase.purchasables.length === 0">
              <td class="px-3 py-4 text-center text-gray-500" colspan="7">
                No items found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import currency from '@/modules/currecyFormatter'

defineProps({
  purchase: {
    type: Object,
    required: true
  }
})

function formatDate(date: string) {
  return new Date(date).toLocaleDateString()
}


function getTotal(items: any[]) {
  if (!items?.length) return 0;
  return items.reduce((sum, item) => sum + Number(item.subtotal), 0)
}

const TYPE_LABELS: Record<string, string> = {
    'App\\Models\\Medicine': 'Medicine',
    'App\\Models\\Product': 'Product',
    // Add more as needed
}

function getPurchasableTypeLabel(item: any): string {
    if (item?.purchasable_type && TYPE_LABELS[item.purchasable_type]) {
        return TYPE_LABELS[item.purchasable_type]
    }
    return 'Unknown'
}

</script>
