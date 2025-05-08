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
        buyer: {name:string};
        total: number;
        paid: number;
        balance: number;
    };
}>();

const form = useForm({
    amount: '',
});

function submit() {
    form.post(route('sales.payments.store', props.sale.id));
}
</script>

<template>

    <AppLayout>

        <Head title="Add Payment" />
        <div class="container p-6">

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold">Add Payment for Sale #{{ sale.id }}</h1>
                <!-- <Link :href="route('sales.show', sale.id)">
            <Button variant="outline">Back to Sale</Button>
            </Link> -->
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Sale Summary</CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <strong>Buyer:</strong>
                        <p>{{ sale.buyer.name }}</p>
                    </div>
                    <div>
                        <strong>Total:</strong>
                        <p>{{ currency(sale.total) }}</p>
                    </div>
                    <div>
                        <strong>Balance:</strong>
                        <p>{{ currency(sale.balance) }}</p>
                    </div>
                </CardContent>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="amount">Amount</Label>
                            <Input id="amount" v-model="form.amount" type="number" step="0.01" min="0"
                                :max="props.sale.balance" class="w-full mt-1" />
                            <p v-if="form.errors.amount" class="text-red-500 text-sm mt-1">
                                {{ form.errors.amount }}
                            </p>
                        </div>
                        <Button type="submit" :disabled="form.processing">Submit Payment</Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
