<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table';
import currency from '@/modules/currecyFormatter';
import AppLayout from '@/layouts/AppLayout.vue';
import { PrinterIcon, ArrowLeftIcon, CheckCircle2, AlertCircle, Clock, Package, User, TrendingUp, DollarSign } from 'lucide-vue-next';
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
        return { label: 'Paid in Full', color: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300', icon: CheckCircle2 };
    }
    if (props.sale.balance > 0) {
        return { label: 'Pending Payment', color: 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300', icon: Clock };
    }
    return { label: 'Overpaid', color: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300', icon: CheckCircle2 };
});

const balanceColor = computed(() => {
    if (props.sale.balance > 0) return 'text-amber-600 dark:text-amber-400';
    if (props.sale.balance < 0) return 'text-blue-600 dark:text-blue-400';
    return 'text-emerald-600 dark:text-emerald-400';
});

const balanceBg = computed(() => {
    if (props.sale.balance > 0) return 'border-l-4 border-l-amber-500';
    if (props.sale.balance < 0) return 'border-l-4 border-l-blue-500';
    return 'border-l-4 border-l-emerald-500';
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

const itemCount = computed(() => props.sale.items.length);
const paymentPercentage = computed(() => Math.round((props.sale.paid / props.sale.total) * 100));
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-50 to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 p-4 sm:p-6">
            <div class="container max-w-6xl mx-auto space-y-6">
                <Head :title="`Sale Details #${props.sale.id}`" />

                <!-- Header with Actions -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <DollarSign class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Sale #{{ props.sale.id.slice(0,8) }}</h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ formattedDate }} at {{ formattedTime }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <Link :href="route('sales.index')" class="flex-1 sm:flex-none">
                            <Button variant="outline" class="gap-2 w-full sm:w-auto">
                                <ArrowLeftIcon class="w-4 h-4" />
                                <span class="hidden sm:inline">Back</span>
                            </Button>
                        </Link>
                        <Button as="a" :href="route('sales.receipt', props.sale.id)" class="flex-1 sm:flex-none gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700">
                            <PrinterIcon class="w-4 h-4" />
                            <span>Print</span>
                        </Button>
                    </div>
                </div>

                <!-- Payment Status Banner -->
                <div :class="['px-4 sm:px-6 py-4 rounded-xl border-2 flex items-start gap-4 w-full', paymentStatus.color]">
                    <component :is="paymentStatus.icon" class="w-6 h-6 flex-shrink-0 mt-0.5" />
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-base sm:text-lg">{{ paymentStatus.label }}</p>
                        <p class="text-xs sm:text-sm opacity-90 mt-1">{{ props.sale.type }} Sale • {{ itemCount }} {{ itemCount === 1 ? 'item' : 'items' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="w-20 h-2 bg-current bg-opacity-20 rounded-full overflow-hidden">
                            <div class="h-full bg-current transition-all duration-500" :style="{ width: `${paymentPercentage}%` }"></div>
                        </div>
                        <p class="text-xs mt-1 font-semibold">{{ paymentPercentage }}%</p>
                    </div>
                </div>

                <!-- Summary Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Buyer Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Buyer</p>
                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <User class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate">{{ props.sale.buyer.name }}</p>
                    </div>

                    <!-- Total Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Amount</p>
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <TrendingUp class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ currency(props.sale.total) }}</p>
                    </div>

                    <!-- Paid Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-l-emerald-500 p-6 hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Amount Paid</p>
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <CheckCircle2 class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ currency(props.sale.paid) }}</p>
                    </div>

                    <!-- Balance Card -->
                    <div :class="['bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 p-6 hover:shadow-md transition-all group', balanceBg]">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Remaining</p>
                            <div :class="[
                                'w-8 h-8 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform',
                                props.sale.balance > 0 ? 'bg-amber-100 dark:bg-amber-900/30' : props.sale.balance < 0 ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-emerald-100 dark:bg-emerald-900/30'
                            ]">
                                <AlertCircle v-if="props.sale.balance > 0" class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                                <CheckCircle2 v-else class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        </div>
                        <p :class="['text-2xl font-bold', balanceColor]">{{ currency(props.sale.balance) }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-700 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <Package class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Sold Items</h2>
                            <Badge class="ml-auto bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{{ itemCount }} {{ itemCount === 1 ? 'item' : 'items' }}</Badge>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <Table class="w-full">
                            <TableHeader class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <TableRow>
                                    <TableHead class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">#</TableHead>
                                    <TableHead class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Item Name</TableHead>
                                    <TableHead class="hidden sm:table-cell text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Type</TableHead>
                                    <TableHead class="text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Qty</TableHead>
                                    <TableHead class="text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Unit Price</TableHead>
                                    <TableHead class="text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Subtotal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(item, index) in props.sale.items" :key="index" class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <TableCell class="text-sm text-gray-600 dark:text-gray-400 font-medium px-6 py-4">{{ index + 1 }}</TableCell>
                                    <TableCell class="text-sm font-semibold text-gray-900 dark:text-white px-6 py-4">{{ item.sellable.name }}</TableCell>
                                    <TableCell class="hidden sm:table-cell text-sm text-gray-600 dark:text-gray-400 px-6 py-4">
                                        <Badge :class="[
                                            'text-xs font-medium',
                                            item.type === 'Product' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'
                                        ]">
                                            {{ item.type }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right text-sm text-gray-600 dark:text-gray-400 px-6 py-4">
                                        <span class="inline-block bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-lg font-semibold">{{ item.quantity }}</span>
                                    </TableCell>
                                    <TableCell class="text-right text-sm text-gray-600 dark:text-gray-400 px-6 py-4">{{ currency(item.price) }}</TableCell>
                                    <TableCell class="text-right text-sm font-bold text-gray-900 dark:text-white px-6 py-4">
                                        {{ currency(item.quantity * item.price) }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- Table Footer with Totals -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                        <div class="flex justify-end items-center gap-8">
                            <div class="text-right">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-1">Total Sale</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ currency(props.sale.total) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
