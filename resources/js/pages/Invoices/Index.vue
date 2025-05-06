<template>
    <AppLayout title="Invoices">
        <div class="container p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl leading-tight font-semibold text-gray-800">Invoices</h2>
                <Link :href="route('invoices.create')" class="btn btn-primary">New Invoice</Link>
            </div>
            <div class="rounded bg-white p-4 shadow">
                <table class="min-w-full table-auto border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Patient</th>
                            <th class="px-4 py-2 text-left">Issued At</th>
                            <th class="px-4 py-2 text-left">Total Amount</th>
                            <th class="px-4 py-2 text-left">Balance</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-t">
                            <td class="px-4 py-2">{{ invoice.patient?.first_name }} {{ invoice.patient?.last_name }}</td>
                            <td class="px-4 py-2">{{ formatDate(invoice.invoice_date) }}</td>
                            <td class="px-4 py-2 capitalize">{{ invoice.total }}</td>
                            <td class="px-4 py-2 capitalize">{{ invoice.balance }}</td>
                            <td class="px-4 py-2 capitalize">{{ invoice.status }}</td>
                            <td class="px-4 py-2">
                                <Link :href="`/invoices/${invoice.id}`" class="text-blue-500 hover:underline">View</Link>
                            </td>
                        </tr>
                        <tr v-if="invoices.data.length === 0">
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    </tbody>
                </table>

                <Pagination :links="invoices.links" class="mt-4"  items-per-page="15"/>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Pagination } from '@/Components/ui/pagination';
import { Link } from '@inertiajs/vue3';

defineProps({
    invoices: Object,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}
</script>
