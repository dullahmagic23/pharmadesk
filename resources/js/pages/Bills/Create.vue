<template>
    <AppLayout title="Create Bill">
        <div class="container p-6">
            <h1 class="text-2xl">Create a new Bill</h1>
            <form @submit.prevent="form.post(route('bills.store'))" class="space-y-6">
            <div>
                <Label for="billable_type" class="mb-2">Billable type</Label>
                <Select v-model="form.billable_type" id="billable_type" class="w-full">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent class="w-full">
                        <SelectItem value="vendor">Vendor</SelectItem>
                        <SelectItem value="supplier">Supplier</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.billable_type" />
            </div>

            <div v-if="form.billable_type">
                <Label for="billable_id" class="mb-2">{{ `Select ${form.billable_type}` }}</Label>
                <Select v-model="form.billable_id" id="billable_id" class="w-full">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select" />
                    </SelectTrigger>
                    <SelectContent class="w-full">
                        <SelectItem
                            v-for="entity in selectedEntities"
                            :key="entity.id"
                            :value="entity.id"
                        >
                            {{ entity.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.billable_id" />
            </div>

            <div>
                <Label for="amount" class="mb-2">Amount</Label>
                <Input v-model="form.amount" id="amount" type="number" step="0.01" />
                <InputError :message="form.errors.amount" />
            </div>

            <div>
                <Label for="billing_date" class="mb-2">Billing Date</Label>
                <Input v-model="form.billing_date" id="billing_date" type="date" />
                <InputError :message="form.errors.billing_date" />
            </div>

            <div class="flex justify-start">
                <Button type="submit" :disabled="form.processing">Create Bill</Button>
            </div>
        </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import {Input} from '@/components/ui/input'
import {Select, SelectTrigger, SelectValue, SelectContent, SelectItem} from '@/components/ui/select'
import {Label} from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import {Button} from '@/components/ui/button'

const props = defineProps({
    vendors: Array,
    suppliers: Array,
})

const form = useForm({
    billable_type: '',
    billable_id: '',
    amount: '',
    billing_date: '',
})

const selectedEntities = computed(() => {
    return form.billable_type === 'vendor'
        ? props.vendors
        : form.billable_type === 'supplier'
            ? props.suppliers
            : []
})
</script>
