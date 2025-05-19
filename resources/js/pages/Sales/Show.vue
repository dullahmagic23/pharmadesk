<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import currency from '@/modules/currecyFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { PrinterIcon } from 'lucide-vue-next';

const props = defineProps<{
    sale: {
        id: number;
        type: string;
        buyer: { name: string };
        total: number;
        paid: number;
        balance: number;
        created_at: string;
        items: Array<{
            type: string;
            sellable: { name: string };
            name: string;
            quantity: number;
            price: number;
        }>;
    };
}>();
const breadcrumbs = [
    { title: 'Sales', href: route('sales.index') },
    { title: `Sale #${props.sale.id}`, href: route('sales.show', props.sale.id) }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container p-6">

            <Head title="Sale Details" />

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold">Sale #{{ props.sale.id }}</h1>
                <div class="flex justify-end space-x-6">
                    <Link :href="route('sales.index')">
                    <Button variant="outline">Back to Sales</Button>
                    </Link>
                     <Button as="a" :href="route('sales.receipt', sale.id)" variant="default">
                        <PrinterIcon />Receipt
                    </Button>
                </div>
            </div>

            <Card class="mb-4">
                <CardHeader>
                    <CardTitle>Sale Information</CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <strong>Buyer:</strong>
                        <p>{{ props.sale.type }} - {{ props.sale.buyer.name }}</p>
                    </div>
                    <div>
                        <strong>Date:</strong>
                        <p>{{ new Date(props.sale.created_at).toLocaleString() }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="mb-4">
                <CardHeader>
                    <CardTitle>Items Sold</CardTitle>
                </CardHeader>
                <CardContent class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Item Type</TableHead>
                                <TableHead>Item Name</TableHead>
                                <TableHead class="text-right">Quantity</TableHead>
                                <TableHead class="text-right">Price</TableHead>
                                <TableHead class="text-right">Subtotal</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(item, index) in props.sale.items" :key="index">
                                <TableCell>{{ index + 1 }}</TableCell>
                                <TableCell>{{ item.type }}</TableCell>
                                <TableCell>{{ item.sellable.name }}</TableCell>
                                <TableCell class="text-right">{{ item.quantity }}</TableCell>
                                <TableCell class="text-right">{{ currency(item.price) }}</TableCell>
                                <TableCell class="text-right">{{ (item.quantity * item.price) }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Summary</CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <strong>Total:</strong>
                        <p class="text-left">{{ currency(sale.total) }}</p>
                    </div>
                    <div>
                        <strong>Paid:</strong>
                        <p class="text-left">{{ currency(sale.paid) }}</p>
                    </div>
                    <div>
                        <strong>Balance:</strong>
                        <p class="text-left">{{ currency(sale.balance) }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
