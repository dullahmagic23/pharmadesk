<template>
    <AppLayout title="Create Purchase">
        <div class="container p-6">
            <h6 class="text-2xl mb-4">Record new Purchase</h6>
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Vendor Selection -->
                <div>
                    <Label for="vendor_id" class="mb-2">Vendor</Label>
                    <Select v-model="form.vendor_id" class="w-full">
                        <SelectTrigger id="vendor_id" class="w-full">
                            <SelectValue placeholder="Select Vendor" />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectLabel>Vendors</SelectLabel>
                            <SelectItem v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                                {{ vendor.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.vendor_id" class="mt-1" />
                </div>

                <!-- Items -->
                <div
                    v-for="(item, index) in form.items"
                    :key="index"
                    class="bg-white rounded-md p-3 shadow space-y-4"
                >
                    <div class="flex items-center gap-3">
                        <!-- Type Selector -->
                        <div class="flex flex-col flex-1">
                            <Label :for="`type_${index}`" class="mb-2">Type</Label>
                            <Select v-model="item.purchasable_type" class="w-full">
                                <SelectTrigger :id="`type_${index}`" class="w-full">
                                    <SelectValue placeholder="Type" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectLabel>Type</SelectLabel>
                                    <SelectItem value="product">Product</SelectItem>
                                    <SelectItem value="medicine">Medicine</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Purchasable Selector -->
                        <div class="flex flex-col flex-1">
                            <Label :for="`purchasable_id_${index}`" class="mb-2">
                                {{ item.purchasable_type === 'medicine' ? 'Medicine' : 'Product' }}
                            </Label>
                            <Select v-model="item.purchasable_id" class="w-full">
                                <SelectTrigger :id="`purchasable_id_${index}`" class="w-full">
                                    <SelectValue :placeholder="`Select ${item.purchasable_type}`" />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectLabel>{{ item.purchasable_type === 'medicine' ? 'Medicines' : 'Products' }}</SelectLabel>
                                    <SelectItem
                                        v-for="option in item.purchasable_type === 'medicine' ? medicines : products"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Quantity -->
                        <div class="flex flex-col flex-1">
                            <Label :for="`quantity_${index}`" class="mb-2">Quantity</Label>
                            <Input v-model="item.quantity" :id="`quantity_${index}`" type="number" min="1" placeholder="Quantity" />
                        </div>

                        <!-- Unit Cost -->
                        <div class="flex flex-col flex-1">
                            <Label :for="`unit_cost_${index}`" class="mb-2">Unit Cost</Label>
                            <Input v-model="item.unit_cost" :id="`unit_cost_${index}`" type="number" step="0.01" min="0" placeholder="Unit Cost" />
                        </div>

                        <!-- Batch Number -->
                        <div class="flex flex-col flex-1">
                            <Label :for="`batch_number_${index}`" class="mb-2">Batch Number</Label>
                            <Input v-model="item.batch_number" :id="`batch_number_${index}`" placeholder="Batch Number" />
                        </div>

                        <!-- Expiry Date -->
                        <div class="flex flex-col flex-1">
                            <Label :for="`expiry_date_${index}`" class="mb-2">Expiry Date</Label>
                            <Input v-model="item.expiry_date" :id="`expiry_date_${index}`" type="date" />
                        </div>

                        <!-- Remove Row -->
                        <Button
                            v-if="form.items.length > 1"
                            type="button"
                            @click="removeItem(index)"
                            variant="secondary"
                            class="ml-2 self-end"
                        >
                            Remove
                        </Button>
                    </div>
                </div>
                <!-- Add More Items -->
                <Button type="button" variant="outline" class="mt-2" @click="addItem">+ Add Item</Button>
                <div>
                    <Label for="reference">Reference</Label>
                    <Input v-model="form.reference" placeholder="References" class="input w-full"/>
                    <InputError :message="form.reference.error"/>
                </div>

                <div>
                    <Label for="notes">Notes</Label>
                    <Textarea v-model="form.notes"></Textarea>
                    <InputError :message="form.notes.error" />
                </div>

                <div>
                    <Label for="date">Date</Label>
                    <Input v-model="form.date" class="input w-full"/>
                    <InputError :message="form.date.error"/>
                </div>

                <!-- Submit -->
                <div class="mt-6">
                    <Button :disabled="form.processing">Submit</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { computed, onMounted } from 'vue';
import {Textarea} from '@/components/ui/textarea';
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectLabel,
    SelectItem,
} from '@/components/ui/select'

defineProps({
    vendors: Array,
    products: Array,
    medicines: Array,
})




const form = useForm({
    vendor_id: '',
    date: new Date().toISOString().slice(0, 10),
    total_cost: 0,
    notes:'',
    reference:'',
    items: [
        {
            purchasable_type: '',
            purchasable_id: '',
            quantity: '',
            unit_cost: '',
            batch_number: '',
            expiry_date: ''
        }
    ]
})
const getTotalCost  = computed(() => {
    let total = 0;
    form.items.forEach(item => {
        total += item.quantity * item.unit_cost;
    })
    return total;
})

onMounted(()=>{
    form.total_cost = getTotalCost;
})
function addItem() {
    form.items.push({
        purchasable_type: '',
        purchasable_id: '',
        quantity: '',
        unit_cost: '',
        batch_number: '',
        expiry_date: ''
    })
}

function removeItem(index: number) {
    form.items.splice(index, 1)
}

function submit() {
    form.post('/purchases')
}


</script>
