<script setup>
import { ref, onMounted } from "vue";
import Chart from "chart.js/auto";
import AppLayout from "@/layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";

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
      },
      scales: {
        x: { ticks: { color: "#374151" } },
        y: { ticks: { color: "#374151" } },
      },
    },
  });
});
</script>

<template>
  <AppLayout>
    <Head title="Admin Dashboard" />
    <div class="relative z-10 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white rounded-3xl p-6 mb-6 shadow-lg"
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

            <!-- Search + Time -->
            <div class="flex items-center space-x-4">
              <div class="relative">
                <input
                  type="text"
                  placeholder="Quick search..."
                  class="bg-white/30 text-white placeholder-white/70 rounded-2xl px-6 py-3 w-64 backdrop-blur-lg border border-white/40 focus:outline-none focus:ring-2 focus:ring-white/60 transition-all"
                />
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
          class="card p-6 mb-8 glow-on-hover bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg"
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
            class="card p-6 border-l-4 border-red-600 bg-gradient-to-br from-red-500 to-red-700 text-white shadow-lg"
          >
            <h3 class="text-lg font-bold mb-2">
              Expired Items ({{ expiredItems.length }})
            </h3>
            <ul class="space-y-1 max-h-40 overflow-y-auto">
              <li v-for="(item, i) in expiredItems" :key="i" class="text-sm">
                {{ item.stockable.name }} – {{ item.batch }} (Expired on: {{ item.expiration_date }})
              </li>
            </ul>
          </div>

          <div
            class="card p-6 border-l-4 border-yellow-500 bg-gradient-to-br from-yellow-400 to-amber-500 text-white shadow-lg"
          >
            <h3 class="text-lg font-bold mb-2">
              Expiring Soon ({{ expiringItems.length }})
            </h3>
            <ul class="space-y-1 max-h-40 overflow-y-auto">
              <li v-for="(item, i) in expiringItems" :key="i" class="text-sm">
                {{ item.stockable.name }} – {{ item.batch }} ({{ item.expiration_date }})
              </li>
            </ul>
          </div>

          <div
            class="card p-6 border-l-4 border-orange-600 bg-gradient-to-br from-orange-500 to-pink-500 text-white shadow-lg"
          >
            <h3 class="text-lg font-bold mb-2">
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
            class="metric-card p-6 rounded-3xl text-center bg-gradient-to-br from-green-400 to-green-600 text-white shadow-md"
          >
            <div class="text-2xl font-bold">{{ metrics.systemHealth }}%</div>
            <p>System Health</p>
          </div>
          <div
            class="metric-card p-6 rounded-3xl text-center bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-md"
          >
            <div class="text-2xl font-bold">{{ metrics.activeUsers }}</div>
            <p>Active Users</p>
          </div>
          <div
            class="metric-card p-6 rounded-3xl text-center bg-gradient-to-br from-purple-400 to-purple-600 text-white shadow-md"
          >
            <div class="text-2xl font-bold">{{ metrics.revenue }}</div>
            <p>Revenue</p>
          </div>
          <div
            class="metric-card p-6 rounded-3xl text-center bg-gradient-to-br from-pink-400 to-pink-600 text-white shadow-md"
          >
            <div class="text-2xl font-bold">{{ metrics.orders }}</div>
            <p>Orders</p>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <div class="card p-6 shadow-lg">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
              Sales Analytics
            </h2>
            <canvas id="salesChart"></canvas>
          </div>
          <div class="card p-6 shadow-lg">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
              Top Products
            </h2>
            <canvas id="productsChart"></canvas>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="card p-6 shadow-lg bg-gradient-to-br from-gray-50 to-gray-100">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">
            Recent Activity
          </h2>
          <div class="space-y-2">
            <div
              v-for="(a, i) in activities"
              :key="i"
              class="activity-item text-sm text-gray-700"
            >
              {{ a }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
