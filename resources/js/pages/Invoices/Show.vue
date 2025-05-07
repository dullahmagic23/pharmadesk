<template>
    <AppLayout>
        <div class="container p-6">
            <div class="flex justify-between">
                <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                    Invoice Details
                </h2>
                <a :href="route('invoices.print',invoice.id)" class="flex ">
                    <PrinterIcon />
                    Print
                </a>
            </div>

            <div class="bg-white rounded shadow p-6">
                <!-- Invoice Metadata -->
                <div class="mb-4 space-y-2">
                    <div><strong>Patient:</strong> {{ fullName(invoice.patient) }}</div>
                    <div><strong>Issued At:</strong> {{ formatDate(invoice.invoice_date) }}</div>
                    <div><strong>Status:</strong> <span class="capitalize">{{ invoice.status }}</span></div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Item</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-right">Qty</th>
                            <th class="px-4 py-2 text-right">Unit Price</th>
                            <th class="px-4 py-2 text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in invoice.items" :key="item.id" class="border-t">
                            <td class="px-4 py-2">{{ getItemName(item) }}</td>
                            <td class="px-4 py-2">{{ getType(item.billable_type) }}</td>
                            <td class="px-4 py-2 text-right">{{ item.quantity }}</td>
                            <td class="px-4 py-2 text-right">{{ currency(item.unit_price) }}</td>
                            <td class="px-4 py-2 text-right">{{ currency(item.total_price) }}</td>
                        </tr>
                        <tr v-if="invoice.items.length === 0">
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">No items in this invoice.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Total Sum -->
                <div class="mt-4 text-right font-semibold">
                    Total: {{ currency(invoice.total) }}
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'
import currency from '../../modules/currecyFormatter.js';
import { Link } from '@inertiajs/vue3';
import {PrinterIcon} from 'lucide-vue-next';

const props = defineProps({
    invoice: Object
})

const fullName = (patient) => {
    return `${patient.first_name ?? ''} ${patient.last_name ?? ''}`.trim()
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString()
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0)
}

const getItemName = (item) => {
    return item.billable?.name || item.billable?.title || '—'
}

const getType = (fullClassName) => {
    return fullClassName.split('\\').pop()
}

const grandTotal = computed(() => {
    return props.invoice.items.reduce((sum, item) => sum + item.total_price, 0)
})
</script>
