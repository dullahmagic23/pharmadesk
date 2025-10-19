<script setup>
import { ref, onMounted, computed } from "vue";
import Chart from "chart.js/auto";
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import axios from "axios";
import currency from "@/modules/currecyFormatter";

const props = defineProps({
  userName: { type: String, required: true },
  userRole: { type: String, required: true },
  dailyQuote: { type: String, required: true },
  expiredItems: { type: Array, default: () => [] },
  expiringItems: { type: Array, default: () => [] },
  lowStockItems: { type: Array, default: () => [] },
  metrics: {
    type: Object,
    default: () => ({
      systemHealth: 75,
      activeUsers: 0,
      revenue: 0,
      orders: 0,
    }),
  },
  salesChartData: { type: Object, required: true },
  productsChartData: { type: Object, required: true },
  activities: { type: Array, default: () => [] },
});

const currentTime = ref("");
const query = ref("");
const results = ref([]);
const suggestions = ref([]);
const showResults = ref(false);
const showSuggestions = ref(false);
const loading = ref(false);
const searchType = ref("all");
const sortBy = ref("relevance");
const selectedResultIndex = ref(-1);
const searchStats = ref({ totalResults: 0, searchTime: 0, lastQuery: "" });

let searchTimeout = null;
let suggestionsTimeout = null;

onMounted(() => {
  setInterval(() => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString();
  }, 1000);

  initCharts();
  document.addEventListener('click', handleClickOutside);
});

const initCharts = () => {
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        labels: { color: "#6b7280", font: { size: 12 } },
      },
      tooltip: {
        backgroundColor: "#1f2937",
        titleColor: "#fff",
        bodyColor: "#fff",
        padding: 12,
        borderRadius: 8,
      },
    },
  };

  const salesCtx = document.getElementById("salesChart");
  if (salesCtx) {
    new Chart(salesCtx, {
      type: "line",
      data: props.salesChartData,
      options: {
        ...chartOptions,
        elements: {
          line: { tension: 0.4, borderWidth: 3 },
          point: { radius: 4, backgroundColor: "#6366f1", borderWidth: 0 },
        },
      },
    });
  }

  const productsCtx = document.getElementById("productsChart");
  if (productsCtx) {
    new Chart(productsCtx, {
      type: "bar",
      data: props.productsChartData,
      options: {
        ...chartOptions,
        scales: {
          x: { ticks: { color: "#9ca3af" } },
          y: { ticks: { color: "#9ca3af" }, beginAtZero: true },
        },
      },
    });
  }
};

const hasResults = computed(() => results.value.length > 0);
const hasSuggestions = computed(() => suggestions.value.length > 0);
const isValidQuery = computed(() => query.value.length >= 2);

const onSearch = () => {
  clearTimeout(searchTimeout);
  clearTimeout(suggestionsTimeout);

  if (!query.value) {
    results.value = [];
    suggestions.value = [];
    showResults.value = false;
    showSuggestions.value = false;
    return;
  }

  if (query.value.length >= 1 && query.value.length < 2) {
    suggestionsTimeout = setTimeout(getSuggestions, 200);
    return;
  }

  if (query.value.length >= 2) {
    loading.value = true;
    searchTimeout = setTimeout(performSearch, 300);
  }
};

const getSuggestions = async () => {
  try {
    const { data } = await axios.get("/search/suggestions", {
      params: { q: query.value },
    });
    suggestions.value = data;
    showSuggestions.value = data.length > 0;
    showResults.value = false;
  } catch (error) {
    console.error("Suggestions error:", error);
  }
};

const performSearch = async () => {
  const startTime = Date.now();
  try {
    const { data } = await axios.get("/search/advanced", {
      params: {
        q: query.value,
        type: searchType.value,
        sort: sortBy.value,
        limit: 10,
      },
    });

    results.value = data.data || data;
    showResults.value = results.value.length > 0;
    showSuggestions.value = false;
    searchStats.value = {
      totalResults: data.meta?.total_results || results.value.length,
      searchTime: Date.now() - startTime,
      lastQuery: query.value,
    };
  } catch (error) {
    console.error("Search error:", error);
    results.value = [];
    showResults.value = false;
  } finally {
    loading.value = false;
  }
};

