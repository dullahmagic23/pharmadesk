<script setup>
import { ref, computed, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import currency from '@/modules/currecyFormatter'

const props = defineProps({
  medicines: { type: Array, required: true },
  categories: { type: Array, required: true },
  filters: { type: Object, default: () => ({ category: null, from: null, to: null }) },
});

// Reactive filters
const selectedCategory = ref(props.filters.category || "");
const fromDate = ref(props.filters.from || "");
const toDate = ref(props.filters.to || "");

// Filtered medicines
const filteredMedicines = computed(() => {
  return props.medicines.filter(m => {
    const matchesCategory = selectedCategory.value ? m.category === selectedCategory.value : true;
    const matchesFrom = fromDate.value ? new Date(m.created_at) >= new Date(fromDate.value) : true;
    const matchesTo = toDate.value ? new Date(m.created_at) <= new Date(toDate.value) : true;
    return matchesCategory && matchesFrom && matchesTo;
  });
});

// Export CSV
function exportCSV() {
  const headers = ["#", "Name", "Category", "Stock Qty", "Buying Price", "Selling Price", "Units Sold", "Revenue", "Cost", "Profit"];
  const rows = filteredMedicines.value.map((m, i) => [
    i + 1,
    m.name,
    m.category || "—",
    m.stock_qty,
    currency(m.buying_price),
    currency(m.selling_price),
    m.units_sold,
    currency(m.revenue),
    currency(m.cost),
    currency(m.profit),
  ]);

  // Total row
  const totalRevenue = filteredMedicines.value.reduce((acc, m) => acc + m.revenue, 0);
  const totalCost = filteredMedicines.value.reduce((acc, m) => acc + m.cost, 0);
  const totalProfit = filteredMedicines.value.reduce((acc, m) => acc + m.profit, 0);

  rows.push([]);
  rows.push(["", "", "", "", "", "Totals", "", currency(totalRevenue), currency(totalCost), currency(totalProfit)]);

  const csvContent = [headers, ...rows].map(r => r.map(c => `"${c}"`).join(",")).join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", "medicine_report.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Export PDF
function exportPDF() {
  const doc = new jsPDF();
  doc.text("Medicine Report", 14, 16);
  const rows = filteredMedicines.value.map((m, i) => [
    i + 1,
    m.name,
    m.category || "—",
    m.stock_qty,
    m.buying_price,
    m.selling_price,
    m.units_sold,
    m.revenue,
    m.cost,
    m.profit,
  ]);
  autoTable(doc, {
    head: [["#", "Name", "Category", "Stock Qty", "Buying Price", "Selling Price", "Units Sold", "Revenue", "Cost", "Profit"]],
    body: rows,
    startY: 20,
    theme: "grid",
  });
  doc.save("medicine_report.pdf");
}

// Handle filter change
watch([selectedCategory, fromDate, toDate], () => {
  Inertia.get(route("reports.medicine"), {
    category: selectedCategory.value,
    from: fromDate.value,
    to: toDate.value
  }, { preserveState: true, replace: true });
});
</script>

<template>
 <AppLayout>
   <Head title="Medicine Reports" />
   <div class="max-w-7xl mx-auto p-6 space-y-6">

     <!-- Filters -->
     <div class="flex flex-col md:flex-row gap-4 items-end">
       <div>
         <label class="block text-sm font-medium text-gray-700">Category</label>
         <select v-model="selectedCategory" class="mt-1 block w-full border rounded p-2">
           <option value="">All</option>
           <option v-for="c in categories" :key="c.id" :value="c.name">{{ c.name }}</option>
         </select>
       </div>
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
             <th class="p-3 text-left">Category</th>
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
           <tr v-for="(m, i) in filteredMedicines" :key="m.id" class="border-b">
             <td class="p-3">{{ i + 1 }}</td>
             <td class="p-3">{{ m.name }}</td>
             <td class="p-3">{{ m.category || '—' }}</td>
             <td class="p-3">{{ m.stock_qty }}</td>
             <td class="p-3">{{ currency(m.buying_price) }}</td>
             <td class="p-3">{{ currency(m.selling_price) }}</td>
             <td class="p-3">{{ m.units_sold }}</td>
             <td class="p-3">{{ currency(m.revenue) }}</td>
             <td class="p-3">{{ currency(m.cost) }}</td>
             <td class="p-3">{{ currency(m.profit) }}</td>
           </tr>
         </tbody>
       </table>
     </div>
   </div>
 </AppLayout>
</template>
