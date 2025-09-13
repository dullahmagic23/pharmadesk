<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import {DeleteIcon,PrinterIcon} from 'lucide-vue-next'

const props = defineProps({
    receipts: Array, // passed from controller
    filters: Object, // e.g. { search: '' }
})

const search = ref(props.filters?.search || '')

const filteredReceipts = computed(() => {
    if (!search.value) return props.receipts
    return props.receipts.filter(r =>
        (r.reference?.toLowerCase().includes(search.value.toLowerCase())) ||
        (r.sale_id?.toLowerCase().includes(search.value.toLowerCase())) ||
        (r.issued_by?.toLowerCase().includes(search.value.toLowerCase()))
    )
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Receipts', href: '/receipts' },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Receipts" />

        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">🧾 Receipts</h1>
                <Link :href="route('receipts.create')">
                    <Button>➕ New Receipt</Button>
                </Link>
            </div>

            <!-- Filters -->
            <Card>
                <CardContent class="p-4 flex items-center justify-between gap-4">
                    <Input
                        v-model="search"
                        placeholder="Search receipts by reference, sale, or cashier..."
                        class="w-full md:w-1/3"
                    />
                </CardContent>
            </Card>

            <!-- Table -->
            <div class="overflow-x-auto bg-white rounded-2xl shadow">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Sale</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Issued By</th>
                        <th class="px-6 py-3">Issued At</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="receipt in filteredReceipts"
                        :key="receipt.id"
                        class="border-b hover:bg-gray-50"
                    >
                        <td class="px-6 py-4">{{ receipt.reference ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <Link :href="`/sales/${receipt.sale_id}`" class="text-blue-600 hover:underline">
                                {{ receipt.sale_id }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            {{ Number(receipt.sale?.total).toLocaleString() }}
                        </td>
                        <td class="px-6 py-4">{{ receipt.issued_by ?? '—' }}</td>
                        <td class="px-6 py-4">
                            {{ new Date(receipt.issued_at).toLocaleString() }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a :href="`/receipts/${receipt.id}`">
                                <Button title="Print" variant="outline" size="sm">
                                    <PrinterIcon class="w-4 h-4" />
                                </Button>
                            </a>
                                <Button title="Cancel" variant="destructive" size="sm" @click="$inertia.delete(`/receipts/${receipt.id}`)">
                                    <DeleteIcon class="w-4 h-4" />
                                </Button>
                        </td>
                    </tr>
                    <tr v-if="filteredReceipts.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No receipts found.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
