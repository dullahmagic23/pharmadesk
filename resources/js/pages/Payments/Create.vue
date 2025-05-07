<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/AppLayout.vue'
defineProps<{
    invoices: Array<any>
}>()

const form = useForm({
    invoice_id: '',
    amount: '',
    method: 'cash',
    paid_at: new Date().toISOString().slice(0, 10),
})

const submit = () => {
    form.post(route('payments.store'))
}
</script>

<template>
    <Head title="New Payment" />
    <AppLayout>
        <div class="container p-6 bg-white rounded-xl shadow mx-auto">
            <h1 class="text-xl font-bold mb-4">Record Payment</h1>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <Label for="invoice_id">Invoice</Label>
                    <select v-model="form.invoice_id" id="invoice_id" class="w-full border p-2 rounded">
                        <option value="" disabled>Select Invoice</option>
                        <option v-for="inv in invoices" :key="inv.id" :value="inv.id">
                            {{ inv.invoice_number }} — {{ inv.patient?.first_name }} {{ inv.patient?.last_name }}
                        </option>
                    </select>
                    <div v-if="form.errors.invoice_id" class="text-red-600 text-xs">{{ form.errors.invoice_id }}</div>
                </div>

                <div>
                    <Label for="amount">Amount</Label>
                    <Input v-model="form.amount" type="number" min="0" step="0.01" id="amount" />
                    <div v-if="form.errors.amount" class="text-red-600 text-xs">{{ form.errors.amount }}</div>
                </div>

                <div>
                    <Label for="method">Method</Label>
                    <select v-model="form.method" id="method" class="w-full border p-2 rounded">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="insurance">Insurance</option>
                    </select>
                    <div v-if="form.errors.method" class="text-red-600 text-xs">{{ form.errors.method }}</div>
                </div>

                <div>
                    <Label for="paid_at">Paid At</Label>
                    <Input v-model="form.paid_at" type="date" id="paid_at" />
                    <div v-if="form.errors.paid_at" class="text-red-600 text-xs">{{ form.errors.paid_at }}</div>
                </div>

                <Button type="submit" :disabled="form.processing">Submit Payment</Button>
            </form>
        </div>
    </AppLayout>
</template>
