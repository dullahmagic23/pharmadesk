<template>
    <AppLayout>
        <div class="container p-6">
            <h1 class="text-xl font-bold mb-4">New Bill Payment</h1>
            <form @submit.prevent="form.post(route('bill-payments.store'))" class="space-y-4">
                <div>
                    <Label for="bill_id" class="mb-2">Select Bill</Label>
                    <Select v-model="form.bill_id" class="w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Choose a bill..." />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectGroup class="w-full">
                                <SelectItem
                                    v-for="bill in bills"
                                    :key="bill.id"
                                    :value="bill.id"
                                >
                                    {{ bill.billable.name || `Bill on ${bill.billing_date}` }} -  {{ currency(bill.amount) }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.bill_id" />
                </div>

                <div>
                    <Label for="amount"  class="mb-2">Amount</Label>
                    <Input v-model="form.amount" type="number" step="0.01" />
                    <InputError :message="form.errors.amount" />
                </div>

                <div>
                    <Label for="payment_date" class="mb-2">Payment date </Label>
                    <Input v-model="form.payment_date" type="date" />
                    <InputError :message="form.errors.payment_date" />
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
import { Select, SelectTrigger, SelectValue, SelectContent, SelectGroup, SelectItem } from '@/components/ui/select'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import currency from '@/modules/currecyFormatter'

const props = defineProps({
    bills: Array
})

const form = useForm({
    bill_id: '',
    amount: '',
    payment_date: new Date().toISOString().split('T')[0],
})
</script>
