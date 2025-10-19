<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
                <div class="container mx-auto px-4 py-4 lg:py-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Create New Sale</h1>
                        <p class="text-gray-500 text-sm mt-1">Fill in the details below to create a new sale</p>
                    </div>
                    <Button
                        variant="outline"
                        size="lg"
                        class="gap-2 rounded-lg border-2"
                        @click="toggleNewCustomerModal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Customer
                    </Button>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- MAIN CONTENT -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Selection -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Customer Details</h2>
                                    <p class="text-xs text-gray-500">Select or add a customer</p>
                                </div>
                            </div>

                            <Label for="customer-select" class="text-sm font-semibold text-gray-700 block mb-2">
                                Customer
                            </Label>
                            <Select :value="searchableCustomers[0]?.id" v-model="form.customer_id">
                                <SelectTrigger id="customer-select" class="w-full h-11 rounded-lg border-gray-200">
                                    <SelectValue placeholder="Search or select a customer" />
                                </SelectTrigger>
                                <SelectContent>
                                    <div class="p-2 border-b border-gray-100">
                                        <Input
                                            type="text"
                                            placeholder="Search customer..."
                                            v-model="searchTerm"
                                            class="w-full h-9"
                                            @keydown.stop
                                        />
                                    </div>
                                    <SelectGroup>
                                        <SelectLabel class="text-xs font-semibold text-gray-500">Available Customers</SelectLabel>
                                        <SelectItem
                                            v-for="c in searchableCustomers"
                                            :key="c.id"
                                            :value="c.id"
                                            class="cursor-pointer"
                                        >
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                {{ c.name }}
                                            </div>
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.customer_id" />
                        </div>

                        <!-- Sale Items Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m0 0l-8-4m0 0v10l8 4v-10m0 0v10l8-4v-10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Sale Items</h3>
                                        <p class="text-xs text-gray-600">{{ form.items.length }} item(s)</p>
                                    </div>
                                </div>
                                <Button size="sm" @click="addItem" class="gap-2 rounded-lg bg-blue-600 hover:bg-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Item
                                </Button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <th class="py-3 px-4">#</th>
                                        <th class="py-3 px-4">Item</th>
                                        <th class="py-3 px-4">Type</th>
                                        <th class="py-3 px-4 w-20 text-center">Qty</th>
                                        <th class="py-3 px-4 w-24 text-right">Price</th>
                                        <th class="py-3 px-4 w-24 text-right">Subtotal</th>
                                        <th class="py-3 px-4 w-12 text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="(item, index) in form.items"
                                        :key="index"
                                        class="hover:bg-blue-50 transition-colors group"
                                    >
                                        <td class="py-3 px-4 text-gray-500 font-medium">{{ index + 1 }}</td>

                                        <!-- Item Select -->
                                        <td class="py-3 px-4">
                                            <Select v-model="item.stock_id">
                                                <SelectTrigger class="w-full h-9 rounded-lg text-sm">
                                                    <SelectValue placeholder="Select Product" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <div class="p-2 border-b border-gray-100">
                                                        <Input
                                                            type="text"
                                                            placeholder="Search..."
                                                            v-model="search"
                                                            class="w-full h-8"
                                                            @keydown.stop
                                                        />
                                                    </div>
                                                    <SelectGroup>
                                                        <SelectLabel class="text-xs font-semibold">Available Stock</SelectLabel>
                                                        <SelectItem
                                                            v-for="s in searchableItems"
                                                            :key="s.id"
                                                            :value="s.id"
                                                            :disabled="isExpired(s) || s.quantity <= 0"
                                                        >
                                                            <div class="flex flex-col gap-1">
                                                                    <span :class="{
                                                                        'text-red-600 font-semibold': isExpired(s),
                                                                        'text-amber-600 font-semibold': !isExpired(s) && isLow(s),
                                                                    }">
                                                                        {{ s.stockable.name }} {{ s.stockable.brand ? `- ${s.stockable.brand}` : '' }}
                                                                        <span v-if="s.unit?.unit_name" class="text-gray-500">({{ s.unit.unit_name }})</span>
                                                                    </span>
                                                                <span :class="{
                                                                        'text-xs text-red-500': isExpired(s),
                                                                        'text-xs text-amber-500': !isExpired(s) && isLow(s),
                                                                        'text-xs text-gray-400': !isExpired(s) && !isLow(s),
                                                                    }">
                                                                        {{ isExpired(s) ? '⚠ Expired' : isLow(s) ? `⚠ Low (${s.quantity})` : `✓ Stock: ${s.quantity}` }}
                                                                    </span>
                                                            </div>
                                                        </SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </td>

                                        <!-- Sale Type -->
                                        <td class="py-3 px-4">
                                            <Select v-model="item.sale_type">
                                                <SelectTrigger class="w-full h-9 rounded-lg text-sm">
                                                    <SelectValue placeholder="Type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectItem value="retail" class="cursor-pointer">
                                                                <span class="flex items-center gap-2">
                                                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                                    Retail
                                                                </span>
                                                        </SelectItem>
                                                        <SelectItem value="wholesale" class="cursor-pointer">
                                                                <span class="flex items-center gap-2">
                                                                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                                                    Wholesale
                                                                </span>
                                                        </SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </td>

                                        <!-- Quantity -->
                                        <td class="py-3 px-4">
                                            <Input
                                                type="number"
                                                v-model.number="item.quantity"
                                                min="1"
                                                class="w-full h-9 rounded-lg text-center"
                                            />
                                        </td>

                                        <!-- Price -->
                                        <td class="py-3 px-4">
                                            <Input
                                                type="number"
                                                v-model.number="item.price"
                                                min="0"
                                                class="w-full h-9 rounded-lg text-right"
                                            />
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="py-3 px-4 text-right font-bold text-gray-900">
                                            {{ (item.quantity * item.price).toFixed(2) }}
                                        </td>

                                        <!-- Delete -->
                                        <td class="py-3 px-4 text-center">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                title="Remove item"
                                                class="opacity-0 group-hover:opacity-100 text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                                @click="removeItem(index)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </Button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div v-if="form.items.length === 0" class="text-center py-12">
                                    <p class="text-gray-500 text-sm">No items added yet. Click "Add Item" to get started.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SIDEBAR: Payment Summary -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24 space-y-4">
                            <!-- Total Card -->
                            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
                                <p class="text-sm font-semibold opacity-90 mb-2">Total Amount</p>
                                <h3 class="text-4xl font-bold">{{ form.total.toFixed(2) }}</h3>
                            </div>

                            <!-- Amount Paid -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <Label for="paid" class="text-sm font-semibold text-gray-700 block mb-3">
                                    Amount Paid
                                </Label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 font-semibold">$</span>
                                    <Input
                                        id="paid"
                                        type="number"
                                        v-model.number="form.paid"
                                        min="0"
                                        :max="form.total"
                                        class="text-xl font-bold pl-8 rounded-lg h-12"
                                    />
                                </div>
                            </div>

                            <!-- Balance Status -->
                            <div
                                :class="[
                                    'rounded-xl shadow-sm border-2 p-6 transition-all',
                                    form.balance === 0
                                        ? 'bg-green-50 border-green-300 text-green-700'
                                        : form.balance > 0
                                        ? 'bg-amber-50 border-amber-300 text-amber-700'
                                        : 'bg-blue-50 border-blue-300 text-blue-700'
                                ]"
                            >
                                <p class="text-xs font-semibold uppercase tracking-wider opacity-75 mb-1">
                                    {{ form.balance === 0 ? 'Status' : form.balance > 0 ? 'Outstanding' : 'Overpaid' }}
                                </p>
                                <p class="text-3xl font-bold">{{ Math.abs(form.balance).toFixed(2) }}</p>
                                <p class="text-xs mt-2 opacity-75">
                                    {{ form.balance === 0 ? '✓ Paid in full' : form.balance > 0 ? '⚠ Payment due' : '✓ Excess paid' }}
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <Button
                                :disabled="form.processing || form.total <= 0 || !form.customer_id"
                                @click="form.post('/sales')"
                                class="w-full h-12 text-base font-semibold bg-blue-600 hover:bg-blue-700 rounded-lg gap-2"
                            >
                                <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                {{ form.processing ? 'Submitting...' : 'Complete Sale' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Customer Modal -->
        <div v-if="showNewCustomerModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm transform transition-all">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
                    <h2 class="text-xl font-bold text-white">Add New Customer</h2>
                    <Button variant="ghost" size="icon" @click="toggleNewCustomerModal" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <Label for="new-customer-name" class="text-sm font-semibold text-gray-700 block mb-2">
                            Customer Name *
                        </Label>
                        <Input
                            id="new-customer-name"
                            type="text"
                            v-model="customerForm.newCustomerName"
                            placeholder="Enter customer name"
                            class="rounded-lg h-10"
                        />
                    </div>
                    <div>
                        <Label for="new-customer-phone" class="text-sm font-semibold text-gray-700 block mb-2">
                            Phone Number
                        </Label>
                        <Input
                            id="new-customer-phone"
                            type="text"
                            v-model="customerForm.newCustomerPhone"
                            placeholder="Enter phone number"
                            class="rounded-lg h-10"
                        />
                    </div>
                    <div class="flex gap-3 pt-4">
                        <Button variant="outline" class="flex-1 rounded-lg h-10" @click="toggleNewCustomerModal">
                            Cancel
                        </Button>
                        <Button class="flex-1 bg-blue-600 hover:bg-blue-700 rounded-lg h-10" @click="addNewCustomer">
                            Save Customer
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, watch, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import InputError from "@/components/InputError.vue";
import axios from 'axios';

const props = defineProps({
    customers: Array,
    stocks: Array,
});

const searchTerm = ref("");
const search = ref("");
const showNewCustomerModal = ref(false);

const allCustomers = ref([...props.customers]);

const form = useForm({
    customer_id: "",
    items: [
        {
            sale_type: "retail",
            stock_id: "",
            quantity: 1,
            price: 0,
            stock: null,
        },
    ],
    total: 0,
    paid: 0,
    balance: 0,
});

onMounted(() => {
    if (allCustomers.value.length > 0 && !form.customer_id) {
        form.customer_id = allCustomers.value[0].id;
    }
});

watch(
    () => form.items.map((item) => [item.sale_type, item.stock_id]),
    () => {
        form.items.forEach((item) => {
            const stock = props.stocks.find((s) => s.id === item.stock_id);
            if (stock) {
                item.price =
                    item.sale_type === "retail"
                        ? stock.retail_price
                        : stock.wholesale_price;
                item.stock = stock;
            } else {
                item.price = 0;
                item.stock = null;
            }
        });
    },
    { immediate: true, deep: true }
);

watch(
    () => form.items.map((item) => item.quantity * item.price),
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

function addItem() {
    form.items.push({
        sale_type: "retail",
        stock_id: "",
        quantity: 1,
        price: 0,
        stock: null,
    });
}

function removeItem(index) {
    form.items.splice(index, 1);
}

const breadcrumbs = [
    { title: "Dashboard", href: "/" },
    { title: "Sales", href: "/sales" },
    { title: "New Sale", href: "/sales/create" },
];

const searchableCustomers = computed(() => {
    if (!searchTerm.value) {
        return allCustomers.value;
    }
    return allCustomers.value.filter((customer) =>
        customer.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const searchableItems = computed(() => {
    if (!search.value) {
        return props.stocks;
    }
    return props.stocks.filter((stock) =>
        stock.stockable.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

function isExpired(stock) {
    if (!stock.expire_date) return false;
    return new Date(stock.expire_date) < new Date();
}

function isLow(stock) {
    return stock.quantity <= 5;
}

const customerForm = useForm({
    newCustomerName: "",
    newCustomerPhone: "",
})

function toggleNewCustomerModal() {
    showNewCustomerModal.value = !showNewCustomerModal.value;
    if (!showNewCustomerModal.value) {
        customerForm.newCustomerName = '';
        customerForm.newCustomerPhone = '';
    }
}

function addNewCustomer() {
    if (customerForm.newCustomerName.trim() === '') {
        return;
    }

    axios.post('/api/customers', {
        name: customerForm.newCustomerName,
        phone: customerForm.newCustomerPhone,
    }).then(response => {
        allCustomers.value.push(response.data);
        form.customer_id = response.data.id;
    });

    toggleNewCustomerModal();
}
</script>
