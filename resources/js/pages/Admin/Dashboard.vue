<script setup>
import { ref, onMounted, computed } from "vue";
import Chart from "chart.js/auto";
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import axios from "axios";

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
let searchTimeout = null;
let suggestionsTimeout = null;

// Search statistics
const searchStats = ref({
  totalResults: 0,
  searchTime: 0,
  lastQuery: ""
});

onMounted(() => {
  setInterval(() => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString();
  }, 1000);

  // Sales chart
  new Chart(document.getElementById("salesChart"), {
    type: "line",
    data: props.salesChartData,
    options: {
      responsive: true,
      plugins: {
        legend: { labels: { color: "#1f2937" } },
        tooltip: {
          backgroundColor: "#1f2937",
          titleColor: "#fff",
          bodyColor: "#fff",
        },
      },
      elements: {
        line: { tension: 0.4 },
        point: { radius: 5, backgroundColor: "#6366f1" },
      },
    },
  });

  // Products chart
  new Chart(document.getElementById("productsChart"), {
    type: "bar",
    data: props.productsChartData,
    options: {
      responsive: true,
      plugins: {
        legend: { labels: { color: "#1f2937" } },
        tooltip: {
          backgroundColor: "#111827",
          titleColor: "#fff",
          bodyColor: "#fff",
        },
      },
      scales: {
        x: { ticks: { color: "#374151" } },
        y: { ticks: { color: "#374151" } },
      },
    },
  });

  // Add click outside listener to close search results
  document.addEventListener('click', handleClickOutside);
});

// Computed properties for better UX
const hasResults = computed(() => results.value.length > 0);
const hasSuggestions = computed(() => suggestions.value.length > 0);
const isValidQuery = computed(() => query.value.length >= 2);

/** Enhanced debounced search with advanced features */
const onSearch = () => {
  clearTimeout(searchTimeout);
  clearTimeout(suggestionsTimeout);

  if (!query.value) {
    results.value = [];
    suggestions.value = [];
    showResults.value = false;
    showSuggestions.value = false;
    selectedResultIndex.value = -1;
    return;
  }

  // Show suggestions for short queries
  if (query.value.length >= 1 && query.value.length < 2) {
    suggestionsTimeout = setTimeout(getSuggestions, 200);
    return;
  }

  // Perform full search for longer queries
  if (query.value.length >= 2) {
    loading.value = true;
    searchTimeout = setTimeout(performSearch, 300);
  }
};

/** Get search suggestions */
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
    showSuggestions.value = false;
  }
};

/** Perform advanced search */
const performSearch = async () => {
  const startTime = Date.now();

  try {
    const { data } = await axios.get("/search/advanced", {
      params: {
        q: query.value,
        type: searchType.value,
        sort: sortBy.value,
        limit: 10
      },
    });

    const searchTime = Date.now() - startTime;

    results.value = data.data || data; // Handle both response formats
    showResults.value = results.value.length > 0;
    showSuggestions.value = false;
    selectedResultIndex.value = -1;

    // Update search statistics
    searchStats.value = {
      totalResults: data.meta?.total_results || results.value.length,
      searchTime: searchTime,
      lastQuery: query.value
    };

  } catch (error) {
    console.error("Search error:", error);
    results.value = [];
    showResults.value = false;

    // Show user-friendly error message
    showErrorNotification("Search failed. Please try again.");
  } finally {
    loading.value = false;
  }
};

