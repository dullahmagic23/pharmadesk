<template>
  <AppLayout>
    <div class="container p-6">
      <h1 class="text-xl font-bold mb-4">Create New Expense</h1>

      <form @submit.prevent="form.post(route('expenses.store'))" class="space-y-4">
        <div>
          <Label for="title" class="mb-2">Expense Title</Label>
          <Input v-model="form.title" type="text" />
          <InputError :message="form.errors.title" />
        </div>

        <div>
          <Label for="amount" class="mb-2">Amount</Label>
          <Input v-model="form.amount" type="number" step="0.01" />
          <InputError :message="form.errors.amount" />
        </div>

        <div>
          <Label for="expense_date" class="mb-2">Expense date</Label>
          <Input v-model="form.expense_date" type="date" />
          <InputError :message="form.errors.expense_date" />
        </div>

        <div>
          <Label for="bill_id" class="mb-2">Linked Bill (Optional)</Label>
          <Select v-model="form.bill_id" class="w-full">
            <SelectTrigger class="w-full">
              <SelectValue v-if="form.bill_id" :value="form.bill_id" />
              <SelectValue v-else>Select a Bill</SelectValue>
            </SelectTrigger>
            <SelectContent>
              <SelectGroup class="w-full">
                <SelectLabel>Available Bills</SelectLabel>
                <SelectItem v-for="bill in bills" :key="bill.id" :value="bill.id">
                  {{ bill.billable.name || 'Untitled Bill' }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError :message="form.errors.bill_id" />
        </div>

        <div>
          <Label for="notes" class="mb-2">Notes (Optional)</Label>
          <Input v-model="form.notes" type="text" />
          <InputError :message="form.errors.notes" />
        </div>

        <Button type="submit" :disabled="form.processing">Submit</Button>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectContent, SelectGroup, SelectLabel, SelectItem, SelectValue } from '@/components/ui/select'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  bills: Array
})

const form = useForm({
  title: '',
  amount: '',
  expense_date: new Date().toISOString().split('T')[0],  // Set today's date as default
  bill_id: null,
  notes: ''
})
</script>
