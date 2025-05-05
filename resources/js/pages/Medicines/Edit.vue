<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectGroup, SelectItem } from '@/components/ui/select'

const props = defineProps({
  medicine: Object,
  categories: Array,
  units: Array,
})

const form = useForm({
  name: props.medicine.name,
  brand: props.medicine.brand,
  medicine_category_id: props.medicine.category?.id ?? '',
  unit_ids: props.medicine.units?.map(unit => unit.id) ?? [],
})

function updateMedicine() {
  form.put(`/medicines/${props.medicine.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    },
    onError: (errors) => {
      console.error(errors)
    },
  })
}

const breadcrumbs = [
  { title: 'Medicines', href: '/medicines' },
  { title: 'Edit Medicine', href: `/medicines/${props.medicine.id}/edit` },
]
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Edit Medicine" />
    <div class="conatiner p-6 bg-white shadow rounded-md">
      <h2 class="text-xl font-bold mb-4">Edit Medicine</h2>

      <form @submit.prevent="updateMedicine" class="space-y-6">
        <div>
          <Label for="name">Name</Label>
          <Input id="name" v-model="form.name" type="text" class="mt-1" />
          <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
          <Label for="brand">Brand</Label>
          <Input id="brand" v-model="form.brand" type="text" class="mt-1" />
          <p v-if="form.errors.brand" class="text-red-600 text-sm mt-1">{{ form.errors.brand }}</p>
        </div>

        <div>
          <Label for="medicine_category_id">Category</Label>
          <Select v-model="form.medicine_category_id" class="w-full">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select a category" />
            </SelectTrigger>
            <SelectContent class="w-full">
              <SelectGroup>
                <SelectItem
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <p v-if="form.errors.medicine_category_id" class="text-red-600 text-sm mt-1">{{ form.errors.medicine_category_id }}</p>
        </div>

        <div>
          <Label>Units</Label>
          <div class="flex flex-wrap gap-2 mt-1">
            <label
              v-for="unit in units"
              :key="unit.id"
              class="inline-flex items-center space-x-2"
            >
              <input
                type="checkbox"
                :value="unit.id"
                v-model="form.unit_ids"
                class="form-checkbox rounded"
              />
              <span>{{ unit.unit_name }}</span>
            </label>
          </div>
          <p v-if="form.errors.unit_ids" class="text-red-600 text-sm mt-1">{{ form.errors.unit_ids }}</p>
        </div>

        <div class="flex items-center justify-end space-x-4">
          <Link href="/medicines" class="text-sm text-gray-600 hover:underline">Cancel</Link>
          <Button :disabled="form.processing" type="submit">
            Update Medicine
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
