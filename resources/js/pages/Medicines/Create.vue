<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import SelectLabel from '@/components/ui/select/SelectLabel.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps({
  categories: Array,
  units: Array // expected to be passed from controller as all available units
})

const form = useForm({
  name: '',
  brand: '',
  medicine_category_id: '',
  description: '',
  units: [] // holds selected units with details
})

// check if unit is selected
const isSelected = (unitId) => form.units.some(u => u.medicine_unit_id === unitId)

// toggle selection
const toggleUnit = (unitId) => {
  if (isSelected(unitId)) {
    form.units = form.units.filter(u => u.medicine_unit_id !== unitId)
  } else {
    form.units.push({
      medicine_unit_id: unitId,
    })
  }
}

// submit form
const submit = () => {
  form.post(route('medicines.store'),{
    onFinish: () => {
      form.reset()
    },
    preserveScroll: true,
    onError: (errors) => {
      console.error(errors)
    },
  })
}

const breadcrumbs = [
  { title: 'Medicines', href: '/medicines' },
  { title: 'Create Medicine', href: '/medicines/create' },
]
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Register new Medicine" />

    <div class="container mx-auto p-6 space-y-6">
      <h2 class="text-2xl font-semibold">Register new Medicine</h2>

      <form @submit.prevent="submit" class="space-y-5">

        <div>
          <Label for="name">Medicine Name</Label>
          <Input id="name" v-model="form.name" type="text" class="mt-1" />
          <InputError :message="form.errors.name" class="mt-1" />
        </div>

        <div>
          <Label for="brand">Brand</Label>
          <Input id="brand" v-model="form.brand" type="text" class="mt-1" />
          <InputError :message="form.errors.brand" class="mt-1" />
        </div>

        <div>
          <Label for="medicine_category_id" class="mb-2">Category</Label>
          <Select v-model="form.medicine_category_id" class="w-full">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select a category" />
            </SelectTrigger>
            <SelectContent class="w-full">
              <SelectLabel>Categories</SelectLabel>
              <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <InputError :message="form.errors.medicine_category_id" class="mt-1" />
        </div>

        <div>
          <Label for="description" class="mb-2">Description</Label>
          <Textarea id="description" v-model="form.description" rows="4" />
          <InputError :message="form.errors.description" class="mt-1" />
        </div>

        <!-- Unit Selection -->
        <div>
          <h3 class="text-lg font-semibold mb-2">Select Units</h3>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            <label
              v-for="unit in units"
              :key="unit.id"
              class="flex items-center gap-2 text-sm"
            >
              <input
                type="checkbox"
                :value="unit.id"
                :checked="isSelected(unit.id)"
                @change="toggleUnit(unit.id)"
                class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              {{ unit.unit_name }}
            </label>
            <InputError :message="form.errors.units" class="mt-1" />
          </div>
        </div>

        <div class="pt-4">
          <Button type="submit" :disabled="form.processing">Save</Button>
        </div>

      </form>
    </div>
  </AppLayout>
</template>