const handleKeyDown = (event) => {
  if (!showResults.value || results.value.length === 0) return;

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault();
      selectedResultIndex.value = Math.min(selectedResultIndex.value + 1, results.value.length - 1);
      break;
    case 'ArrowUp':
      event.preventDefault();
      selectedResultIndex.value = Math.max(selectedResultIndex.value - 1, -1);
      break;
    case 'Enter':
      event.preventDefault();
      if (selectedResultIndex.value >= 0) {
        goTo(results.value[selectedResultIndex.value]);
      }
      break;
    case 'Escape':
      clearSearch();
      break;
  }
};

const handleClickOutside = (event) => {
  const searchContainer = document.querySelector('.search-container');
  if (searchContainer && !searchContainer.contains(event.target)) {
    showResults.value = false;
    showSuggestions.value = false;
  }
};

const useSuggestion = (suggestion) => {
  query.value = suggestion;
  showSuggestions.value = false;
  performSearch();
};

const clearSearch = () => {
  query.value = "";
  results.value = [];
  suggestions.value = [];
  showResults.value = false;
  showSuggestions.value = false;
};

const goTo = (item) => {
  clearSearch();
  router.visit(item.url);
};

const getTypeIcon = (type) => {
  const icons = { Medicine: '💊', Product: '📦', Customer: '👤' };
  return icons[type] || '🔍';
};

const getTypeColor = (type) => {
  const colors = {
    Medicine: 'bg-green-100 text-green-700',
    Product: 'bg-blue-100 text-blue-700',
    Customer: 'bg-purple-100 text-purple-700',
  };
  return colors[type] || 'bg-gray-100 text-gray-700';
};

const formatPrice = (price) => price ? `$${parseFloat(price).toFixed(2)}` : '-';
</script>

