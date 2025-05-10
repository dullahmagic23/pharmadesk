<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import html2canvas from 'html2canvas-pro';
import jsPDF from 'jspdf';
import * as XLSX from 'xlsx';

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js';
import { Bar } from 'vue-chartjs';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';



// Register Chart.js components correctly
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

// Props
const props = defineProps({
    purchases: Array,
    vendors: Array,
});

// Refs
const reportRef = ref(null);

// Filters
const filters = ref({
    startDate: '',
    endDate: '',
    vendorId: '',
});

// Filtered Purchases
const filteredPurchases = computed(() => {
    return props.purchases.filter(p => {
        const date = new Date(p.created_at);
        const start = filters.value.startDate ? new Date(filters.value.startDate) : null;
        const end = filters.value.endDate ? new Date(filters.value.endDate) : null;
        const matchesDate =
            (!start || date >= start) && (!end || date <= end);
        const matchesVendor =
            !filters.vendorId || p.vendor_id === parseInt(filters.value.vendorId);

        return matchesDate && matchesVendor;
    });
});

// Chart Data
const chartData = computed(() => {
    let products = 0;
    let medicines = 0;

    filteredPurchases.value.forEach(p => {
        p.purchasables.forEach(item => {
            if (item.purchasable_type.includes('Product')) {
                products += item.quantity * item.unit_cost;
            } else if (item.purchasable_type.includes('Medicine')) {
                medicines += item.quantity * item.unit_cost;
            }
        });
    });

    return {
        labels: ['Products', 'Medicines'],
        datasets: [{
            label: 'Purchase Totals',
            data: [products, medicines],
            backgroundColor: ['#3B82F6', '#10B981'],
        }],
    };
});

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { position: 'top' },
        title: { display: true, text: 'Purchase Summary' },
    },
};

// Export Functions
const exportToPDF = async () => {
    const params = new URLSearchParams(filters.value).toString();
    window.open(`/reports/purchases/pdf?${params}`, '_blank');
};


const exportToExcel = () => {
    const rows = filteredPurchases.value.flatMap(p =>
        p.purchasables.map(item => ({
            Vendor: p.vendor?.name || '—',
            Date: p.created_at,
            Type: item.purchasable_type.split('\\').pop(),
            Item: item.purchasable?.name || '—',
            Quantity: item.quantity,
            'Unit Price': item.unit_cost,
            Total: item.quantity * item.unit_cost,
        }))
    );

    const worksheet = XLSX.utils.json_to_sheet(rows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Purchases');
    XLSX.writeFile(workbook, 'purchase-report.xlsx');
};

const breadcrumbs = [
    {title:"Reports", href:'/reports'},
    {title:"Purchases Report", href:'/reports/purchases'},
]
</script>


<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Purchases Report" />
        <div class="p-4">
            <!-- Filters -->
            <div class="flex flex-wrap gap-4 mb-6">
                <label class="flex flex-col">
                    From:
                    <input type="date" v-model="filters.startDate" class="border rounded px-2 py-1" />
                </label>
                <label class="flex flex-col">
                    To:
                    <input type="date" v-model="filters.endDate" class="border rounded px-2 py-1" />
                </label>
                <label class="flex flex-col">
                    Vendor:
                    <select v-model="filters.vendorId" class="border rounded px-2 py-1">
                        <option value="">All</option>
                        <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                            {{ vendor.name }}
                        </option>
                    </select>
                </label>
                <Button @click="exportToPDF" class="bg-red-500 text-white px-4 py-2 rounded shadow">Export PDF</Button>
                <Button @click="exportToExcel" class="bg-green-600 text-white px-4 py-2 rounded shadow">Export
                    Excel</Button>
            </div>



            <!-- Report Table -->
            <div ref="reportRef" class="overflow-x-auto bg-white shadow rounded">
                <table class="min-w-full text-sm text-left table-auto border">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 border">Vendor</th>
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border">Item Type</th>
                            <th class="px-4 py-2 border">Item</th>
                            <th class="px-4 py-2 border">Quantity</th>
                            <th class="px-4 py-2 border">Unit Cost</th>
                            <th class="px-4 py-2 border">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="purchase in filteredPurchases" :key="purchase.id">
                            <tr v-for="item in purchase.purchasables" :key="item.id">
                                <td class="px-4 py-2 border">{{ purchase.vendor?.name || '—' }}</td>
                                <td class="px-4 py-2 border">{{ purchase.created_at }}</td>
                                <td class="px-4 py-2 border">{{ item.purchasable_type.split('\\').pop() }}</td>
                                <td class="px-4 py-2 border">{{ item.purchasable?.name || '—' }}</td>
                                <td class="px-4 py-2 border">{{ item.quantity }}</td>
                                <td class="px-4 py-2 border">{{ item.unit_cost }}</td>
                                <td class="px-4 py-2 border">{{ item.quantity * item.unit_cost }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <!-- Chart -->
            <!-- <div class="flex mb-6 mt-6 justify-center">
                <div class="w-1/4">
                    <Bar :data="chartData" :options="chartOptions" />
                </div>
            </div> -->
        </div>
    </AppLayout>
</template>
