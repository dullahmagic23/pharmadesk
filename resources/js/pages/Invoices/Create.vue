<script lang="ts" setup>
import { ref, reactive, computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectContent, SelectItem, SelectValue } from '@/components/ui/select'
import AppLayout from '@/layouts/AppLayout.vue'
import currency from '../../modules/currecyFormatter';

const props = defineProps({
    patients: Array,
    medicines: Array,
    products: Array,
    services: Array,
})

const items = ref([])
const selected = reactive({
    billable_type: '',
    billable_id: '',
    quantity: 1,
    price_type: 'retail',
})

const form = useForm({
    patient_id: '',
    items: [],
    total:null
})

const allItems = computed(() => [
    ...props.medicines.filter(m => m.stock && (m.stock.retail_price || m.stock.wholesale_price)).map(m => ({ ...m, type: 'App\\Models\\Medicine' })),
    ...props.products.filter(p => p.stock && (p.stock.retail_price || p.stock.wholesale_price)).map(p => ({ ...p, type: 'App\\Models\\Product' })),
    ...props.services.map(s => ({ ...s, type: 'App\\Models\\Service' })),
])


const addItem = () => {
    const item = allItems.value.find(i => {
        const normalizedType = i.type.replace(/\\/g, '\\\\');
        return i.id === selected.billable_id && normalizedType === selected.billable_type;
    });

    if (!item) return;
    let price = 0;
    if (item.stock && (item.type === 'App\\Models\\Medicine' || item.type === 'App\\Models\\Product')) {
        price = selected.price_type === 'retail' ? item.stock.retail_price : item.stock.wholesale_price;
    } else {
        price = item.price
    }

    items.value.push({
        ...selected,
        name: item.name,
        price,
    });
    selected.billable_id = '';
    selected.billable_type = '';
    selected.quantity = 1;
    selected.price_type = 'retail';
}


const removeItem = (index) => {
    items.value.splice(index, 1)
}

const submit = () => {
    if (!form.patient_id || items.value.length === 0) {
        alert('Please select a patient and add at least one item.')
        return
    }

    form.items = items.value
    form.total = total
    form.post(route('invoices.store'))
}

const total = computed(() =>
    items.value.reduce((sum, i) => sum + (i.price * i.quantity), 0).toFixed(2)
)
</script>

<template>
    <Head title="Create Invoice" />
    <AppLayout>
        <div class="container p-6 bg-white rounded-xl shadow">
            <h1 class="text-xl font-bold mb-4">Create Invoice</h1>

            <div class="mb-4">
                <Label for="patient_id">Patient</Label>
                <select v-model="form.patient_id" class="w-full border p-2 rounded">
                    <option value="" disabled>Select Patient</option>
                    <option v-for="p in patients" :key="p.id" :value="p.id">{{ p.first_name + ' ' + p.last_name }}</option>
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <Label>Type</Label>
                    <Select v-model="selected.billable_type" class="w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Type" />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectItem value="App\\Models\\Medicine">Medicine</SelectItem>
                            <SelectItem value="App\\Models\\Product">Product</SelectItem>
                            <SelectItem value="App\\Models\\Service">Service</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div>
                    <Label>Item</Label>
                    <select v-model="selected.billable_id" class="w-full border p-2 rounded">
                        <option value="" disabled>Select Item</option>
                        <option
                            v-for="item in allItems.filter(i => i.type.replace(/\\/g, '\\\\') === selected.billable_type)"
                            :key="`${item.type}-${item.id}`"
                            :value="item.id"
                        >
                            {{ item.name }}
                            <span v-if="item.stock">
                            — Retail: {{ item.stock.retail_price || 'N/A' }}, Wholesale: {{ item.stock.wholesale_price || 'N/A' }}
                          </span>
                        </option>

                    </select>
                </div>

                <div class="flex space-x-2">
                    <div class="w-1/2">
                        <Label>Qty</Label>
                        <Input type="number" min="1" v-model.number="selected.quantity" />
                    </div>
                    <div class="w-1/2">
                        <Label>Price</Label>
                        <select v-model="selected.price_type" class="w-full border p-2 rounded">
                            <option value="retail">Retail</option>
                            <option value="wholesale">Wholesale</option>
                        </select>
                    </div>
                </div>
            </div>

            <Button @click="addItem" class="mb-4">Add Item</Button>

            <table class="w-full table-auto border mb-4">
                <thead>
                <tr class="bg-gray-100">
                    <th class="text-left p-2">Name</th>
                    <th class="text-left p-2">Type</th>
                    <th class="text-left p-2">Qty</th>
                    <th class="text-left p-2">Price Type</th>
                    <th class="text-left p-2">Unit Price</th>
                    <th class="text-left p-2">Subtotal</th>
                    <th class="text-left p-2">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(i, index) in items" :key="index">
                    <td class="p-2">{{ i.name }}</td>
                    <td class="p-2">{{ i.billable_type.split('\\').pop() }}</td>
                    <td class="p-2">{{ i.quantity }}</td>
                    <td class="p-2 capitalize">{{ i.price_type }}</td>
                    <td class="p-2">{{ currency(i.price) }}</td>
                    <td class="p-2">{{ currency((i.price * i.quantity).toFixed(2)) }}</td>
                    <td class="p-2"><Button @click="removeItem(index)" variant="destructive">Remove</Button></td>
                </tr>
                </tbody>
            </table>

            <div class="text-right font-bold text-lg mb-4">
                Total: {{ currency(total) }}
            </div>

            <Button @click="submit" :disabled="form.processing">Submit Invoice</Button>
        </div>
    </AppLayout>
</template>