/** Handle keyboard navigation */
const handleKeyDown = (event) => {
  if (!showResults.value || results.value.length === 0) return;

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault();
      selectedResultIndex.value = Math.min(
        selectedResultIndex.value + 1,
        results.value.length - 1
      );
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

/** Handle clicking outside search results */
const handleClickOutside = (event) => {
  const searchContainer = document.querySelector('.search-container');
  if (searchContainer && !searchContainer.contains(event.target)) {
    showResults.value = false;
    showSuggestions.value = false;
    selectedResultIndex.value = -1;
  }
};

/** Use suggestion */
const useSuggestion = (suggestion) => {
  query.value = suggestion;
  showSuggestions.value = false;
  performSearch();
};

/** Clear search */
const clearSearch = () => {
  query.value = "";
  results.value = [];
  suggestions.value = [];
  showResults.value = false;
  showSuggestions.value = false;
  selectedResultIndex.value = -1;
  searchStats.value = { totalResults: 0, searchTime: 0, lastQuery: "" };
};

/** Navigate to selected item */
const goTo = (item) => {
  showResults.value = false;
  showSuggestions.value = false;
  query.value = "";
  selectedResultIndex.value = -1;
  router.visit(item.url);
};

/** Get icon for search result type */
const getTypeIcon = (type) => {
  const icons = {
    'Medicine': 'fas fa-pills',
    'Product': 'fas fa-box',
    'Customer': 'fas fa-user'
  };
  return icons[type] || 'fas fa-search';
};

/** Get type color for search results */
const getTypeColor = (type) => {
  const colors = {
    'Medicine': 'bg-green-100 text-green-800',
    'Product': 'bg-blue-100 text-blue-800',
    'Customer': 'bg-purple-100 text-purple-800'
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};

/** Show error notification */
const showErrorNotification = (message) => {
  // You can implement your preferred notification system here
  console.error(message);
};

/** Format price for display */
const formatPrice = (price) => {
  return price ? `$${parseFloat(price).toFixed(2)}` : '';
};
</script>

<template>
  <AppLayout>
    <Head title="Admin Dashboard" />
    <div class="relative p-2">
      <div class="w-full mx-auto">
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white rounded-3xl p-6 mb-6 shadow-xl"
        >
          <div
            class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0"
          >
            <div class="flex items-center space-x-4">
              <div
                class="bg-white/20 w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-md"
              >
                AD
              </div>
              <div>
                <h1 class="text-3xl font-bold mb-1">
                  Welcome back, {{ userName }}
                </h1>
                <p class="text-blue-100 text-lg">
                  {{ userRole }} Dashboard
                </p>
              </div>
            </div>

            <!-- Enhanced Search + Time -->
            <div class="flex items-center space-x-4 relative">
              <div class="relative w-80 search-container">
                <!-- Search Input with Filters -->
                <div class="relative">
                  <input
                    type="text"
                    v-model="query"
                    @input="onSearch"
                    @keydown="handleKeyDown"
                    placeholder="Search medicines, products, customers..."
                    class="bg-white/30 text-white placeholder-white/70 rounded-2xl px-6 py-3 w-full backdrop-blur-lg border border-white/40 focus:outline-none focus:ring-2 focus:ring-white/60 transition-all pr-12"
                  />
                  <!-- Loading Spinner -->
                  <div v-if="loading" class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
                  </div>
                  <!-- Clear Button -->
                  <button
                    v-else-if="query"
                    @click="clearSearch"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white"
                  >
                    <i class="fas fa-times"></i>
                  </button>
                </div>

                <!-- Search Filters -->
                <div v-if="isValidQuery" class="flex space-x-2 mt-2">
                  <select
                    v-model="searchType"
                    @change="performSearch"
                    class="bg-white/20 text-white text-sm rounded-lg px-3 py-1 border border-white/30 focus:outline-none focus:ring-1 focus:ring-white/50"
                  >
                    <option value="all">All Types</option>
                    <option value="medicine">Medicines</option>
                    <option value="product">Products</option>
                    <option value="customer">Customers</option>
                  </select>
                  <select
                    v-model="sortBy"
                    @change="performSearch"
                    class="bg-white/20 text-white text-sm rounded-lg px-3 py-1 border border-white/30 focus:outline-none focus:ring-1 focus:ring-white/50"
                  >
                    <option value="relevance">Relevance</option>
                    <option value="name">Name</option>
                    <option value="created_at">Recently Added</option>
                  </select>
                </div>

                <!-- Suggestions Dropdown -->
                <ul
                  v-if="showSuggestions && hasSuggestions"
                  class="absolute z-50 mt-2 w-full bg-white shadow-lg rounded-2xl border divide-y divide-gray-100 max-h-40 overflow-y-auto"
                >
                  <li class="p-2 bg-gray-50 text-xs text-gray-600 font-medium">
                    Suggestions
                  </li>
                  <li
                    v-for="(suggestion, index) in suggestions"
                    :key="index"
                    @click="useSuggestion(suggestion)"
                    class="p-3 hover:bg-blue-50 cursor-pointer text-gray-700 text-sm"
                  >
                    <i class="fas fa-search text-gray-400 mr-2"></i>
                    {{ suggestion }}
                  </li>
                </ul>

                <!-- Search Results Dropdown -->
                <div
                  v-if="showResults && hasResults"
                  class="absolute z-50 mt-2 w-full bg-white shadow-xl rounded-2xl border overflow-hidden max-h-96 overflow-y-auto"
                >
                  <!-- Results Header -->
                  <div class="p-3 bg-gray-50 border-b flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                      <span class="font-medium">{{ searchStats.totalResults }}</span> results
                      <span v-if="searchStats.searchTime > 0" class="text-gray-400">
                        ({{ searchStats.searchTime }}ms)
                      </span>
                    </div>
                    <button
                      @click="clearSearch"
                      class="text-gray-400 hover:text-gray-600 text-xs"
                    >
                      Clear
                    </button>
                  </div>

                  <!-- Results List -->
                  <div class="divide-y divide-gray-100">
                    <div
                      v-for="(item, index) in results"
                      :key="index"
                      @click="goTo(item)"
                      :class="[
                        'p-4 hover:bg-blue-50 cursor-pointer transition-colors',
                        selectedResultIndex === index ? 'bg-blue-100' : ''
                      ]"
                    >
                      <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                          <div class="flex items-center space-x-2 mb-1">
                            <i :class="getTypeIcon(item.type)" class="text-gray-500 text-sm"></i>
                            <p class="text-sm font-medium text-gray-900 truncate">
                              {{ item.name }}
                            </p>
                            <span :class="getTypeColor(item.type)" class="px-2 py-1 text-xs font-medium rounded-full">
                              {{ item.type }}
                            </span>
                          </div>

                          <!-- Additional Info Based on Type -->
                          <div class="text-xs text-gray-500 space-y-1">
                            <p v-if="item.description" class="truncate">
                              {{ item.description }}
                            </p>
                            <div class="flex flex-wrap gap-3">
                              <span v-if="item.manufacturer">
                                <i class="fas fa-industry mr-1"></i>{{ item.manufacturer }}
                              </span>
                              <span v-if="item.category">
                                <i class="fas fa-tag mr-1"></i>{{ item.category }}
                              </span>
                              <span v-if="item.email">
                                <i class="fas fa-envelope mr-1"></i>{{ item.email }}
                              </span>
                              <span v-if="item.phone">
                                <i class="fas fa-phone mr-1"></i>{{ item.phone }}
                              </span>
                              <span v-if="item.price" class="text-green-600 font-medium">
                                {{ formatPrice(item.price) }}
                              </span>
                            </div>
                          </div>
                        </div>

                        <!-- Relevance Score (for debugging) -->
                        <div v-if="item.relevance_score && sortBy === 'relevance'"
                             class="text-xs text-gray-400 ml-2">
                          {{ Math.round(item.relevance_score * 100) }}%
                        </div>

                        <i class="fas fa-chevron-right text-gray-400 ml-2"></i>
                      </div>
                    </div>
                  </div>

                  <!-- No Results Message -->
                  <div v-if="!loading && !hasResults && query.length >= 2"
                       class="p-6 text-center text-gray-500">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p class="text-sm">No results found for "{{ query }}"</p>
                    <p class="text-xs text-gray-400 mt-1">Try different keywords or check spelling</p>
                  </div>
                </div>
              </div>

              <div
                class="bg-white/30 rounded-2xl px-6 py-3 backdrop-blur-lg border border-white/40"
              >
                <div class="time-display text-lg font-semibold">
                  {{ currentTime }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quote -->
        <div
          class="p-6 mb-8 bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg rounded-2xl"
        >
          <div class="flex items-center space-x-4">
            <div
              class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center shadow-lg text-xl"
            >
              💡
            </div>
            <div>
              <h3 class="text-lg font-semibold mb-1">Quote of the Day</h3>
              <p class="italic">{{ dailyQuote }}</p>
            </div>
          </div>
        </div>

        <!-- Alerts -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div
            class="p-6 border-l-4 border-red-600 bg-gradient-to-br from-red-500 to-red-700 text-white shadow-lg rounded-2xl"
          >
            <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
              <i class="fas fa-exclamation-circle"></i>
              Expired Items ({{ expiredItems.length }})
            </h3>
            <ul class="space-y-1 max-h-40 overflow-y-auto">
              <li v-for="(item, i) in expiredItems" :key="i" class="text-sm">
                {{ item.stockable.name }} – {{ item.batch }} (Expired:
                {{ item.expiration_date }})
              </li>
            </ul>
          </div>

          <div
            class="p-6 border-l-4 border-yellow-500 bg-gradient-to-br from-yellow-400 to-amber-500 text-white shadow-lg rounded-2xl"
          >
            <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
              <i class="fas fa-clock"></i>
              Expiring Soon ({{ expiringItems.length }})
            </h3>
            <ul class="space-y-1 max-h-40 overflow-y-auto">
              <li v-for="(item, i) in expiringItems" :key="i" class="text-sm">
                {{ item.stockable.name }} – {{ item.batch }} ({{ item.expiration_date }})
              </li>
            </ul>
          </div>

          <div
            class="p-6 border-l-4 border-orange-600 bg-gradient-to-br from-orange-500 to-pink-500 text-white shadow-lg rounded-2xl"
          >
            <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
              <i class="fas fa-box"></i>
              Low Stock ({{ lowStockItems.length }})
            </h3>
            <ul class="space-y-1 max-h-40 overflow-y-auto">
              <li v-for="(item, i) in lowStockItems" :key="i" class="text-sm">
                {{ item.name }} – {{ item.batch }} (Qty: {{ item.quantity }}/{{
                  item.minimum
                }})
              </li>
            </ul>
          </div>
        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div
            class="p-6 rounded-3xl text-center bg-gradient-to-br from-green-400 to-green-600 text-white shadow-md"
          >
            <div class="text-3xl font-bold animate-pulse">
              {{ metrics.systemHealth }}%
            </div>
            <p>System Health</p>
          </div>
          <div
            class="p-6 rounded-3xl text-center bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-md"
          >
            <div class="text-3xl font-bold animate-bounce">
              {{ metrics.activeUsers }}
            </div>
            <p>Active Users</p>
          </div>
          <div
            class="p-6 rounded-3xl text-center bg-gradient-to-br from-purple-400 to-purple-600 text-white shadow-md"
          >
            <div class="text-3xl font-bold">
              {{ metrics.revenue }}
            </div>
            <p>Revenue</p>
          </div>
          <div
            class="p-6 rounded-3xl text-center bg-gradient-to-br from-pink-400 to-pink-600 text-white shadow-md"
          >
            <div class="text-3xl font-bold">
              {{ metrics.orders }}
            </div>
            <p>Orders</p>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <div class="p-6 shadow-lg bg-white rounded-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
              Sales Analytics
            </h2>
            <canvas id="salesChart"></canvas>
          </div>
          <div class="p-6 shadow-lg bg-white rounded-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
              Top Products
            </h2>
            <canvas id="productsChart"></canvas>
          </div>
        </div>

        <!-- Recent Activity -->
        <div
          class="p-6 shadow-lg bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl"
        >
          <h2 class="text-2xl font-bold text-gray-800 mb-6">
            Recent Activity
          </h2>
          <div class="space-y-4">
            <div
              v-for="(a, i) in activities"
              :key="i"
              class="flex items-start gap-3"
            >
              <div
                class="w-3 h-3 bg-blue-500 rounded-full mt-1 animate-pulse"
              ></div>
              <p class="text-sm text-gray-700">{{ a }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom scrollbar for search results */
.search-container ::-webkit-scrollbar {
  width: 6px;
}

.search-container ::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.search-container ::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.search-container ::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Smooth transitions */
.search-container ul,
.search-container div {
  transition: all 0.2s ease-in-out;
}
</style>
