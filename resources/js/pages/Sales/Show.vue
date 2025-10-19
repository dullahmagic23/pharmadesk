<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import currency from '@/modules/currecyFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { PrinterIcon, ArrowLeftIcon, CheckCircle2, AlertCircle, Clock } from 'lucide-vue-next';
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

const paymentStatus = computed(() => {
    if (props.sale.balance === 0) {
        return { label: 'Paid in Full', color: 'bg-green-50 border-green-200 text-green-700', icon: CheckCircle2 };
    }
    if (props.sale.balance > 0) {
        return { label: 'Pending', color: 'bg-amber-50 border-amber-200 text-amber-700', icon: Clock };
    }
    return { label: 'Overpaid', color: 'bg-blue-50 border-blue-200 text-blue-700', icon: CheckCircle2 };
});

const balanceColor = computed(() => {
    if (props.sale.balance > 0) return 'text-amber-600';
    if (props.sale.balance < 0) return 'text-blue-600';
    return 'text-gray-600';
});

const formattedDate = computed(() => {
    return new Date(props.sale.created_at).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
});

const formattedTime = computed(() => {
    return new Date(props.sale.created_at).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
            <div class="container max-w-6xl mx-auto">
                <Head :title="`Sale Details #${props.sale.id}`" />

                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900 mb-2">Sale #{{ props.sale.id }}</h1>
                            <p class="text-gray-500">{{ formattedDate }} at {{ formattedTime }}</p>
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('sales.index')">
                                <Button variant="outline" class="gap-2">
                                    <ArrowLeftIcon class="w-4 h-4" />
                                    Back
                                </Button>
                            </Link>
                            <Button as="a" :href="route('sales.receipt', props.sale.id)" class="gap-2">
                                <PrinterIcon class="w-4 h-4" />
                                Print Receipt
                            </Button>
                        </div>
                    </div>

                    <!-- Payment Status Banner -->
                    <div :class="['px-4 py-3 rounded-lg border-2 flex items-center gap-3 w-fit', paymentStatus.color]">
                        <component :is="paymentStatus.icon" class="w-5 h-5 flex-shrink-0" />
                        <div>
                            <p class="font-semibold text-sm">{{ paymentStatus.label }}</p>
                            <p class="text-xs opacity-90">{{ props.sale.type }} Sale</p>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
                    <!-- Buyer Card -->
                    <Card class="md:col-span-1 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-semibold text-gray-600">Buyer</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-xl font-bold text-gray-900 truncate">{{ props.sale.buyer.name }}</p>
                        </CardContent>
                    </Card>

                    <!-- Total Card -->
                    <Card class="md:col-span-1 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-semibold text-gray-600">Total Amount</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold text-gray-900">{{ currency(props.sale.total) }}</p>
                        </CardContent>
                    </Card>

                    <!-- Paid Card -->
                    <Card class="md:col-span-1 bg-white shadow-sm hover:shadow-md transition-shadow border-l-4 border-l-green-500">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-semibold text-gray-600">Amount Paid</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold text-green-600">{{ currency(props.sale.paid) }}</p>
                        </CardContent>
                    </Card>

                    <!-- Balance Card -->
                    <Card :class="['md:col-span-1 bg-white shadow-sm hover:shadow-md transition-shadow', props.sale.balance > 0 ? 'border-l-4 border-l-amber-500' : props.sale.balance < 0 ? 'border-l-4 border-l-blue-500' : 'border-l-4 border-l-gray-300']">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-sm font-semibold text-gray-600">Remaining Balance</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p :class="['text-2xl font-bold', balanceColor]">{{ currency(props.sale.balance) }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Items Table -->
                <Card class="bg-white shadow-sm overflow-hidden">
                    <CardHeader class="border-b bg-gray-50 py-4">
                        <CardTitle class="text-lg font-bold text-gray-900">Items Sold</CardTitle>
                    </CardHeader>
                    <CardContent class="p-0 overflow-x-auto">
                        <Table class="w-full">
                            <TableHeader class="bg-gray-50 border-b">
                                <TableRow class="hover:bg-gray-50">
                                    <TableHead class="w-12 font-semibold text-gray-700">#</TableHead>
                                    <TableHead class="font-semibold text-gray-700">Item Name</TableHead>
                                    <TableHead class="hidden sm:table-cell font-semibold text-gray-700">Type</TableHead>
                                    <TableHead class="text-right font-semibold text-gray-700">Qty</TableHead>
                                    <TableHead class="text-right font-semibold text-gray-700">Unit Price</TableHead>
                                    <TableHead class="text-right font-semibold text-gray-700">Subtotal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(item, index) in props.sale.items" :key="index" class="border-b hover:bg-gray-50 transition-colors">
                                    <TableCell class="text-gray-600 font-medium">{{ index + 1 }}</TableCell>
                                    <TableCell class="font-semibold text-gray-900">{{ item.sellable.name }}</TableCell>
                                    <TableCell class="hidden sm:table-cell text-gray-600">
                                        <Badge variant="outline" class="text-xs">{{ item.type }}</Badge>
                                    </TableCell>
                                    <TableCell class="text-right text-gray-600">{{ item.quantity }}</TableCell>
                                    <TableCell class="text-right text-gray-600">{{ currency(item.price) }}</TableCell>
                                    <TableCell class="text-right font-semibold text-gray-900">
                                        {{ currency(item.quantity * item.price) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
