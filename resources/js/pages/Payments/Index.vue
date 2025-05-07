<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'

 defineProps<{
    payments: Array<any>
}>()
</script>

<template>
    <Head title="Payments" />
    <AppLayout>
        <div class="container p-6 bg-white rounded-xl shadow mx-auto">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold">Payments</h1>
                <Link :href="route('payments.create')">
                    <Button>Add Payment</Button>
                </Link>
            </div>
            <table class="w-full table-auto border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-2 py-1 text-left">Invoice #</th>
                    <th class="px-2 py-1 text-left">Patient</th>
                    <th class="px-2 py-1 text-left">Amount</th>
                    <th class="px-2 py-1 text-left">Method</th>
                    <th class="px-2 py-1 text-left">Paid At</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="payment in payments" :key="payment.id" >
                    <td class="border px-2 py-1">{{ payment.invoice?.invoice_number }}</td>
                    <td class="border px-2 py-1">
                        {{ payment.invoice?.patient?.first_name }} {{ payment.invoice?.patient?.last_name }}
                    </td>
                    <td class="border px-2 py-1">{{ Number(payment.amount).toLocaleString(undefined, {minimumFractionDigits:2}) }}</td>
                    <td class="border px-2 py-1 capitalize">{{ payment.method }}</td>
                    <td class="border px-2 py-1">{{ payment.paid_at }}</td>
                </tr>
                <tr v-if="!payments.length">
                    <td colspan="5" class="py-3 text-center text-gray-500">No payments found.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
