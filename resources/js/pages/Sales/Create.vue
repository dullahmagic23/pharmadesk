<script lang="ts" setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table } from '@/components/ui/table';

interface Item {
    id: string;
    name: string;
    stock?: {
        retail_price: number;
    };
}

const props = defineProps<{
    customers: Item[];
    patients: Item[];
    medicines: Item[];
    products: Item[];
    equipment: Item[];
}>();

const form = useForm({
    buyer_type: 'Customer',
    buyer_id: null as string | null,
    items: [] as Array<{
        sellable_type: string;
        sellable_id: string | null;
        quantity: number;
        price: number;
    }>,
    total: 0,
    paid: 0,
    balance: 0,
});

function addItem() {
    form.items.push({
        sellable_type: 'Medicine',
        sellable_id: null,
        quantity: 1,
        price: 0,
    });
}

function removeItem(index) {
    form.items.splice(index, 1);
    recalculate();
}

function recalculate() {
    form.total = form.items.reduce((sum, item) => sum + item.quantity * item.price, 0);
    recalculateBalance();
}

function recalculateBalance() {
    form.balance = form.total - parseFloat(form.paid || 0);
}

function setItemPrice(item: any, index: number) {
    const { sellable_type, sellable_id } = item;

    if (!sellable_id) return;

    let selectedItem;

    switch (sellable_type) {
        case 'Medicine':
            selectedItem = props.medicines.find((m) => m.id === sellable_id);
            break;
        case 'Product':
            selectedItem = props.products.find((p) => p.id === sellable_id);
            break;
        case 'Equipment':
            selectedItem = props.equipment.find((e) => e.id === sellable_id);
            break;
    }
    if (selectedItem?.stock) {
        form.items[index].stock = selectedItem.stock;
        form.items[index].price = selectedItem.stock.retail_price;
        recalculate();
    }
}

// Watch for changes in sellable_id for any item and update price
watch(
    () => form.items.map((item) => ({ type: item.sellable_type, id: item.sellable_id })),
    (newValues, oldValues) => {
        newValues.forEach((newVal, index) => {
            // Only update if the ID changed
            if (newVal.id !== oldValues?.[index]?.id && newVal.id) {
                setItemPrice(form.items[index], index);
            }
        });
    },
    { deep: true },
);

watch(() => form.items, recalculate, { deep: true });
watch(() => form.paid, recalculateBalance);
</script>

<template>
    <AppLayout>
        <div class="container mt-4 space-y-6 p-6">
            <h1 class="text-2xl font-semibold">New Sale</h1>
            <!-- Buyer Type -->
            <div>
                <Label>Buyer Type</Label>
                <Select v-model="form.buyer_type">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select Buyer Type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Buyer Types</SelectLabel>
                            <div v-if="form.errors.buyer_type" class="mt-1 text-sm text-red-500">
                                {{ form.errors.buyer_type }}
                            </div>
                            <SelectItem value="Customer">Customer</SelectItem>
                            <SelectItem value="Patient">Patient</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>

            <!-- Buyer -->
            <div>
                <Label>Buyer</Label>
                <Select v-model="form.buyer_id">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select Buyer" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>{{ form.buyer_type }}</SelectLabel>
                            <template v-if="form.buyer_type === 'Customer'">
                                <SelectItem v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</SelectItem>
                            </template>
                            <template v-else>
                                <SelectItem v-for="p in patients" :key="p.id" :value="p.id">{{ p.name }}</SelectItem>
                            </template>
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
                            <th>Type</th>
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
                                <Select v-model="item.sellable_type">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Item Types</SelectLabel>
                                            <SelectItem value="Medicine">Medicine</SelectItem>
                                            <SelectItem value="Product">Product</SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </td>
                            <td>
                                <Select v-if="item.sellable_type === 'Medicine'" v-model="item.sellable_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Medicine" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Medicines</SelectLabel>
                                            <SelectItem v-for="m in medicines" :key="m.id" :value="m.id">{{ m.name }}</SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <Select v-else-if="item.sellable_type === 'Product'" v-model="item.sellable_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Product" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Products</SelectLabel>
                                            <SelectItem v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <Select v-else-if="item.sellable_type === 'Equipment'" v-model="item.sellable_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select Equipment" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Equipment</SelectLabel>
                                            <SelectItem v-for="e in equipment" :key="e.id" :value="e.id">{{ e.name }}</SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                            </td>
                            <td class="px-2"><Input type="number" v-model.number="item.quantity" min="1" /></td>
                            <td class="px-2"><Input type="number" v-model.number="item.price" min="0" :value="item.stock?.retail_price" /></td>
                            <td class="px-2">{{ (item.quantity * item.price).toFixed(2) }}</td>
                            <td class="px-2"><Button variant="ghost" @click="removeItem(index)">Remove</Button></td>
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
