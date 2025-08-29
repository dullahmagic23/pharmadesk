<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">New Sale</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg border">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Customer Details</h2>
                        <Label for="customer-select" class="block mb-2 font-medium">Select Customer</Label>
                        <Select v-model="form.customer_id">
                            <SelectTrigger id="customer-select" class="w-full">
                                <SelectValue placeholder="Search or select a customer" />
                            </SelectTrigger>
                            <SelectContent>
                                <div class="p-2">
                                    <Input
                                        type="text"
                                        placeholder="Search customer..."
                                        v-model="searchTerm"
                                        class="w-full"
                                        @keydown.stop
                                    />
                                </div>
                                <SelectGroup>
                                    <SelectLabel>Customers</SelectLabel>
                                    <SelectItem v-for="c in searchableCustomers" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg border">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Sale Items</h2>
                        <div class="overflow-x-auto">
                            <Table class="w-full min-w-[700px] text-sm">
                                <thead>
                                <tr class="text-left font-bold text-gray-600 border-b-2 border-gray-200">
                                    <th class="py-2 px-4">Item</th>
                                    <th class="py-2 px-4">Sale Type</th>
                                    <th class="py-2 px-4 w-20">Qty</th>
                                    <th class="py-2 px-4 w-32">Price</th>
                                    <th class="py-2 px-4 w-32">Subtotal</th>
                                    <th class="py-2 px-4 w-20"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in form.items" :key="index" class="border-b hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4">
                                        <Select v-model="item.stock_id">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Medicine/Product" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <div class="p-2">
                                                    <Input
                                                        type="text"
                                                        placeholder="Search..."
                                                        v-model="search"
                                                        class="w-full"
                                                        @keydown.stop
                                                    />
                                                </div>
                                                <SelectGroup>
                                                    <SelectLabel>Available Stock</SelectLabel>
                                                    <SelectItem
                                                        v-for="s in searchableItems"
                                                        :key="s.id"
                                                        :value="s.id"
                                                        :disabled="isExpired(s) || s.quantity <= 0"
                                                    >
                                                        <div class="flex items-center space-x-2">
                                                                <span
                                                                    :class="{
                                                                        'text-red-600 font-medium': isExpired(s),
                                                                        'text-yellow-600 font-medium': !isExpired(s) && isLow(s),
                                                                    }"
                                                                >
                                                                    {{ s.stockable.name }} <span v-if="s.unit?.unit_name">({{ s.unit.unit_name }})</span>
                                                                </span>
                                                            <span v-if="isExpired(s)" class="text-xs text-red-500 italic">Expired</span>
                                                            <span v-else-if="isLow(s)" class="text-xs text-orange-500 italic">Low ({{ s.quantity }})</span>
                                                            <span v-else class="text-xs text-gray-500 italic">In stock: {{ s.quantity }}</span>
                                                        </div>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </td>
                                    <td class="py-3 px-4">
                                        <Select v-model="item.sale_type">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Sale Type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectLabel>Sale Type</SelectLabel>
                                                    <SelectItem value="retail">Retail</SelectItem>
                                                    <SelectItem value="wholesale">Wholesale</SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </td>
                                    <td class="py-3 px-4">
                                        <Input type="number" v-model.number="item.quantity" min="1" class="w-full" />
                                    </td>
                                    <td class="py-3 px-4">
                                        <Input type="number" v-model.number="item.price" min="0" :value="item.price.toFixed(2)" class="w-full" />
                                    </td>
                                    <td class="py-3 px-4 font-bold text-gray-800">
                                        {{ (item.quantity * item.price).toFixed(2) }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <Button variant="ghost" size="sm" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 112 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                            </svg>
                                        </Button>
                                    </td>
                                </tr>
                                </tbody>
                            </Table>
                        </div>
                        <Button class="mt-4" @click="addItem">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Item
                        </Button>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 rounded-xl shadow-lg border">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Payment Summary</h2>
                        <div class="space-y-4">
                            <div>
                                <Label class="font-medium text-lg">Total</Label>
                                <Input :value="form.total.toFixed(2)" disabled class="text-xl font-bold bg-gray-100 mt-1" />
                            </div>
                            <div>
                                <Label for="paid" class="font-medium text-lg">Amount Paid</Label>
                                <Input id="paid" type="number" v-model.number="form.paid" min="0" class="text-xl font-bold text-green-700 mt-1" />
                            </div>
                            <div>
                                <Label class="font-medium text-lg">Balance</Label>
                                <Input :value="form.balance.toFixed(2)" disabled class="text-xl font-bold bg-gray-100 mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg border">
                        <Button
                            :disabled="form.processing || form.total <= 0"
                            @click="form.post('/sales')"
                            class="w-full h-12 text-lg font-semibold bg-blue-600 hover:bg-blue-700 transition-colors"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit Sale' }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table } from '@/components/ui/table';

const props = defineProps({
    customers: Array,
    stocks: Array
});

const searchTerm = ref('');
const search = ref('');

const form = useForm({
    customer_id: '',
    items: [
        {
            sale_type: 'retail',
            stock_id: '',
            quantity: 1,
            price: 0,
            stock: null
        }
    ],
    total: 0,
    paid: 0,
    balance: 0
});

// --- price handling ---
const computePrice = computed(() => {
    return form.items.map(item => {
        const stock = props.stocks.find(s => s.id === item.stock_id);
        if (!stock) return 0;
        return item.sale_type === 'retail' ? stock.retail_price : stock.wholesale_price;
    });
});

watch(
    () => form.items.map(item => [item.sale_type, item.stock_id]),
    () => {
        form.items.forEach((item) => {
            const stock = props.stocks.find(s => s.id === item.stock_id);
            if (stock) {
                item.price = item.sale_type === 'retail' ? stock.retail_price : stock.wholesale_price;
                item.stock = stock;
            } else {
                item.price = 0;
                item.stock = null;
            }
        });
    },
    { immediate: true, deep: true }
);

// --- total & balance ---
watch(
    () => form.items.map(item => item.quantity * item.price),
    (subtotals) => {
        form.total = subtotals.reduce((sum, val) => sum + Number(val), 0);
    },
    { immediate: true }
);

watch(
    () => [form.total, form.paid],
    ([total, paid]) => {
        form.balance = total - paid;
    },
    { immediate: true }
);

// helpers
function addItem() {
    form.items.push({
        sale_type: 'retail',
        stock_id: '',
        quantity: 1,
        price: 0,
        stock: null
    });
}

function removeItem(index) {
    if (confirm("Remove the item?")) {
        form.items.splice(index, 1);
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/' },
    { title: 'Sales', href: '/sales' },
    { title: 'New Sale', href: '/sales/create' }
];

// --- Search filters ---
const searchableCustomers = computed(() => {
    if (!searchTerm.value) {
        return props.customers;
    }
    return props.customers.filter(customer =>
        customer.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const searchableItems = computed(() => {
    if (!search.value) {
        return props.stocks;
    }
    return props.stocks.filter(stock =>
        stock.stockable.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

// --- helpers for expiry and low quantity ---
function isExpired(stock) {
    if (!stock.expire_date) return false;
    return new Date(stock.expire_date) < new Date();
}

function isLow(stock) {
    return stock.quantity <= 5; // threshold configurable
}
</script>