<template>
  <AppLayout>
    <Head title="Admin Dashboard" />
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
      <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              Welcome back, {{ userName }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
              {{ userRole }} · {{ currentTime }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 rounded-full text-sm font-medium">
              {{ userRole }}
            </span>
          </div>
        </div>

        <!-- Advanced Search -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 search-container">
          <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
              <input
                type="text"
                v-model="query"
                @input="onSearch"
                @keydown="handleKeyDown"
                placeholder="Search medicines, products, customers..."
                class="w-full px-4 py-3 pl-10 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
              />
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              <button
                v-if="query"
                @click="clearSearch"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <i class="fas fa-times"></i>
              </button>
              <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                <div class="animate-spin h-5 w-5 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
              </div>
            </div>

            <select
              v-model="searchType"
              @change="performSearch"
              class="px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="all">All Types</option>
              <option value="medicine">Medicines</option>
              <option value="product">Products</option>
              <option value="customer">Customers</option>
            </select>
          </div>

          <!-- Search Results Dropdown -->
          <transition name="fade">
            <div
              v-if="showResults && hasResults"
              class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden max-h-96 overflow-y-auto"
            >
              <div class="p-3 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-400">
                {{ searchStats.totalResults }} results ({{ searchStats.searchTime }}ms)
              </div>
              <div
                v-for="(item, index) in results"
                :key="index"
                @click="goTo(item)"
                :class="[
                  'p-4 hover:bg-indigo-50 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition',
                  selectedResultIndex === index ? 'bg-indigo-100 dark:bg-gray-600' : ''
                ]"
              >
                <div class="flex items-start gap-3">
                  <span class="text-lg flex-shrink-0">{{ getTypeIcon(item.type) }}</span>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                      <p class="font-medium text-gray-900 dark:text-white truncate">{{ item.name }}</p>
                      <span :class="getTypeColor(item.type)" class="px-2 py-1 text-xs font-medium rounded">
                        {{ item.type }}
                      </span>
                    </div>
                    <p v-if="item.description" class="text-xs text-gray-600 dark:text-gray-400 truncate">
                      {{ item.description }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                      <span v-if="item.category">📁 {{ item.category }}</span>
                      <span v-if="item.price" class="text-green-600 dark:text-green-400 font-medium">
                        {{ formatPrice(item.price) }}
                      </span>
                    </div>
                  </div>
                  <i class="fas fa-chevron-right text-gray-300 flex-shrink-0"></i>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <!-- Daily Quote -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl p-6 shadow-sm">
          <div class="flex items-start gap-4">
            <span class="text-3xl flex-shrink-0">💡</span>
            <div>
              <p class="font-semibold mb-1">Quote of the Day</p>
              <p class="text-indigo-100 italic">{{ dailyQuote }}</p>
            </div>
          </div>
        </div>

        <!-- Alert Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Expired Items -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-red-500 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-900 dark:text-white">Expired Items</h3>
              <span class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-full text-sm font-bold">
                {{ expiredItems.length }}
              </span>
            </div>
            <ul class="space-y-2 max-h-40 overflow-y-auto">
              <li
                v-for="(item, i) in expiredItems.slice(0, 3)"
                :key="i"
                class="text-xs text-gray-600 dark:text-gray-400 p-2 bg-gray-50 dark:bg-gray-700 rounded"
              >
                <div class="font-medium text-gray-900 dark:text-white">{{ item.stockable?.name }}</div>
                <div class="text-red-600 dark:text-red-400">{{ item.expiration_date }}</div>
              </li>
            </ul>
          </div>

          <!-- Expiring Soon -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-yellow-500 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-900 dark:text-white">Expiring Soon</h3>
              <span class="px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded-full text-sm font-bold">
                {{ expiringItems.length }}
              </span>
            </div>
            <ul class="space-y-2 max-h-40 overflow-y-auto">
              <li
                v-for="(item, i) in expiringItems.slice(0, 3)"
                :key="i"
                class="text-xs text-gray-600 dark:text-gray-400 p-2 bg-gray-50 dark:bg-gray-700 rounded"
              >
                <div class="font-medium text-gray-900 dark:text-white">{{ item.stockable?.name }}</div>
                <div class="text-yellow-600 dark:text-yellow-400">{{ item.expiration_date }}</div>
              </li>
            </ul>
          </div>

          <!-- Low Stock -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-orange-500 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-900 dark:text-white">Low Stock</h3>
              <span class="px-3 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 rounded-full text-sm font-bold">
                {{ lowStockItems.length }}
              </span>
            </div>
            <ul class="space-y-2 max-h-40 overflow-y-auto">
              <li
                v-for="(item, i) in lowStockItems.slice(0, 3)"
                :key="i"
                class="text-xs text-gray-600 dark:text-gray-400 p-2 bg-gray-50 dark:bg-gray-700 rounded"
              >
                <div class="font-medium text-gray-900 dark:text-white">{{ item.name }}</div>
                <div class="text-orange-600 dark:text-orange-400">{{ item.quantity }}/{{ item.minimum }}</div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">System Health</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ metrics.systemHealth }}%</p>
              </div>
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center text-xl">
                ✓
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Active Users</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ metrics.activeUsers }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center text-xl">
                👥
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Revenue</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ currency(metrics.revenue) }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center text-xl">
                💰
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Orders</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ metrics.orders }}</p>
              </div>
              <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900 text-pink-600 dark:text-pink-400 rounded-lg flex items-center justify-center text-xl">
                📦
              </div>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sales Analytics</h2>
            <canvas id="salesChart" height="80"></canvas>
          </div>
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Products</h2>
            <canvas id="productsChart" height="80"></canvas>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
          <div class="space-y-3">
            <div
              v-for="(activity, i) in activities.slice(0, 5)"
              :key="i"
              class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition"
            >
              <div class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0"></div>
              <p class="text-sm text-gray-700 dark:text-gray-300">{{ activity }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.search-container ::-webkit-scrollbar {
  width: 6px;
}

.search-container ::-webkit-scrollbar-track {
  background: transparent;
}

.search-container ::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.search-container ::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
