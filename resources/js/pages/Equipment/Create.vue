<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

// Props
const props = defineProps({
    equipment: Object,
    suppliers: Array,
    isEdit: Boolean,
});

// Form
const form = useForm({
    name: props.equipment?.name || '',
    status: props.equipment?.status || '',
    supplier_id: props.equipment?.supplier_id || '',
});
</script>

<template>
  <AppLayout>
      <div class="container p-6">
          <form
              @submit.prevent="isEdit ? form.put(route('equipments.update', props.equipment.id)) : form.post(route('equipments.store'))"
              class="space-y-6"
          >
              <!-- Name -->
              <div>
                  <Label for="name">Name</Label>
                  <Input v-model="form.name" id="name" class="mt-1 w-full" />
              </div>

              <!-- Status -->
              <div>
                  <Label>Status</Label>
                  <Select v-model="form.status" class="w-full">
                      <SelectTrigger class="w-full">
                          <SelectValue placeholder="Select status" />
                      </SelectTrigger>
                      <SelectContent class="w-full">
                          <SelectItem value="active">Active</SelectItem>
                          <SelectItem value="inactive">Inactive</SelectItem>
                          <SelectItem value="maintenance">Maintenance</SelectItem>
                      </SelectContent>
                  </Select>
              </div>

              <!-- Supplier -->
              <div>
                  <Label>Supplier</Label>
                  <Select v-model="form.supplier_id" class="w-full">
                      <SelectTrigger class="w-full">
                          <SelectValue placeholder="Select supplier" />
                      </SelectTrigger>
                      <SelectContent class="w-full">
                          <SelectItem
                              v-for="supplier in suppliers"
                              :key="supplier.id"
                              :value="supplier.id"
                          >
                              {{ supplier.name }}
                          </SelectItem>
                      </SelectContent>
                  </Select>
              </div>

              <!-- Submit -->
              <div>
                  <Button type="submit" >
                      {{ isEdit ? 'Update Equipment' : 'Register Equipment' }}
                  </Button>
              </div>
          </form>
      </div>
  </AppLayout>
</template>
