<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto px-4 py-8 space-y-8">
            <!-- Page Title -->
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-800">🛒 New Sale</h1>
                <Button variant="outline" size="sm" class="rounded-lg" @click="toggleNewCustomerModal">
                    + New Customer
                </Button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT SIDE -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Customer Details -->
                    <div class="bg-white p-6 rounded-xl shadow-md border">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">
                            Customer Details
                        </h2>
                        <Label for="customer-select" class="mb-2 font-medium block">
                            Select Customer
                        </Label>
                        <Select :value="searchableCustomers[0]?.id" v-model="form.customer_id">
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
                                    <SelectItem aria-selected="true" :value="searchableCustomers[0].id">{{searchableCustomers[0].name}}</SelectItem>
                                    <SelectItem
                                        v-for="c in searchableCustomers"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.customer_id" />
                    </div>

                    <!-- Sale Items -->
                    <div class="bg-white p-6 rounded-xl shadow-md border">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-700">Sale Items</h2>
                            <Button size="sm" @click="addItem" class="flex items-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-2"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Add Item
                            </Button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="sticky top-0 bg-gray-50 shadow-sm">
                                <tr
                                    class="text-left font-semibold text-gray-600 border-b border-gray-200"
                                >
                                    <th class="py-2 px-4">Item</th>
                                    <th class="py-2 px-4">Sale Type</th>
                                    <th class="py-2 px-4 w-20">Qty</th>
                                    <th class="py-2 px-4 w-32">Price</th>
                                    <th class="py-2 px-4 w-32">Subtotal</th>
                                    <th class="py-2 px-4 w-16 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="(item, index) in form.items"
                                    :key="index"
                                    class="border-b hover:bg-gray-50 transition"
                                >
                                    <!-- Item Select -->
                                    <td class="py-3 px-4">
                                        <Select v-model="item.stock_id">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Select Product" />
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
                                                        <div class="flex flex-col">
                                <span
                                    :class="{
                                    'text-red-600 font-medium': isExpired(s),
                                    'text-yellow-600 font-medium':
                                      !isExpired(s) && isLow(s),
                                  }"
                                >
                                  {{ s.stockable.name }} - {{s.stockable.brand}}
                                  <span v-if="s.unit?.unit_name"
                                  >({{ s.unit.unit_name }})</span
                                  >
                                </span>
                                                            <span
                                                                v-if="isExpired(s)"
                                                                class="text-xs text-red-500 italic"
                                                            >Expired</span
                                                            >
                                                            <span
                                                                v-else-if="isLow(s)"
                                                                class="text-xs text-orange-500 italic"
                                                            >Low ({{ s.quantity }})</span
                                                            >
                                                            <span
                                                                v-else
                                                                class="text-xs text-gray-500 italic"
                                                            >In stock: {{ s.quantity }}</span
                                                            >
                                                        </div>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </td>

                                    <!-- Sale Type -->
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

                                    <!-- Qty -->
                                    <td class="py-3">
                                        <Input
                                            type="number"
                                            v-model.number="item.quantity"
                                            min="1"
                                            class="w-full"
                                        />
                                    </td>

                                    <!-- Price -->
                                    <td class="py-3">
                                        <Input
                                            type="number"
                                            v-model.number="item.price"
                                            min="0"
                                            class="w-full"
                                        />
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="py-3">
                      <span
                          class="px-2 py-1 rounded-lg bg-gray-100 font-semibold text-gray-700"
                      >
                        {{ (item.quantity * item.price).toFixed(2) }}
                      </span>
                                    </td>

                                    <!-- Delete -->
                                    <td class="py-3 text-center">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            title="Remove"
                                            class="text-red-500 hover:bg-red-50 rounded-full"
                                            @click="removeItem(index)"
                                        >
                                            <DeleteIcon class="h-6 w-6" />
                                        </Button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Payment Summary -->
                    <div class="bg-white p-6 rounded-xl shadow-md border space-y-4">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">
                            Payment Summary
                        </h2>

                        <div class="p-3 rounded-xl bg-blue-50 text-blue-700 font-bold text-lg">
                            Total: {{ form.total.toFixed(2) }}
                        </div>

                        <div>
                            <Label for="paid" class="font-medium text-lg">Amount Paid</Label>
                            <Input
                                id="paid"
                                type="number"
                                v-model.number="form.paid"
                                min="0"
                                class="text-xl font-bold text-green-700 mt-1"
                                :value="form.total.toFixed(2)"
                            />
                        </div>

                        <div
                            :class="[
                'p-3 rounded-xl font-bold text-lg',
                form.balance === 0
                  ? 'bg-green-50 text-green-700'
                  : form.balance > 0
                  ? 'bg-orange-50 text-orange-700'
                  : 'bg-red-50 text-red-700',
              ]"
                        >
                            Balance: {{ form.balance.toFixed(2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Submit -->
        <div class="sticky bottom-0 bg-white border-t shadow-lg p-4">
            <Button
                :disabled="form.processing || form.total <= 0"
                @click="form.post('/sales')"
                class="w-full h-12 text-lg font-semibold bg-blue-600 hover:bg-blue-700 flex items-center justify-center"
            >
                <svg
                    v-if="form.processing"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-2 animate-spin"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v8z"
                    />
                </svg>
                {{ form.processing ? "Submitting..." : "Submit Sale" }}
            </Button>
        </div>

        <!-- New Customer Modal -->
        <div v-if="showNewCustomerModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Add New Customer</h2>
                    <Button variant="ghost" size="icon" @click="toggleNewCustomerModal" class="rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </Button>
                </div>
                <div class="space-y-4">
                    <div>
                        <Label for="new-customer-name" class="font-medium">Customer Name</Label>
                        <Input id="new-customer-name" type="text" v-model="customerForm.newCustomerName" class="mt-1" />
                    </div>
                    <div>
                        <Label for="new-customer-phone" class="font-medium">Phone No.</Label>
                        <Input id="new-customer-phone" type="text" v-model="customerForm.newCustomerPhone" class="mt-1" />
                    </div>
                    <Button class="w-full bg-blue-600 hover:bg-blue-700" @click="addNewCustomer">Save Customer</Button>
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
import { DeleteIcon } from 'lucide-vue-next'
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

// ✅ Ensure first customer is pre-selected on mount
onMounted(() => {
    if (allCustomers.value.length > 0 && !form.customer_id) {
        form.customer_id = allCustomers.value[0].id;
    }
});

// --- price handling ---
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

// --- total & balance ---
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

// helpers
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

// --- Search filters ---
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

// --- helpers for expiry and low quantity ---
function isExpired(stock) {
    if (!stock.expire_date) return false;
    return new Date(stock.expire_date) < new Date();
}

function isLow(stock) {
    return stock.quantity <= 5; // threshold configurable
}

// --- New Customer Modal Functions ---
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

        // ✅ Auto-select the newly added customer
        form.customer_id = response.data.id;
    });

    toggleNewCustomerModal();
}
</script>
