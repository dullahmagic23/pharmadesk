<script lang="ts" setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table } from '@/components/ui/table';
import currency from '@/modules/currecyFormatter';

// Define props
interface Filters {
    search: string;
    buyer_type: string;
    date_from: string;
    date_to: string;
    min_amount: string;
    max_amount: string;
    status: string;
}

const props = defineProps<{
    sales: {
        data: any[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
    filters: Filters;
}>();

// Initialize filter state with values from the URL or default values
const filters = ref({
    search: props.filters?.search || '',
    buyer_type: props.filters?.buyer_type || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    min_amount: props.filters?.min_amount || '',
    max_amount: props.filters?.max_amount || '',
    status: props.filters?.status || '',
});

// Watch for filter changes and update URL
watch(
    filters,
    (newFilters) => {
        router.get(
            route('sales.index'),
            {
                ...newFilters,
                page: 1,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    },
    { deep: true },
);

// Clear all filters
function resetFilters() {
    filters.value = {
        search: '',
        buyer_type: '',
        date_from: '',
        date_to: '',
        min_amount: '',
        max_amount: '',
        status: '',
    };
}
</script>

<template>
    <AppLayout>
        <div class="container mx-auto p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Sales</h1>
                <Button as="a" :href="route('sales.create')">New Sale</Button>
            </div>

            <!-- Filters Section -->
            <div class="mt-6 rounded-lg bg-gray-50 p-4">
                <h2 class="mb-4 text-lg font-medium">Filters</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <!-- Search Field -->
                    <div>
                        <Input v-model="filters.search" placeholder="Search by invoice number, buyer name..." class="w-full" />
                    </div>

                    <!-- Buyer Type Filter -->
                    <div>
                        <Select v-model="filters.buyer_type">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Filter by buyer type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Buyer Types</SelectLabel>
                                    <SelectLabel>All Types</SelectLabel>
                                    <SelectItem value="Customer">Customer</SelectItem>
                                    <SelectItem value="Patient">Patient</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <Select v-model="filters.status">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Filter by status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Status</SelectLabel>
                                    <SelectLabel>All Statuses</SelectLabel>
                                    <SelectItem value="paid">Paid</SelectItem>
                                    <SelectItem value="partial">Partially Paid</SelectItem>
                                    <SelectItem value="unpaid">Unpaid</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Date Range Filters -->
                    <div>
                        <Input v-model="filters.date_from" type="date" placeholder="From Date" class="w-full" />
                    </div>

                    <div>
                        <Input v-model="filters.date_to" type="date" placeholder="To Date" class="w-full" />
                    </div>

                    <!-- Amount Range Filters -->
                    <div class="flex space-x-2">
                        <Input v-model="filters.min_amount" type="number" placeholder="Min Amount" class="w-full" />
                        <Input v-model="filters.max_amount" type="number" placeholder="Max Amount" class="w-full" />
                    </div>
                </div>

                <!-- Reset Filters -->
                <div class="mt-4 flex justify-end">
                    <Button variant="outline" @click="resetFilters">Reset Filters</Button>
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
                        <tr class="my-1" v-for="sale in sales.data" :key="sale.id">
                            <td>{{ sale.created_at }}</td>
                            <td>{{ sale.buyer_name }}</td>
                            <td>{{ sale.items_count }}</td>
                            <td>{{ currency(sale.total) }}</td>
                            <td>{{ currency(sale.paid) }}</td>
                            <td>{{ currency(sale.balance) }}</td>
                            <td>
                                <span
                                    :class="{
                                        'rounded px-2 py-1 text-xs font-medium': true,
                                        'bg-green-100 text-green-800': sale.status === 'paid',
                                        'bg-yellow-100 text-yellow-800': sale.status === 'partial',
                                        'bg-red-100 text-red-800': sale.status === 'unpaid',
                                    }"
                                >
                                    {{ sale.status === 'paid' ? 'Paid' : sale.status === 'partial' ? 'Partially Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <Button as="a" :href="route('sales.show', sale.id)" variant="ghost" size="sm"> View </Button>
                                    <Button as="a" v-if="sale.status !== 'paid'" :href="route('sales.add-payments', sale.id)" size="sm" variant="default">Add payment</Button>
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
                            <Button
                                v-for="(link, i) in sales.links"
                                :key="i"
                                :disabled="!link.url || link.active"
                                :variant="link.active ? 'default' : 'outline'"
                                @click="router.get(link.url)"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
