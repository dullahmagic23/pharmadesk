<template>
    <AppLayout>
        <div class="container max-w-2xl mx-auto p-6 space-y-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-800">
                    <RecycleIcon class="inline-block mr-2 h-7 w-7 text-blue-600"/> Stock Unit Conversion
                </h1>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <form @submit.prevent="convertStock" class="space-y-6">

                    <div class="space-y-2">
                        <Label for="stock-select" class="text-lg font-semibold text-gray-700">1. Select Stock Item</Label>
                        <p class="text-sm text-gray-500">Choose the medicine or product you want to convert.</p>
                        <Select v-model="form.selectedStockId" class="w-full">
                            <SelectTrigger id="stock-select" class="w-full">
                                <SelectValue placeholder="Select an item" />
                            </SelectTrigger>
                            <SelectContent class="w-full max-h-[300px] overflow-y-auto">
                                <SelectGroup>
                                    <SelectLabel>Available Stock</SelectLabel>
                                    <SelectItem v-for="stock in stocks" :key="stock.id" :value="stock.id">
                                        {{ stock.name }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.selectedStockId"/>
                    </div>

                    <div v-if="selectedStock" class="space-y-6">
                        <hr class="border-gray-200" />
                        <div class="space-y-2">
                            <Label class="text-lg font-semibold text-gray-700">2. Define Conversion</Label>
                            <p class="text-sm text-gray-500">Specify the units and conversion rate for this item.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <Label for="from-unit" class="block font-medium mb-1">From Unit (Bulk)</Label>
                                    <Select v-model="form.fromUnit">
                                        <SelectTrigger id="from-unit">
                                            <SelectValue placeholder="Select bulk unit" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="unit in selectedStock.units" :key="unit.id" :value="unit.id">
                                                    {{ unit.unit_name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.fromUnit"/>
                                </div>

                                <div>
                                    <Label for="to-unit" class="block font-medium mb-1">To Unit (Smaller)</Label>
                                    <Select v-model="form.toUnit">
                                        <SelectTrigger id="to-unit">
                                            <SelectValue placeholder="Select smaller unit" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem v-for="unit in selectedStock.units" :key="unit.id" :value="unit.id">
                                                    {{ unit.unit_name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="form.errors.toUnit"/>
                                </div>
                            </div>

                            <div>
                                <Label for="rate" class="block font-medium mb-1">Conversion Rate</Label>
                                <Input type="number" v-model.number="form.rate" min="1" placeholder="e.g., 1 box = 100 tablets" />
                                <InputError :message="form.errors.rate"/>
                            </div>
                        </div>

                        <hr class="border-gray-200" />
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <Label class="text-lg font-semibold text-gray-700">3. Enter Quantity & Prices</Label>
                                <p class="text-sm text-gray-500">Provide the quantity of the bulk unit and the prices for the smaller unit.</p>
                            </div>

                            <div>
                                <Label for="quantity" class="block font-medium mb-1">Quantity to Convert</Label>
                                <Input type="number" v-model.number="form.quantity" min="1" placeholder="e.g., 10" />
                                <InputError :message="form.errors.quantity"/>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <Label for="retail-price" class="block font-medium mb-1">Retail Price (Smaller Unit)</Label>
                                    <Input type="number" v-model.number="form.retail_price" min="0" step="0.01" placeholder="e.g., 5.00" />
                                    <InputError :message="form.errors.retail_price"/>
                                </div>
                                <div>
                                    <Label for="wholesale-price" class="block font-medium mb-1">Wholesale Price (Smaller Unit)</Label>
                                    <Input type="number" v-model.number="form.wholesale_price" min="0" step="0.01" placeholder="e.g., 4.50" />
                                    <InputError :message="form.errors.wholesale_price"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <Button type="submit" :disabled="!canConvert || form.processing" class="w-full h-12 text-lg font-semibold transition-colors duration-200">
                            {{ form.processing ? 'Converting...' : 'Convert Stock' }}
                        </Button>
                    </div>
                </form>

                <div v-if="form.isDirty && result !== null" class="mt-8 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-center">
                    <span class="font-bold">Conversion Successful:</span>
                    Successfully converted <span class="font-bold">{{ form.quantity }}</span> {{ fromUnitName }} into <span class="font-bold">{{ result }}</span> {{ toUnitName }}.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useForm } from '@inertiajs/vue3';
import { RecycleIcon } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import SelectLabel from '@/components/ui/select/SelectLabel.vue';

const props = defineProps({
    stocks: Array
});

const form = useForm({
    selectedStockId: '',
    fromUnit: '',
    toUnit: '',
    rate: null,
    quantity: null,
    retail_price: null,
    wholesale_price: null
});

const result = ref(null);

const selectedStock = computed(() => props.stocks.find(s => s.id === form.selectedStockId));

const fromUnitName = computed(() => {
    const unit = selectedStock.value?.units.find(u => u.id === form.fromUnit);
    return unit?.unit_name || 'Unit';
});

const toUnitName = computed(() => {
    const unit = selectedStock.value?.units.find(u => u.id === form.toUnit);
    return unit?.unit_name || 'Unit';
});

const canConvert = computed(() =>
    form.selectedStockId &&
    form.fromUnit &&
    form.toUnit &&
    form.rate > 0 &&
    form.quantity > 0 &&
    form.retail_price > 0 &&
    form.wholesale_price > 0 &&
    form.fromUnit !== form.toUnit
);

function convertStock() {
    form.post(route('stock_conversion.store'), {
        preserveScroll: true,
        preserveUrl: true,
        onSuccess: (page) => {
            result.value = page.props.conversion_result.result;
        }
    });
}
</script>
