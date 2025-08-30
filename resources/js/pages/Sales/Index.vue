<script lang="ts" setup>
import { computed, ref, watch } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import currency from '@/modules/currecyFormatter';
import { PrinterIcon, FileTextIcon, FileDownIcon, SearchIcon, FilterIcon, DeleteIcon } from 'lucide-vue-next';
import axios from 'axios';


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
});

const deleteForm = useForm({
    sale_id: ''
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
});

// Watch the form to trigger a new request on filter change
watch(form, () => {
    router.get(
        route('sales.index'),
        {
            buyer_name: form.buyer_name,
            start_date: form.start_date,
            end_date: form.end_date,
        },
        { preserveState: true, replace: true }
    );
}, { deep: true });

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Sales', href: route('sales.index') }
];

const cancelSales = (id: string) => {
    deleteForm.sale_id = id;
    if (confirm('Are you sure you want to cancel this sale?')) {
        router.get('/api/sales/' + id);
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8">
            <div class="flex items-center justify-between flex-wrap mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Sales History</h1>
                <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <Link :href="route('sales.create')">
                        <Button class="bg-blue-600 hover:bg-blue-700 transition-colors">
                            <span class="mr-2">+</span> New Sale
                        </Button>
                    </Link>
                    <div class="hidden sm:flex space-x-2">
                        <a :href="`/sales/export/pdf?start_date=${form.start_date}&end_date=${form.end_date}`">
                            <Button variant="outline" class="flex items-center">
                                <FileTextIcon class="w-4 h-4 mr-2" /> PDF
                            </Button>
                        </a>
                        <a :href="`/sales/export/excel?start_date=${form.start_date}&end_date=${form.end_date}`">
                            <Button variant="outline" class="flex items-center">
                                <FileDownIcon class="w-4 h-4 mr-2" /> Excel
                            </Button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border mb-8">
                <h2 class="flex items-center text-lg font-semibold text-gray-700 mb-4">
                    <FilterIcon class="w-5 h-5 mr-2" /> Filter Sales
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <Input v-model="form.buyer_name" placeholder="Search by buyer name..." class="w-full h-10" />
                    </div>
                    <div>
                        <Input v-model="form.start_date" type="date" placeholder="From Date" class="w-full h-10" />
                    </div>
                    <div>
                        <Input v-model="form.end_date" type="date" placeholder="To Date" class="w-full h-10" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <th class="py-2 px-4 text-left">Date</th>
                            <th class="py-2 px-4 text-left">Buyer</th>
                            <th class="py-2 px-4 text-left">Items</th>
                            <th class="py-2 px-4 text-left">Total</th>
                            <th class="py-2 px-4 text-left">Paid</th>
                            <th class="py-2 px-4 text-left">Balance</th>
                            <th class="py-2 px-4 text-center">Status</th>
                            <th class="py-2 px-4 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="filteredSales.length === 0">
                            <td colspan="8" class="text-center py-4 text-gray-500 italic">No sales found matching your filters.</td>
                        </tr>
                        <tr v-else v-for="sale in filteredSales" :key="sale.id" class="border-b last:border-b-0 hover:bg-gray-100 transition-colors">
                            <td class="py-2 px-4">{{ new Date(sale.created_at).toLocaleDateString() }}</td>
                            <td class="py-2 px-4 font-medium text-gray-800">{{ sale.buyer_name }}</td>
                            <td class="py-2 px-4">{{ sale.items_count }}</td>
                            <td class="py-2 px-4 font-bold text-gray-900">{{ currency(sale.total) }}</td>
                            <td class="py-2 px-4 text-green-600 font-medium">{{ currency(sale.paid) }}</td>
                            <td class="py-2 px-4 text-red-600 font-medium">{{ currency(sale.balance) }}</td>
                            <td class="py-2 px-4 text-center">
                                    <span :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-semibold',
                                        {
                                            'bg-green-100 text-green-800': sale.status === 'paid',
                                            'bg-yellow-100 text-yellow-800': sale.status === 'partial',
                                            'bg-red-100 text-red-800': sale.status === 'unpaid',
                                        },
                                    ]">
                                        {{ sale.status === 'paid' ? 'Paid' : sale.status === 'partial' ? 'Partial' : 'Unpaid' }}
                                    </span>
                            </td>
                            <td class="py-2 px-4 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <Link :href="route('sales.show', sale.id)">
                                        <Button variant="ghost" size="sm" class="hover:bg-gray-200">View</Button>
                                    </Link>
                                    <Link v-if="sale.status !== 'paid'" :href="route('sales.add-payments', sale.id)">
                                        <Button size="sm" variant="outline" class="text-blue-600 hover:text-blue-700">Pay</Button>
                                    </Link>
                                    <a :href="route('sales.receipt', sale.id)" target="_blank">
                                        <Button size="icon" variant="ghost" class="hover:bg-gray-200">
                                            <PrinterIcon class="w-5 h-5 text-gray-500 hover:text-gray-700" />
                                        </Button>
                                    </a>
                                    <DeleteIcon class="w-4 h-4 mr-2" @click="cancelSales(sale.id)"/>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="sales.links.length > 3" class="mt-6 flex items-center justify-between flex-wrap">
                <div class="text-sm text-gray-600 mb-2 md:mb-0">
                    Showing <span class="font-bold">{{ sales.from }}</span> to <span class="font-bold">{{ sales.to }}</span> of <span class="font-bold">{{ sales.total }}</span> results
                </div>
                <div class="flex space-x-1 flex-wrap">
                    <Button
                        v-for="(link, i) in sales.links"
                        :key="i"
                        :disabled="!link.url"
                        :variant="link.active ? 'default' : 'outline'"
                        @click="router.get(link.url)"
                        v-html="link.label"
                        class="min-w-[40px] px-3 py-1"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
