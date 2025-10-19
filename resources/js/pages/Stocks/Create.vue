<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Add Stock" />

        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Add New Stock</h1>
                            <p class="text-gray-500 text-sm mt-1">Register new inventory items</p>
                        </div>
                        <Link href="/stocks">
                            <Button variant="outline" class="rounded-lg gap-2 h-11 border-2">
                                <ArrowLeft class="w-5 h-5" />
                                <span class="hidden sm:inline">Back</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-4 py-8">
                <div class="max-w-4xl mx-auto">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Item Selection Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <Package class="w-6 h-6 text-blue-600" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Item Selection</h2>
                                    <p class="text-xs text-gray-500">Choose the item type and specific item</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Stock Type -->
                                <div>
                                    <Label for="stockable_type" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Stock Type *
                                    </Label>
                                    <Select v-model="form.stockable_type" class="w-full">
                                        <SelectTrigger class="h-10 rounded-lg border-gray-200 w-full">
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
                                    <InputError :message="form.errors.stockable_type" class="mt-2" />
                                </div>

                                <!-- Item Selection -->
                                <div>
                                    <Label for="stockable_id" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Item *
                                    </Label>
                                    <Select v-model="form.stockable_id" class="w-full">
                                        <SelectTrigger class="h-10 rounded-lg border-gray-200 w-full">
                                            <SelectValue placeholder="Search or Select Item" />
                                        </SelectTrigger>
                                        <SelectContent class="w-full">
                                            <div class="p-2 border-b border-gray-100">
                                                <Input
                                                    type="text"
                                                    placeholder="Search Items..."
                                                    v-model="searchTerm"
                                                    class="w-full h-9"
                                                    @keydown.stop
                                                />
                                            </div>
                                            <SelectGroup v-if="searchedItems.length">
                                                <SelectLabel>Items</SelectLabel>
                                                <SelectItem v-for="item in searchedItems" :key="item.id" :value="item.id">
                                                    {{ item.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                            <div v-else class="p-4 text-center text-gray-500 text-sm">
                                                No items found. Select a type first.
                                            </div>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.stockable_id" class="mt-2" />
                                </div>

                                <!-- Unit (Medicine Only) -->
                                <div v-if="form.stockable_type === 'App\\Models\\Medicine'">
                                    <Label for="selected_unit" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Unit *
                                    </Label>
                                    <Select v-model="form.selected_unit" class="w-full">
                                        <SelectTrigger class="h-10 rounded-lg border-gray-200 w-full">
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
                                    <InputError :message="form.errors.selected_unit" class="mt-2" />
                                </div>

                                <!-- Buying Price -->
                                <div>
                                    <Label for="buying_price" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Buying Price (Tsh.) *
                                    </Label>
                                    <Input
                                        id="buying_price"
                                        v-model="form.buying_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="e.g., 15.50"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.buying_price" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Quantity Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <DollarSign class="w-6 h-6 text-green-600" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Pricing & Quantity</h2>
                                    <p class="text-xs text-gray-500">Set prices and stock quantity</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Quantity -->
                                <div>
                                    <Label for="quantity" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Quantity *
                                    </Label>
                                    <Input
                                        id="quantity"
                                        v-model="form.quantity"
                                        type="number"
                                        min="0"
                                        step="1"
                                        placeholder="e.g., 100"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.quantity" class="mt-2" />
                                </div>

                                <!-- Retail Price -->
                                <div>
                                    <Label for="retail_price" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Retail Price (Tsh.) *
                                    </Label>
                                    <Input
                                        id="retail_price"
                                        v-model="form.retail_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="e.g., 15.50"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.retail_price" class="mt-2" />
                                </div>

                                <!-- Wholesale Price -->
                                <div>
                                    <Label for="wholesale_price" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Wholesale Price (TSh.) *
                                    </Label>
                                    <Input
                                        id="wholesale_price"
                                        v-model="form.wholesale_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="e.g., 12.00"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.wholesale_price" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Stock Details Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-3 bg-purple-100 rounded-lg">
                                    <Layers class="w-6 h-6 text-purple-600" />
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Stock Details</h2>
                                    <p class="text-xs text-gray-500">Tracking and expiration information</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status -->
                                <div>
                                    <Label for="status" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Status *
                                    </Label>
                                    <Select v-model="form.status" class="w-full">
                                        <SelectTrigger class="h-10 rounded-lg border-gray-200">
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
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>

                                <!-- Expiration Date -->
                                <div>
                                    <Label for="expiration_date" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Expiration Date
                                    </Label>
                                    <Input
                                        id="expiration_date"
                                        v-model="form.expiration_date"
                                        type="date"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.expiration_date" class="mt-2" />
                                </div>

                                <!-- Batch Number -->
                                <div>
                                    <Label for="batch_number" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Batch Number
                                    </Label>
                                    <Input
                                        id="batch_number"
                                        v-model="form.batch_number"
                                        type="text"
                                        placeholder="e.g., BATCH-123"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.batch_number" class="mt-2" />
                                </div>

                                <!-- Location ID -->
                                <div>
                                    <Label for="location_id" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Location ID
                                    </Label>
                                    <Input
                                        id="location_id"
                                        v-model="form.location_id"
                                        type="text"
                                        placeholder="e.g., Aisle 5, Shelf 2"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.location_id" class="mt-2" />
                                </div>

                                <!-- Receiving Date -->
                                <div>
                                    <Label for="date" class="text-sm font-semibold text-gray-700 block mb-2">
                                        Receiving Date *
                                    </Label>
                                    <Input
                                        id="date"
                                        v-model="form.date"
                                        type="date"
                                        class="h-10 rounded-lg border-gray-200"
                                    />
                                    <InputError :message="form.errors.date" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Required Fields Info -->
                        <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                            <p class="text-xs text-blue-700 font-semibold mb-2 flex items-center gap-2">
                                <Info class="w-4 h-4" />
                                Required fields
                            </p>
                            <p class="text-xs text-blue-600">Stock Type, Item, Buying Price, Quantity, Retail Price, Wholesale Price, Status, and Receiving Date</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3">
                            <Link href="/stocks">
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="rounded-lg border-2 h-10"
                                >
                                    Cancel
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="px-8 h-10 bg-blue-600 hover:bg-blue-700 rounded-lg gap-2 font-semibold transition-all"
                            >
                                <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                {{ form.processing ? 'Saving...' : 'Save Stock' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';
import InputError from '@/components/InputError.vue';
import Input from '@/components/ui/input/Input.vue';
import { Package, DollarSign, Layers, ArrowLeft, Info } from 'lucide-vue-next';

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
    { title: "All Stocks", href: "/stocks" },
    { title: "Add New Stock", href: "/stocks/create" },
]

const searchedItems = computed(() => {
    if (!filteredItems.value) return [];
    return filteredItems.value.filter(item =>
        item.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
})
</script>
