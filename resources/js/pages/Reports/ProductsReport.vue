<script setup>
import { ref, computed, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import currency from '@/modules/currecyFormatter';

const props = defineProps({
  products: { type: Array, required: true },
  filters: { type: Object, default: () => ({ from: null, to: null }) },
});

// Reactive filters
const fromDate = ref(props.filters.from || "");
const toDate = ref(props.filters.to || "");

// Filtered products
const filteredProducts = computed(() => {
  return props.products.filter(p => {
    const matchesFrom = fromDate.value ? new Date(p.created_at) >= new Date(fromDate.value) : true;
    const matchesTo = toDate.value ? new Date(p.created_at) <= new Date(toDate.value) : true;
    return matchesFrom && matchesTo;
  });
});

// Export CSV
function exportCSV() {
  const headers = ["#", "Name", "Stock Qty", "Buying Price", "Selling Price", "Units Sold", "Revenue", "Cost", "Profit"];
  const rows = filteredProducts.value.map((p, i) => [
    i + 1,
    p.name,
    p.stock_qty,
    currency(p.buying_price),
    currency(p.selling_price),
    p.units_sold,
    currency(p.revenue),
    currency(p.cost),
    currency(p.profit),
  ]);

  // Total row
  const totalRevenue = filteredProducts.value.reduce((acc, p) => acc + p.revenue, 0);
  const totalCost = filteredProducts.value.reduce((acc, p) => acc + p.cost, 0);
  const totalProfit = filteredProducts.value.reduce((acc, p) => acc + p.profit, 0);

  rows.push([]);
  rows.push(["", "", "", "", "", "Totals", currency(totalRevenue), currency(totalCost), currency(totalProfit)]);

  const csvContent = [headers, ...rows].map(r => r.map(c => `"${c}"`).join(",")).join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", "products_report.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Export PDF
function exportPDF() {
  const doc = new jsPDF();
  doc.text("Products Report", 14, 16);
  const rows = filteredProducts.value.map((p, i) => [
    i + 1,
    p.name,
    p.stock_qty,
    currency(p.buying_price),
    currency(p.selling_price),
    p.units_sold,
    currency(p.revenue),
    currency(p.cost),
    currency(p.profit),
  ]);
  autoTable(doc, {
    head: [["#", "Name", "Stock Qty", "Buying Price", "Selling Price", "Units Sold", "Revenue", "Cost", "Profit"]],
    body: rows,
    startY: 20,
    theme: "grid",
  });
  const totalRevenue = filteredProducts.value.reduce((acc, p) => acc + p.revenue, 0);
  const totalCost = filteredProducts.value.reduce((acc, p) => acc + p.cost, 0);
  const totalProfit = filteredProducts.value.reduce((acc, p) => acc + p.profit, 0);
  doc.text(`Totals → Revenue: ${currency(totalRevenue)}, Cost: ${currency(totalCost)}, Profit: ${currency(totalProfit)}`, 14, doc.lastAutoTable.finalY + 10);
  doc.save("products_report.pdf");
}

// Handle filter change with debounce
import debounce from 'lodash/debounce';
const updateFilters = debounce(() => {
  Inertia.get(route("reports.products"), {
    from: fromDate.value,
    to: toDate.value
  }, { preserveState: true, replace: true });
}, 500);

watch([fromDate, toDate], updateFilters);
</script>

<template>
 <AppLayout>
   <Head title="Products Report" />
   <div class="max-w-7xl mx-auto p-6 space-y-6">

     <!-- Filters -->
     <div class="flex flex-col md:flex-row gap-4 items-end">
       <div>
         <label class="block text-sm font-medium text-gray-700">From</label>
         <input type="date" v-model="fromDate" class="mt-1 block border rounded p-2" />
       </div>
       <div>
         <label class="block text-sm font-medium text-gray-700">To</label>
         <input type="date" v-model="toDate" class="mt-1 block border rounded p-2" />
       </div>
       <div class="flex gap-2">
         <button @click="exportCSV" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Export CSV</button>
         <button @click="exportPDF" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Export PDF</button>
       </div>
     </div>

     <!-- Report Table -->
     <div class="overflow-x-auto">
       <table class="min-w-full border border-gray-200 rounded">
         <thead class="bg-gray-700 text-white">
           <tr>
             <th class="p-3 text-left">#</th>
             <th class="p-3 text-left">Name</th>
             <th class="p-3 text-left">Stock Qty</th>
             <th class="p-3 text-left">Buying Price</th>
             <th class="p-3 text-left">Selling Price</th>
             <th class="p-3 text-left">Units Sold</th>
             <th class="p-3 text-left">Revenue</th>
             <th class="p-3 text-left">Cost</th>
             <th class="p-3 text-left">Profit</th>
           </tr>
         </thead>
         <tbody>
           <tr v-for="(p, i) in filteredProducts" :key="p.id" class="border-b">
             <td class="p-3">{{ i + 1 }}</td>
             <td class="p-3">{{ p.name }}</td>
             <td class="p-3">{{ p.stock_qty }}</td>
             <td class="p-3">{{ currency(p.buying_price) }}</td>
             <td class="p-3">{{ currency(p.selling_price) }}</td>
             <td class="p-3">{{ p.units_sold }}</td>
             <td class="p-3">{{ currency(p.revenue) }}</td>
             <td class="p-3">{{ currency(p.cost) }}</td>
             <td class="p-3" :class="p.profit >= 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
               {{ currency(p.profit) }}
             </td>
           </tr>
         </tbody>
         <tfoot class="bg-gray-100 font-bold">
           <tr>
             <td colspan="6" class="p-3 text-right">Totals</td>
             <td class="p-3">{{ currency(filteredProducts.reduce((sum, p) => sum + p.revenue, 0)) }}</td>
             <td class="p-3">{{ currency(filteredProducts.reduce((sum, p) => sum + p.cost, 0)) }}</td>
             <td class="p-3">{{ currency(filteredProducts.reduce((sum, p) => sum + p.profit, 0)) }}</td>
           </tr>
         </tfoot>
       </table>
     </div>
   </div>
 </AppLayout>
</template>
