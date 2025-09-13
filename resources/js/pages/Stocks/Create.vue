<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Add Stock" />

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg border p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Stock</h1>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <Label for="stockable_type" class="block font-medium mb-1">Stock Type</Label>
                            <Select v-model="form.stockable_type" class="w-full">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Type" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectGroup>
                                        <SelectLabel>Types</SelectLabel>
                                        <SelectItem v-for="(model, label) in stockableTypes" :key="model" :value="model">
                                            {{ label }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.stockable_type" />
                        </div>

                        <div>
                            <Label for="stockable_id" class="block font-medium mb-1">Item</Label>
                            <Select v-model="form.stockable_id" class="w-full">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Search or Select Item" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <div class="p-2">
                                        <Input
                                            type="text"
                                            placeholder="Search Items..."
                                            v-model="searchTerm"
                                            class="w-full"
                                            @keydown.stop
                                        />
                                    </div>
                                    <SelectGroup v-if="searchedItems.length">
                                        <SelectLabel>Items</SelectLabel>
                                        <SelectItem v-for="item in searchedItems" :key="item.id" :value="item.id">
                                            {{ item.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.stockable_id" />
                        </div>

                        <div v-if="form.stockable_type === 'App\\Models\\Medicine'">
                            <Label for="selected_unit" class="block font-medium mb-1">Unit</Label>
                            <Select v-model="form.selected_unit" class="w-full">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Unit" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectGroup v-if="selectedMedicineUnits.length">
                                        <SelectLabel>Units</SelectLabel>
                                        <SelectItem v-for="unit in selectedMedicineUnits" :key="unit.id" :value="unit.id">
                                            {{ unit.unit_name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.selected_unit" />
                        </div>
                         <div>
                            <Label for="buying_price" class="block font-medium mb-1">Buying Price (Tsh.)</Label>
                            <Input id="buying_price" v-model="form.buying_price" type="number" min="0" step="0.01" placeholder="e.g., 15.50" />
                            <InputError :message="form.errors.buying_price" />
                        </div>
                    </div>
                    <hr class="md:col-span-2 border-gray-200 my-4" />
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                        <div>
                            <Label for="quantity" class="block font-medium mb-1">Quantity</Label>
                            <Input id="quantity" v-model="form.quantity" type="number" min="0" step="1" placeholder="e.g., 100" />
                            <InputError :message="form.errors.quantity" />
                        </div>

                        <div>
                            <Label for="retail_price" class="block font-medium mb-1">Retail Price (Tsh.)</Label>
                            <Input id="retail_price" v-model="form.retail_price" type="number" min="0" step="0.01" placeholder="e.g., 15.50" />
                            <InputError :message="form.errors.retail_price" />
                        </div>

                        <div>
                            <Label for="wholesale_price" class="block font-medium mb-1">Wholesale Price (TSh.)</Label>
                            <Input id="wholesale_price" v-model="form.wholesale_price" type="number" min="0" step="0.01" placeholder="e.g., 12.00" />
                            <InputError :message="form.errors.wholesale_price" />
                        </div>
                    </div>
                    <hr class="md:col-span-2 border-gray-200 my-4" />

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                        <div>
                            <Label for="status" class="block font-medium mb-1">Status</Label>
                            <Select v-model="form.status" class="w-full">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select Status" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectGroup>
                                        <SelectLabel>Statuses</SelectLabel>
                                        <SelectItem value="available">Available</SelectItem>
                                        <SelectItem value="reserved">Reserved</SelectItem>
                                        <SelectItem value="expired">Expired</SelectItem>
                                        <SelectItem value="damaged">Damaged</SelectItem>
                                        <SelectItem value="returned">Returned</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div>
                            <Label for="expiration_date" class="block font-medium mb-1">Expiration Date</Label>
                            <Input id="expiration_date" v-model="form.expiration_date" type="date" />
                            <InputError :message="form.errors.expiration_date" />
                        </div>

                        <div>
                            <Label for="batch_number" class="block font-medium mb-1">Batch Number</Label>
                            <Input id="batch_number" v-model="form.batch_number" type="text" placeholder="e.g., BATCH-123" />
                            <InputError :message="form.errors.batch_number" />
                        </div>

                        <div>
                            <Label for="location_id" class="block font-medium mb-1">Location ID</Label>
                            <Input id="location_id" v-model="form.location_id" type="text" placeholder="e.g., Aisle 5, Shelf 2" />
                            <InputError :message="form.errors.location_id" />
                        </div>

                        <div>
                            <Label for="date" class="block font-medium mb-1">Receiving Date</Label>
                            <Input id="date" v-model="form.date" type="date" />
                            <InputError :message="form.errors.date" />
                        </div>
                    </div>

                    <div class="md:col-span-2 flex justify-end mt-6">
                        <Button type="submit" :disabled="form.processing" class="px-6 py-3 font-semibold bg-blue-600 hover:bg-blue-700 transition-colors">
                            {{ form.processing ? 'Saving...' : 'Save Stock' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';
import InputError from '@/components/InputError.vue';
import Input from '@/components/ui/input/Input.vue';

// Props from parent
const props = defineProps({
    products: Array,
    medicines: Array,
});

const searchTerm = ref('');

// Reactive form
const form = useForm({
    stockable_type: '',
    stockable_id: '',
    quantity: '',
    buying_price: '',
    retail_price: '',
    wholesale_price: '',
    date: new Date().toISOString().slice(0, 10),
    selected_unit: '',
    status: '',
    expiration_date: null,
    batch_number: '',
    location_id: ''
});

const selectedMedicineUnits = computed(() => {
    if (form.stockable_type === 'App\\Models\\Medicine' && form.stockable_id) {
        const medicine = props.medicines.find((m) => m.id === form.stockable_id);
        return medicine ? medicine.units : [];
    }
    return [];
});

const stockableTypes = {
    Product: 'App\\Models\\Product',
    Medicine: 'App\\Models\\Medicine',
};

// Dynamically compute the available items based on type
const filteredItems = computed(() => {
    if (form.stockable_type === 'App\\Models\\Product') return props.products;
    if (form.stockable_type === 'App\\Models\\Medicine') return props.medicines;
    return [];
});

const submit = () => {
    form.post(route('stocks.store'));
};

const breadcrumbs = [
    {title: "All Stocks", href: "/stocks"},
    {title: "Add New Stock", href: "/stocks/create"},
]

const searchedItems = computed(() => {
    if (!filteredItems.value) return [];
    return filteredItems.value.filter(item =>
        item.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
})
</script>
