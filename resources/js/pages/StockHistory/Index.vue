<template>
    <AppLayout>
        <Head title="Stock History" />

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Stock History</h1>

            <!-- Search Field -->
            <div class="mb-4">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by item name or type..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Item</th>
                        <th class="text-left px-4 py-2">Type</th>
                        <th class="text-left px-4 py-2">Quantity</th>
                        <th class="text-left px-4 py-2">Date</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr v-for="(history, index) in filteredStockHistories" :key="history.id">
                        <td class="px-4 py-2">{{ index + 1 }}</td>
                        <td class="px-4 py-2">{{ history.stock.stockable.name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            {{ history.stock.stockable_type?.split('\\').pop() }}
                        </td>
                        <td class="px-4 py-2">{{ history.quantity }}</td>
                        <td class="px-4 py-2">{{ formatDate(history.date) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

interface Stockable {
    name: string
}
interface Stock {
    stockable: Stockable;
    stockable_id: string;
    stockable_type: string;
}
interface History {
    id: number;
    stock: Stock;
    quantity: number;
    date: string;
}

const props = defineProps<{ stockHistories: History[] }>()

const searchQuery = ref('')

// Filtered stock histories based on search query
const filteredStockHistories = computed(() => {
    return props.stockHistories.filter(history => {
        const itemName = history.stock.stockable.name.toLowerCase()
        const itemType = history.stock.stockable_type?.toLowerCase().split('\\').pop()
        return (
            itemName.includes(searchQuery.value.toLowerCase()) ||
            itemType?.includes(searchQuery.value.toLowerCase())
        )
    })
})

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString()
}
</script>
