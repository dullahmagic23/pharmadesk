<script setup>
import { ref, computed, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import currency from '@/modules/currecyFormatter';
import { DownloadIcon, FileTextIcon, TrendingUpIcon, DollarSignIcon, ShoppingCartIcon, AlertCircleIcon } from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps({
  products: { type: Array, required: true },
  filters: { type: Object, default: () => ({ from: null, to: null }) },
});

const fromDate = ref(props.filters.from || "");
const toDate = ref(props.filters.to || "");
const searchQuery = ref("");
const sortBy = ref("name");
const sortOrder = ref("asc");

const filteredProducts = computed(() => {
  let filtered = props.products.filter(p => {
    const matchesDate = (
      (!fromDate.value || new Date(p.created_at) >= new Date(fromDate.value)) &&
      (!toDate.value || new Date(p.created_at) <= new Date(toDate.value))
    );
    const matchesSearch = p.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesDate && matchesSearch;
  });

  // Sort
  filtered.sort((a, b) => {
    let aVal, bVal;
    switch (sortBy.value) {
      case "revenue":
        aVal = a.revenue;
        bVal = b.revenue;
        break;
      case "profit":
        aVal = a.profit;
        bVal = b.profit;
        break;
      case "sold":
        aVal = a.units_sold;
        bVal = b.units_sold;
        break;
      case "stock":
        aVal = a.stock_qty;
        bVal = b.stock_qty;
        break;
      default:
        aVal = a.name.toLowerCase();
        bVal = b.name.toLowerCase();
    }
    return sortOrder.value === "asc" ? (aVal > bVal ? 1 : -1) : (aVal < bVal ? 1 : -1);
  });

  return filtered;
});

const totalRevenue = computed(() => filteredProducts.value.reduce((acc, p) => acc + p.revenue, 0));
const totalCost = computed(() => filteredProducts.value.reduce((acc, p) => acc + p.cost, 0));
const totalProfit = computed(() => filteredProducts.value.reduce((acc, p) => acc + p.profit, 0));
const totalUnitsSold = computed(() => filteredProducts.value.reduce((acc, p) => acc + p.units_sold, 0));
const avgProfitMargin = computed(() =>
  totalRevenue.value > 0 ? ((totalProfit.value / totalRevenue.value) * 100).toFixed(1) : 0
);

const lossProducts = computed(() =>
  filteredProducts.value.filter(p => p.profit < 0)
);

const topProduct = computed(() =>
  filteredProducts.value.reduce((max, p) => (p.profit > max.profit ? p : max), filteredProducts.value[0] || {})
);

const toggleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
  } else {
    sortBy.value = column;
    sortOrder.value = "asc";
  }
};

const getSortIcon = (column) => {
  if (sortBy.value !== column) return " ⇅";
  return sortOrder.value === "asc" ? " ↑" : " ↓";
};

