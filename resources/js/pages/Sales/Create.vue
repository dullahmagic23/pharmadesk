<script setup>
import { computed, watch } from 'vue';
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
})

const computePrice = computed(() => {
    return form.items.map(item => {
        const stock = props.stocks.find(s => s.id === item.stock_id);
        if (!stock) return 0;
        return item.sale_type === 'retail' ? stock.retail_price : stock.wholesale_price;
    });
});

// Watch for changes in sale_type or stock_id to update item.price
watch(
    () => form.items.map(item => [item.sale_type, item.stock_id]),
    (newVals, oldVals) => {
        form.items.forEach((item, idx) => {
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

// Watch for changes in items to compute total
watch(
    () => form.items.map(item => item.quantity * item.price),
    (subtotals) => {
        form.total = subtotals.reduce((sum, val) => sum + Number(val), 0);
    },
    { immediate: true }
);

// Watch for changes in total or paid to compute balance
watch(
    () => [form.total, form.paid],
    ([total, paid]) => {
        form.balance = total - paid;
    },
    { immediate: true }
);

// Add and remove item helpers
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mt-4 space-y-6 p-6">
            <h1 class="text-2xl font-semibold">New Sale</h1>
            <!-- Buyer -->
            <div>
                <Label>Customer</Label>
                <Select v-model="form.customer_id">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select Buyer" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Customers</SelectLabel>
                            <SelectItem v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>

            <!-- Sale Items Table -->
            <div>
                <Label>Items</Label>
                <Table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th>Sale Type</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="space-x-2" v-for="(item, index) in form.items" :key="index">
                            <td>
                                <Select v-model="item.sale_type" class="mt-2">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Sale Type" />
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
                            <td>
                                <Select v-model="item.stock_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Medicine/Product" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Items</SelectLabel>
                                            <SelectItem v-for="m in stocks" :key="m.id" :value="m.id">
                                                <template v-if="m.unit">
                                                    {{ m.stockable.name }} - {{ m.unit?.unit_name }}
                                                </template>
                                                <template v-else>
                                                    {{ m.stockable.name }}
                                                </template>
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </td>
                            <td class="px-2"><Input type="number" v-model.number="item.quantity" min="1" /></td>
                            <td class="px-2"><Input type="number" v-model.number="item.price" min="0"
                                    :value="item.price" /></td>
                            <td class="px-2">{{ (item.quantity * item.price).toFixed(2) }}</td>
                            <td class="px-2"><Button variant="destructive" @click="removeItem(index)">Remove</Button>
                            </td>
                        </tr>
                    </tbody>
                </Table>
                <Button class="mt-2" @click="addItem">+ Add Item</Button>
            </div>

            <!-- Payment Section -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <Label>Total</Label>
                    <Input :value="form.total.toFixed(2)" disabled />
                </div>
                <div>
                    <Label>Paid</Label>
                    <Input type="number" v-model.number="form.paid" min="0" />
                </div>
                <div>
                    <Label>Balance</Label>
                    <Input :value="form.balance.toFixed(2)" disabled />
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <Button :disabled="form.processing" @click="form.post('/sales')">
                    {{ form.processing ? 'Submitting...' : 'Submit Sale' }}
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
