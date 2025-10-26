<script lang="ts" setup>
import { computed, ref, watch } from 'vue';
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import currency from '@/modules/currecyFormatter';
import { Search, Calendar, Filter, ChevronDown, Eye, Printer, Trash2 } from 'lucide-vue-next';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';

interface Auth {
    user: {
        id: string;
        name: string;
        email: string;
        roles: {
            name: string;
        }[];
    }
}

const props = defineProps<{
    sales: {
        data: any[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
}>();

const page = usePage();
const user = page.props.auth.user;

const isAdmin = computed(() => {
    return user.roles.some((role: any) => role.name === 'admin');
});

const form = useForm({
    buyer_name: '',
    start_date: '',
    end_date: ''
});

const showFilters = ref(false);

const filteredSales = computed(() => {
    return props.sales.data.filter((sale) => {
        const matchesBuyerName = form.buyer_name
            ? sale.buyer_name.toLowerCase().includes(form.buyer_name.toLowerCase())
            : true;
        const matchesStartDate = form.start_date
            ? new Date(sale.created_at) >= new Date(form.start_date)
            : true;
        const matchesEndDate = form.end_date
            ? new Date(sale.created_at) <= new Date(form.end_date)
            : true;

        return matchesBuyerName && matchesStartDate && matchesEndDate;
    });
});

const stats = computed(() => {
    const sales = filteredSales.value ?? [];

    const totalSales = sales.length;
    const totalAmount = sales.reduce((sum, s) => sum + Number(s.total || 0), 0);
    const totalPaid = sales.reduce((sum, s) => sum + Number(s.paid || 0), 0);
    const totalBalance = sales.reduce((sum, s) => sum + Number(s.balance || 0), 0);
    const paidCount = sales.filter(s => s.status === 'paid').length;

    return {
        totalSales,
        totalAmount,
        totalPaid,
        totalBalance,
        paidCount,
    };
});

watch(form, () => {
    router.get(
        route('sales.index'),
        {
            buyer_name: form.buyer_name,
            start_date: form.start_date,
            end_date: form.end_date,
        },
        { preserveState: true, replace: true }
    );
}, { deep: true });

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Sales', href: route('sales.index') }
];

const cancelSales = (id: string) => {
    router.delete(route('sales.destroy', id), {
        preserveScroll: true,
    });
};

const clearFilters = () => {
    form.buyer_name = '';
    form.start_date = '';
    form.end_date = '';
};

const hasActiveFilters = computed(() => {
    return form.buyer_name || form.start_date || form.end_date;
});

const getStatusConfig = (status: string) => {
    const config: Record<string, any> = {
        paid: {
            bg: 'bg-green-50 dark:bg-green-900/20',
            border: 'border-green-200 dark:border-green-800',
            text: 'text-green-700 dark:text-green-400',
            icon: '✓',
            label: 'Paid'
        },
        partial: {
            bg: 'bg-amber-50 dark:bg-amber-900/20',
            border: 'border-amber-200 dark:border-amber-800',
            text: 'text-amber-700 dark:text-amber-400',
            icon: '◐',
            label: 'Partial'
        },
        unpaid: {
            bg: 'bg-red-50 dark:bg-red-900/20',
            border: 'border-red-200 dark:border-red-800',
            text: 'text-red-700 dark:text-red-400',
            icon: '○',
            label: 'Unpaid'
        },
    };
    return config[status] || config.unpaid;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
                <div class="w-full mx-auto px-4 md:px-6 py-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Sales History</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Track and manage all your sales transactions</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                            <Link :href="route('sales.create')" class="flex-1 sm:flex-none">
                                <Button class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 rounded-lg gap-2 h-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    <span>New Sale</span>
                                </Button>
                            </Link>
                            <a v-if="isAdmin" :href="`/sales/export/pdf?start_date=${form.start_date}&end_date=${form.end_date}`">
                                <Button variant="outline" class="rounded-lg h-10 px-3 sm:px-4">
                                    <span class="text-sm">PDF</span>
                                </Button>
                            </a>
                            <a v-if="isAdmin" :href="`/sales/export/excel?start_date=${form.start_date}&end_date=${form.end_date}`">
                                <Button variant="outline" class="rounded-lg h-10 px-3 sm:px-4">
                                    <span class="text-sm">Excel</span>
                                </Button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full mx-auto px-4 md:px-6 py-6 md:py-8 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Total Sales</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.totalSales }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">transactions</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Total Amount</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ currency(stats.totalAmount) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">all sales</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-green-200 dark:border-green-900 p-4 hover:shadow-md transition-shadow">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Total Paid</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ currency(stats.totalPaid) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ stats.paidCount }} complete</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-amber-200 dark:border-amber-900 p-4 hover:shadow-md transition-shadow">
                        <p class="text-gray-600 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Outstanding</p>
                        <p class="text-2xl md:text-3xl font-bold text-amber-600 dark:text-amber-400 mt-2">{{ currency(stats.totalBalance) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">pending payment</p>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 dark:from-indigo-500 dark:to-indigo-600 rounded-lg shadow-sm p-4 text-white">
                        <p class="text-indigo-100 text-xs font-medium uppercase tracking-wide">Collection %</p>
                        <p class="text-2xl md:text-3xl font-bold mt-2">{{ stats.totalAmount > 0 ? Math.round((stats.totalPaid / stats.totalAmount) * 100) : 0 }}%</p>
                        <p class="text-xs text-indigo-200 mt-1">payment collection</p>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div
                        class="flex items-center justify-between p-4 md:p-6 cursor-pointer border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                        @click="showFilters = !showFilters"
                    >
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                <Filter class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="font-bold text-gray-900 dark:text-white">Filters</h2>
                                <p v-if="hasActiveFilters" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">{{ filteredSales.length }} results</p>
                            </div>
                        </div>
                        <ChevronDown :class="['flex-shrink-0 w-5 h-5 text-gray-400 transition-transform', showFilters && 'rotate-180']" />
                    </div>

                    <transition name="expand">
                        <div v-show="showFilters" class="p-4 md:p-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">Buyer Name</label>
                                    <div class="relative">
                                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 flex-shrink-0" />
                                        <Input
                                            v-model="form.buyer_name"
                                            placeholder="Search by buyer..."
                                            class="pl-9 h-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">From Date</label>
                                    <div class="relative">
                                        <Calendar class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 flex-shrink-0" />
                                        <Input
                                            v-model="form.start_date"
                                            type="date"
                                            class="pl-9 h-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-2">To Date</label>
                                    <div class="relative">
                                        <Calendar class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 flex-shrink-0" />
                                        <Input
                                            v-model="form.end_date"
                                            type="date"
                                            class="pl-9 h-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div v-if="hasActiveFilters" class="flex justify-end pt-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="clearFilters"
                                    class="gap-2 rounded-lg"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Clear Filters
                                </Button>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Sales Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <tr class="text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <th class="py-4 px-4 md:px-6">Date</th>
                                    <th class="py-4 px-4 md:px-6">Buyer</th>
                                    <th class="py-4 px-4 md:px-6 text-center">Items</th>
                                    <th class="py-4 px-4 md:px-6 text-right">Total</th>
                                    <th class="py-4 px-4 md:px-6 text-right">Paid</th>
                                    <th class="py-4 px-4 md:px-6 text-right">Balance</th>
                                    <th class="py-4 px-4 md:px-6 text-center">Status</th>
                                    <th class="py-4 px-4 md:px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-if="filteredSales.length === 0">
                                    <td colspan="8" class="text-center py-12 px-4">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-gray-600 dark:text-gray-400 font-medium">No sales found</p>
                                            <p class="text-gray-500 dark:text-gray-500 text-sm">Try adjusting your filters</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-for="sale in filteredSales"
                                    :key="sale.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group"
                                >
                                    <td class="py-4 px-4 md:px-6 text-sm text-gray-900 dark:text-white font-medium">
                                        {{ new Date(sale.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="py-4 px-4 md:px-6 text-sm text-gray-900 dark:text-white font-semibold truncate">
                                        {{ sale.buyer_name }}
                                    </td>
                                    <td class="py-4 px-4 md:px-6 text-sm text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-xs font-semibold">
                                            {{ sale.items_count }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 md:px-6 text-sm text-right font-bold text-gray-900 dark:text-white">
                                        {{ currency(sale.total) }}
                                    </td>
                                    <td class="py-4 px-4 md:px-6 text-sm text-right font-semibold text-green-600 dark:text-green-400">
                                        {{ currency(sale.paid) }}
                                    </td>
                                    <td :class="['py-4 px-4 md:px-6 text-sm text-right font-semibold', sale.balance > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400']">
                                        {{ currency(sale.balance) }}
                                    </td>
                                    <td class="py-4 px-4 md:px-6 text-center">
                                        <span :class="[
                                            getStatusConfig(sale.status).bg,
                                            getStatusConfig(sale.status).border,
                                            getStatusConfig(sale.status).text,
                                            'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border'
                                        ]">
                                            {{ getStatusConfig(sale.status).icon }}
                                            {{ getStatusConfig(sale.status).label }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 md:px-6 text-right">
                                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('sales.show', sale.id)">
                                                <Button title="View details" variant="ghost" size="icon" class="h-8 w-8 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded">
                                                    <Eye class="w-4 h-4" />
                                                </Button>
                                            </Link>
                                            <Link v-if="sale.status !== 'paid'" :href="route('sales.add-payments', sale.id)">
                                                <Button size="sm" variant="ghost" class="text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded h-8 px-2 text-xs font-medium">
                                                    Pay
                                                </Button>
                                            </Link>
                                            <a :href="route('sales.receipt', sale.id)" target="_blank">
                                                <Button title="Print receipt" size="icon" variant="ghost" class="h-8 w-8 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded">
                                                    <Printer class="w-4 h-4" />
                                                </Button>
                                            </a>
                                            <ConfirmDeleteDialog
                                                :title="`Cancel Sale #${sale.id}`"
                                                :description="`Are you sure you want to cancel sale #${sale.id}? This action cannot be undone.`"
                                                buttonLabel="Cancel"
                                                triggerVariant="ghost"
                                                :onConfirm="() => cancelSales(sale.id)"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="sales.links.length > 3" class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
                    <div class="text-xs md:text-sm text-gray-600 dark:text-gray-400 order-2 sm:order-1">
                        Showing <span class="font-bold text-gray-900 dark:text-white">{{ sales.from }}</span> to
                        <span class="font-bold text-gray-900 dark:text-white">{{ sales.to }}</span> of
                        <span class="font-bold text-gray-900 dark:text-white">{{ sales.total }}</span> sales
                    </div>
                    <div class="flex gap-1 flex-wrap order-1 sm:order-2 justify-center sm:justify-end">
                        <Button
                            v-for="(link, i) in sales.links"
                            :key="i"
                            :disabled="!link.url"
                            :variant="link.active ? 'default' : 'outline'"
                            @click="router.get(link.url)"
                            v-html="link.label"
                            class="min-w-[40px] px-3 py-2 h-9 rounded-lg text-xs md:text-sm"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    transition: all 0.3s ease;
}

.expand-enter-from {
    opacity: 0;
    max-height: 0;
}

.expand-leave-to {
    opacity: 0;
    max-height: 0;
}
</style>
