<template>
  <AppLayout>

    <Head title="Add Stock" />

    <div class="p-6">
      <h1 class="text-2xl font-semibold mb-6">Add New Stock</h1>

      <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Stockable Type -->
        <div class="w-full">
          <Label for="stockable_type">Stockable Type</Label>
          <Select v-model="form.stockable_type" class="w-full">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select Type" />
            </SelectTrigger class="w-full">
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

        <!-- Stockable Item -->
        <div class="w-full">
          <Label for="stockable_id">Item</Label>
          <Select v-model="form.stockable_id" class="w-full">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select Item" />
            </SelectTrigger>
            <SelectContent class="w-full">
              <SelectGroup v-if="filteredItems.length">
                <SelectLabel>Items</SelectLabel>
                <SelectItem v-for="item in filteredItems" :key="item.id" :value="item.id">
                  {{ item.name }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError :message="form.errors.stockable_id" />
        </div>

        <!-- Quantity -->
        <div>
          <Label for="quantity">Quantity</Label>
          <Input id="quantity" v-model="form.quantity" type="number" step="0.01" />
          <InputError :message="form.errors.quantity" />
        </div>

        <!-- Retail Price -->
        <div>
          <Label for="retail_price">Retail Price</Label>
          <Input id="retail_price" v-model="form.retail_price" type="number" step="0.01" />
          <InputError :message="form.errors.retail_price" />
        </div>

        <!-- Wholesale Price -->
        <div>
          <Label for="wholesale_price">Wholesale Price</Label>
          <Input id="wholesale_price" v-model="form.wholesale_price" type="number" step="0.01" />
          <InputError :message="form.errors.wholesale_price" />
        </div>

        <!-- Date -->
        <div>
          <Label for="date">Date</Label>
          <Input id="date" v-model="form.date" type="date" />
          <InputError :message="form.errors.date" />
        </div>

        <!-- Submit -->
        <div class="col-span-1 md:col-span-2">
          <Button type="submit" :disabled="form.processing">Save</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import SelectLabel from '@/components/ui/select/SelectLabel.vue'
import InputError from '@/components/InputError.vue'
import Input from '@/components/ui/input/Input.vue'

// Props from parent
const props = defineProps({
  products: Array,
  medicines: Array,
})

// Reactive form
const form = useForm({
  stockable_type: '',
  stockable_id: '',
  quantity: '',
  retail_price: '',
  wholesale_price: '',
  date: new Date().toISOString().slice(0, 10),
})

const stockableTypes = {
  Product: 'App\\Models\\Product',
  Medicine: 'App\\Models\\Medicine',
}


// Dynamically compute the available items based on type
const filteredItems = computed(() => {
  if (form.stockable_type === 'App\\Models\\Product') return props.products
  if (form.stockable_type === 'App\\Models\\Medicine') return props.medicines
  return []
})

const submit = () => {
  form.post(route('stocks.store'))
}
</script>
