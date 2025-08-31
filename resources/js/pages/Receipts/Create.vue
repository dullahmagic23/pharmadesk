<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import currency from '@/modules/currecyFormatter';

const props = defineProps<{
    sales: {
        id: string;
        buyer: { name: string };
        total: number;
        balance: number;
    }[];
}>();

const form = useForm({
    sale_id: '',
});

function submit() {
    form.post(route('receipts.store'));
}

const breadcrumbs = [
    { title: 'Receipts', href: route('receipts.index') },
    { title: 'Create Receipt', href: '#' }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create Receipt" />

        <div class="container max-w-3xl mx-auto p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Create Receipt</h1>
                <Link :href="route('receipts.index')">
                    <Button variant="outline" size="sm">Back to Receipts</Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">Receipt Details</CardTitle>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Sale Selection -->
                        <div>
                            <Label for="sale_id">Related Sale (optional)</Label>
                            <Select v-model="form.sale_id" class="w-full mt-1">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a sale..." />
                                </SelectTrigger>
                                <SelectContent class="w-full">
                                    <SelectItem
                                        v-for="sale in props.sales"
                                        :key="sale.id"
                                        :value="sale.id"
                                    >
                                        {{ sale.buyer.name }}-Total: {{sale.total}} (Bal: {{ currency(sale.balance) }})
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.sale_id" class="text-red-500 text-xs mt-1">
                                {{ form.errors.sale_id }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                Save Receipt
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
