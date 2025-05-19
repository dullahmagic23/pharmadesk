<script lang="ts" setup>
import { computed, ref, watch } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table } from '@/components/ui/table';
import currency from '@/modules/currecyFormatter';

const props = defineProps<{
    sales: {
        data: any[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
}>();

const form = useForm({
    buyer_name: '',
    start_date: '',
    end_date: ''
})

const filteredSales = computed(() => {
    return props.sales.data.filter((sale) => {
        const matchesBuyerName = form.buyer_name
            ? sale.buyer_name.toLowerCase().includes(form.buyer_name.toLowerCase())
            : true;
        const matchesStartDate = form.start_date
            ? new Date(sale.created_at) >= new Date(form.start_date)
            : true;
        const matchesEndDate = form.end_date
            ? new Date(sale.created_at) <= new Date(form.end_date)
            : true;

        return matchesBuyerName && matchesStartDate && matchesEndDate;
    });
})
const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Sales', href: route('sales.index') }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Sales</h1>
                <div class="justify-end space-x-4">
                    <Link :href="route('sales.create')">
                    <Button>New Sale</Button>
                    </Link>
                    <a :href="`/sales/export/pdf?start_date=${form.start_date}&end_date=${form.end_date}`">
                        <Button variant="destructive">Export to pdf</Button>
                    </a>

                    <a :href="`/sales/export/excel?start_date=${form.start_date}&end_date=${form.end_date}`">
                        <Button variant="outline">Export to Excel</Button>
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="mt-6 rounded-lg bg-gray-50 p-4">
                <h2 class="mb-4 text-lg font-medium">Filters</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <!-- Search Field -->
                    <div>
                        <Input v-model="form.buyer_name" placeholder="Buyer name..." class="w-full" />
                    </div>
                    <div>
                        <Input v-model="form.start_date" type="date" placeholder="From Date" class="w-full" />
                    </div>

                    <div>
                        <Input v-model="form.end_date" type="date" placeholder="To Date" class="w-full" />
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="mt-6">
                <Table>
                    <thead>
                        <tr class="text-left">
                            <th>Date</th>
                            <th>Buyer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="my-1" v-for="sale in filteredSales" :key="sale.id">
                            <td>{{ sale.created_at }}</td>
                            <td>{{ sale.buyer_name }}</td>
                            <td>{{ sale.items_count }}</td>
                            <td>{{ currency(sale.total) }}</td>
                            <td>{{ currency(sale.paid) }}</td>
                            <td>{{ currency(sale.balance) }}</td>
                            <td>
                                <span :class="{
                                    'rounded px-2 py-1 text-xs font-medium': true,
                                    'bg-green-100 text-green-800': sale.status === 'paid',
                                    'bg-yellow-100 text-yellow-800': sale.status === 'partial',
                                    'bg-red-100 text-red-800': sale.status === 'unpaid',
                                }">
                                    {{ sale.status === 'paid' ? 'Paid' : sale.status === 'partial' ? 'Partially Paid' :
                                        'Unpaid' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <Link :href="route('sales.show', sale.id)">
                                    <Button variant="ghost" size="sm"> View</Button>
                                    </Link>
                                    <Link :href="route('sales.add-payments', sale.id)">
                                    <Button v-if="sale.status !== 'paid'" size="sm" variant="default">Add
                                        payment</Button>
                                    </Link>
                                    <Button as="a" :href="route('sales.receipt', sale.id)" variant="ghost">
                                        <PrinterIcon />Receipt
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="sales.data.length === 0">
                            <td colspan="9" class="py-4 text-center">No sales found matching your filters.</td>
                        </tr>
                    </tbody>
                </Table>

                <!-- Pagination -->
                <div v-if="sales.links" class="mt-4">
                    <!-- Add pagination component here based on your design system -->
                    <!-- This is just an example structure -->
                    <div class="flex items-center justify-between">
                        <div>Showing {{ sales.from }} to {{ sales.to }} of {{ sales.total }} results</div>
                        <div class="flex space-x-1">
                            <Button v-for="(link, i) in sales.links" :key="i" :disabled="!link.url || link.active"
                                :variant="link.active ? 'default' : 'outline'" @click="router.get(link.url)"
                                v-html="link.label" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