function exportCSV() {
  const headers = ["#", "Name", "Stock Qty", "Buying Price", "Selling Price", "Units Sold", "Revenue", "Cost", "Profit", "Margin %"];
  const rows = filteredProducts.value.map((p, i) => [
    i + 1,
    p.name,
    p.stock_qty,
    p.buying_price,
    p.selling_price,
    p.units_sold,
    p.revenue,
    p.cost,
    p.profit,
    ((p.profit / p.revenue) * 100).toFixed(1) || "0",
  ]);

  rows.push([]);
  rows.push(["", "", "", "", "", "Totals", totalRevenue.value, totalCost.value, totalProfit.value, avgProfitMargin.value]);

  const csvContent = [headers, ...rows].map(r => r.map(c => `"${c}"`).join(",")).join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", `products_report_${new Date().toISOString().split('T')[0]}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function exportPDF() {
  const doc = new jsPDF();
  doc.setFont(undefined, "bold");
  doc.setFontSize(16);
  doc.text("Products Report", 14, 16);
  doc.setFont(undefined, "normal");
  doc.setFontSize(10);
  doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 24);

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
    head: [["#", "Name", "Stock", "Buy Price", "Sell Price", "Sold", "Revenue", "Cost", "Profit"]],
    body: rows,
    startY: 32,
    theme: "striped",
    headStyles: { fillColor: [15, 23, 42], textColor: [255, 255, 255], fontStyle: "bold" },
    alternateRowStyles: { fillColor: [248, 250, 252] },
    styles: { fontSize: 9, cellPadding: 3 }
  });

  const finalY = doc.lastAutoTable.finalY + 10;
  doc.setFont(undefined, "bold");
  doc.text(`Total Revenue: ${currency(totalRevenue.value)}`, 14, finalY);
  doc.text(`Total Cost: ${currency(totalCost.value)}`, 14, finalY + 7);
  doc.text(`Total Profit: ${currency(totalProfit.value)}`, 14, finalY + 14);
  doc.text(`Profit Margin: ${avgProfitMargin.value}%`, 14, finalY + 21);

  doc.save(`products_report_${new Date().toISOString().split('T')[0]}.pdf`);
}

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
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-6">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Products Report</h1>
        <p class="text-slate-600 mt-1">Analyze product performance and profitability</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <p class="text-sm font-medium text-slate-600 mb-2">Total Products</p>
          <p class="text-2xl font-bold text-slate-900">{{ filteredProducts.length }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600 mb-2">Total Revenue</p>
              <p class="text-2xl font-bold text-blue-600">{{ currency(totalRevenue) }}</p>
            </div>
            <DollarSignIcon class="w-8 h-8 text-blue-200" />
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600 mb-2">Total Cost</p>
              <p class="text-2xl font-bold text-orange-600">{{ currency(totalCost) }}</p>
            </div>
            <ShoppingCartIcon class="w-8 h-8 text-orange-200" />
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600 mb-2">Total Profit</p>
              <p :class="['text-2xl font-bold', totalProfit >= 0 ? 'text-green-600' : 'text-red-600']">
                {{ currency(totalProfit) }}
              </p>
            </div>
            <TrendingUpIcon class="w-8 h-8" :class="totalProfit >= 0 ? 'text-green-200' : 'text-red-200'" />
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <p class="text-sm font-medium text-slate-600 mb-2">Profit Margin</p>
          <p class="text-2xl font-bold text-purple-600">{{ avgProfitMargin }}%</p>
          <p class="text-xs text-slate-500 mt-2">{{ totalUnitsSold }} units sold</p>
        </div>
      </div>

      <!-- Alerts -->
      <div v-if="lossProducts.length > 0" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
        <AlertCircleIcon class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
        <div>
          <p class="font-semibold text-red-900">{{ lossProducts.length }} product(s) with losses</p>
          <p class="text-sm text-red-700">{{ lossProducts.map(p => p.name).join(', ') }}</p>
        </div>
      </div>

      <div v-if="topProduct.name" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
        <TrendingUpIcon class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" />
        <div>
          <p class="font-semibold text-green-900">Top Performer</p>
          <p class="text-sm text-green-700">{{ topProduct.name }} with {{ currency(topProduct.profit) }} profit</p>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">From</label>
            <input type="date" v-model="fromDate" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">To</label>
            <input type="date" v-model="toDate" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Search</label>
            <input type="text" v-model="searchQuery" placeholder="Search products..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Sort by</label>
            <select v-model="sortBy" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
              <option value="name">Name</option>
              <option value="revenue">Revenue</option>
              <option value="profit">Profit</option>
              <option value="sold">Units Sold</option>
              <option value="stock">Stock</option>
            </select>
          </div>
          <div class="flex gap-2 sm:col-span-2 lg:col-span-1 lg:justify-end">
            <button @click="exportCSV" class="flex-1 lg:flex-initial flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
              <DownloadIcon class="w-4 h-4" />
              <span class="hidden sm:inline">CSV</span>
            </button>
            <button @click="exportPDF" class="flex-1 lg:flex-initial flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
              <FileTextIcon class="w-4 h-4" />
              <span class="hidden sm:inline">PDF</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left font-semibold text-slate-700">#</th>
                <th class="px-6 py-4 text-left font-semibold text-slate-700 cursor-pointer hover:bg-slate-100" @click="toggleSort('name')">
                  Name {{ getSortIcon('name') }}
                </th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100" @click="toggleSort('stock')">
                  Stock {{ getSortIcon('stock') }}
                </th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700">Buy Price</th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700">Sell Price</th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100" @click="toggleSort('sold')">
                  Sold {{ getSortIcon('sold') }}
                </th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100" @click="toggleSort('revenue')">
                  Revenue {{ getSortIcon('revenue') }}
                </th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700">Cost</th>
                <th class="px-6 py-4 text-right font-semibold text-slate-700 cursor-pointer hover:bg-slate-100" @click="toggleSort('profit')">
                  Profit {{ getSortIcon('profit') }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="(p, i) in filteredProducts" :key="p.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 text-slate-900">{{ i + 1 }}</td>
                <td class="px-6 py-4 font-medium text-slate-900">{{ p.name }}</td>
                <td class="px-6 py-4 text-right text-slate-600">{{ p.stock_qty }}</td>
                <td class="px-6 py-4 text-right text-slate-600">{{ currency(p.buying_price) }}</td>
                <td class="px-6 py-4 text-right text-slate-600">{{ currency(p.selling_price) }}</td>
                <td class="px-6 py-4 text-right text-slate-900 font-medium">{{ p.units_sold }}</td>
                <td class="px-6 py-4 text-right text-slate-900 font-medium">{{ currency(p.revenue) }}</td>
                <td class="px-6 py-4 text-right text-slate-600">{{ currency(p.cost) }}</td>
                <td class="px-6 py-4 text-right font-semibold" :class="p.profit >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ currency(p.profit) }}
                </td>
              </tr>
            </tbody>
            <tfoot v-if="filteredProducts.length" class="bg-slate-50 border-t-2 border-slate-300">
              <tr class="font-semibold">
                <td colspan="5" class="px-6 py-4 text-right text-slate-900">Totals</td>
                <td class="px-6 py-4 text-right text-slate-900">{{ totalUnitsSold }}</td>
                <td class="px-6 py-4 text-right text-blue-600">{{ currency(totalRevenue) }}</td>
                <td class="px-6 py-4 text-right text-orange-600">{{ currency(totalCost) }}</td>
                <td class="px-6 py-4 text-right" :class="totalProfit >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ currency(totalProfit) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="!filteredProducts.length" class="px-6 py-16 text-center">
          <ShoppingCartIcon class="w-12 h-12 text-slate-300 mx-auto mb-4" />
          <p class="text-slate-500 text-lg">No products found</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
