<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import currency from '@/modules/currecyFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { PrinterIcon, ArrowLeftIcon } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { computed } from 'vue';

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

const balanceColor = computed(() => {
    if (props.sale.balance > 0) return 'text-red-500';
    if (props.sale.balance < 0) return 'text-green-500'; // Overpaid
    return 'text-gray-500'; // Paid in full
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container p-6 mx-auto">
            <Head :title="`Sale Details #${props.sale.id}`" />

            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <h1 class="text-3xl font-bold text-gray-800">Sale #{{ props.sale.id }}</h1>
                    <Badge variant="outline" class="text-xs tracking-wide">
                        {{ new Date(props.sale.created_at).toLocaleDateString() }}
                    </Badge>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('sales.index')">
                        <Button variant="outline">
                            <ArrowLeftIcon class="w-4 h-4 mr-2" />
                            Back to Sales
                        </Button>
                    </Link>
                    <Button as="a" :href="route('sales.receipt', props.sale.id)" variant="default">
                        <PrinterIcon class="w-4 h-4 mr-2" />
                        Print Receipt
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <Card class="col-span-1">
                    <CardHeader>
                        <CardTitle class="text-lg">Buyer Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">Buyer:</span>
                                <span class="font-semibold">{{ props.sale.buyer.name }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">Type:</span>
                                <Badge variant="secondary">{{ props.sale.type }}</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="col-span-2">
                    <CardHeader>
                        <CardTitle class="text-lg">Summary</CardTitle>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Total</p>
                            <p class="text-xl font-bold text-gray-800">{{ currency(props.sale.total) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Paid</p>
                            <p class="text-xl font-bold text-green-600">{{ currency(props.sale.paid) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Balance</p>
                            <p :class="['text-xl font-bold', balanceColor]">{{ currency(props.sale.balance) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Items Sold</CardTitle>
                </CardHeader>
                <CardContent class="p-0 overflow-x-auto">
                    <Table class="w-full">
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[50px]">#</TableHead>
                                <TableHead>Item Name</TableHead>
                                <TableHead class="hidden sm:table-cell">Item Type</TableHead>
                                <TableHead class="text-right">Quantity</TableHead>
                                <TableHead class="text-right">Price</TableHead>
                                <TableHead class="text-right">Subtotal</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(item, index) in props.sale.items" :key="index">
                                <TableCell>{{ index + 1 }}</TableCell>
                                <TableCell class="font-medium">{{ item.sellable.name }}</TableCell>
                                <TableCell class="hidden sm:table-cell">{{ item.type }}</TableCell>
                                <TableCell class="text-right">{{ item.quantity }}</TableCell>
                                <TableCell class="text-right">{{ currency(item.price) }}</TableCell>
                                <TableCell class="text-right font-medium">
                                    {{ currency(item.quantity * item.price) }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
