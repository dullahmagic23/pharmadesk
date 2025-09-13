<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import currency from '@/modules/currecyFormatter';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    sale: {
        id: string;
        buyer: { name: string };
        total: number;
        paid: number;
        balance: number;
    };
}>();

const form = useForm({
    amount: 0,
});

function submit() {
    form.post(route('sales.payments.store', props.sale.id));
}

function payFullBalance() {
    form.amount = props.sale.balance;
}

const breadcrumbs = [
    { title: 'Sales', href: route('sales.index') },
    { title: `Sale #${props.sale.id}`, href: route('sales.add-payments', props.sale.id) },
    { title: 'Add Payment', href: '#' }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Add Payment" />

        <div class="container max-w-3xl mx-auto p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Add Payment</h1>
                <Link :href="route('sales.show', sale.id)">
                    <Button variant="outline" size="sm">Back to Sale</Button>
                </Link>
            </div>

            <!-- Sale Summary -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">Sale Summary</CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-gray-500">Buyer</p>
                        <p class="font-medium text-gray-800">{{ sale.buyer.name }}</p>
                    </div>
                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-gray-500">Total</p>
                        <p class="font-medium text-gray-800">{{ currency(sale.total) }}</p>
                    </div>
                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-gray-500">Balance</p>
                        <p class="font-semibold text-red-600">{{ currency(sale.balance) }}</p>
                    </div>
                </CardContent>

                <!-- Payment Form -->
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <Label for="amount" class="block mb-1">Payment Amount</Label>
                            <div class="flex gap-2">
                                <Input
                                    id="amount"
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :max="props.sale.balance"
                                    class="flex-1"
                                    placeholder="Enter amount..."
                                />
                                <Button type="button" variant="secondary" @click="payFullBalance">
                                    Pay Full ({{ currency(sale.balance) }})
                                </Button>
                            </div>
                            <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1">
                                {{ form.errors.amount }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                Submit Payment
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
