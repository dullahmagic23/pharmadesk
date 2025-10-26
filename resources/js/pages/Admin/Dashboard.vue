<script setup lang="ts">
import { ref, onMounted, computed, watch, onUnmounted } from 'vue'
import Chart from 'chart.js/auto'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios'
import currency from '@/modules/currecyFormatter'
import { Search, X, ChevronRight, Clock, TrendingUp, AlertCircle } from 'lucide-vue-next'

interface SearchResult {
  id: string | number
  name: string
  type: 'Medicine' | 'Product' | 'Customer'
  description?: string
  category?: string
  price?: number
  url: string
  relevance_score?: number
}

interface AlertItem {
  id: string | number
  name?: string
  stockable?: { name: string }
  batch?: string
  quantity?: number
  minimum?: number
  expiration_date: string
}

const props = defineProps<{
  userName: string
  userRole: string
  dailyQuote: string
  expiredItems: AlertItem[]
  expiringItems: AlertItem[]
  lowStockItems: AlertItem[]
  metrics: {
    systemHealth: number
    activeUsers: number
    revenue: number
    orders: number
  }
  salesChartData: object
  productsChartData: object
  activities: string[]
}>()

// State
const currentTime = ref<string>('')
const query = ref<string>('')
const results = ref<SearchResult[]>([])
const suggestions = ref<string[]>([])
const showResults = ref<boolean>(false)
const showSuggestions = ref<boolean>(false)
const loading = ref<boolean>(false)
const searchType = ref<string>('all')
const sortBy = ref<string>('relevance')
const selectedResultIndex = ref<number>(-1)
const searchStats = ref({
  totalResults: 0,
  searchTime: 0,
  lastQuery: ''
})

let searchTimeout: NodeJS.Timeout | null = null
let suggestionsTimeout: NodeJS.Timeout | null = null
let clockInterval: NodeJS.Timeout | null = null
let chartInstances: { [key: string]: Chart } = {}

// Lifecycle
onMounted(() => {
  initializeClock()
  initializeCharts()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  if (clockInterval) clearInterval(clockInterval)
  if (searchTimeout) clearTimeout(searchTimeout)
  if (suggestionsTimeout) clearTimeout(suggestionsTimeout)
  document.removeEventListener('click', handleClickOutside)
  Object.values(chartInstances).forEach(chart => chart?.destroy?.())
})

// Computed
const hasResults = computed(() => results.value.length > 0)
const hasSuggestions = computed(() => suggestions.value.length > 0)
const isValidQuery = computed(() => query.value.length >= 2)

const alertStats = computed(() => ({
  expired: props.expiredItems.length,
  expiring: props.expiringItems.length,
  lowStock: props.lowStockItems.length,
  total: props.expiredItems.length + props.expiringItems.length + props.lowStockItems.length
}))

// Methods
const initializeClock = (): void => {
  updateTime()
  clockInterval = setInterval(updateTime, 1000)
}

const updateTime = (): void => {
  currentTime.value = new Date().toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const initializeCharts = (): void => {
  const baseConfig = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        labels: {
          color: '#6b7280',
          font: { size: 12, weight: '500' as const },
          usePointStyle: true,
          padding: 20
        }
      },
      tooltip: {
        backgroundColor: 'rgba(31, 41, 55, 0.95)',
        titleColor: '#fff',
        bodyColor: '#fff',
        padding: 12,
        borderRadius: 8,
        displayColors: true,
        boxPadding: 6
      }
    }
  }

  const salesCtx = document.getElementById('salesChart') as HTMLCanvasElement
  if (salesCtx && !chartInstances['sales']) {
    chartInstances['sales'] = new Chart(salesCtx, {
      type: 'line',
      data: props.salesChartData,
      options: {
        ...baseConfig,
        elements: {
          line: { tension: 0.4, borderWidth: 2.5 },
          point: { radius: 4, backgroundColor: '#6366f1', borderWidth: 0 }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
          x: { grid: { display: false } }
        }
      }
    })
  }

  const productsCtx = document.getElementById('productsChart') as HTMLCanvasElement
  if (productsCtx && !chartInstances['products']) {
    chartInstances['products'] = new Chart(productsCtx, {
      type: 'bar',
      data: props.productsChartData,
      options: {
        ...baseConfig,
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true, grid: { color: '#f3f4f6' } }
        }
      }
    })
  }
}

const onSearch = (): void => {
  if (searchTimeout) clearTimeout(searchTimeout)
  if (suggestionsTimeout) clearTimeout(suggestionsTimeout)

  if (!query.value) {
    results.value = []
    suggestions.value = []
    showResults.value = false
    showSuggestions.value = false
    return
  }

  if (query.value.length < 2) {
    if (query.value.length >= 1) {
      suggestionsTimeout = setTimeout(getSuggestions, 200)
    }
    return
  }

  loading.value = true
  searchTimeout = setTimeout(performSearch, 300)
}

const getSuggestions = async (): Promise<void> => {
  try {
    const { data } = await axios.get('/search/suggestions', {
      params: { q: query.value }
    })
    suggestions.value = data || []
    showSuggestions.value = data.length > 0
    showResults.value = false
  } catch (error) {
    console.error('Suggestions error:', error)
    suggestions.value = []
  }
}

const performSearch = async (): Promise<void> => {
  const startTime = Date.now()
  try {
    const { data } = await axios.get('/search/advanced', {
      params: {
        q: query.value,
        type: searchType.value,
        sort: sortBy.value,
        limit: 10
      }
    })

    results.value = data.data || data || []
    showResults.value = results.value.length > 0
    showSuggestions.value = false
    selectedResultIndex.value = -1

    searchStats.value = {
      totalResults: data.meta?.total_results || results.value.length,
      searchTime: Date.now() - startTime,
      lastQuery: query.value
    }
  } catch (error) {
    console.error('Search error:', error)
    results.value = []
    showResults.value = false
  } finally {
    loading.value = false
  }
}

const handleKeyDown = (event: KeyboardEvent): void => {
  if (!showResults.value || results.value.length === 0) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedResultIndex.value = Math.min(
        selectedResultIndex.value + 1,
        results.value.length - 1
      )
      break
    case 'ArrowUp':
      event.preventDefault()
      selectedResultIndex.value = Math.max(selectedResultIndex.value - 1, -1)
      break
    case 'Enter':
      event.preventDefault()
      if (selectedResultIndex.value >= 0) {
        goTo(results.value[selectedResultIndex.value])
      }
      break
    case 'Escape':
      clearSearch()
      break
  }
}

const handleClickOutside = (event: MouseEvent): void => {
  const searchContainer = document.querySelector('.search-container')
  if (searchContainer && !searchContainer.contains(event.target as Node)) {
    showResults.value = false
    showSuggestions.value = false
  }
}

const useSuggestion = (suggestion: string): void => {
  query.value = suggestion
  showSuggestions.value = false
  performSearch()
}

const clearSearch = (): void => {
  query.value = ''
  results.value = []
  suggestions.value = []
  showResults.value = false
  showSuggestions.value = false
  selectedResultIndex.value = -1
}

const goTo = (item: SearchResult): void => {
  clearSearch()
  router.visit(item.url)
}

const getTypeIcon = (type: string): string => {
  const icons: { [key: string]: string } = {
    Medicine: '💊',
    Product: '📦',
    Customer: '👤'
  }
  return icons[type] || '🔍'
}

const getTypeColor = (type: string): string => {
  const colors: { [key: string]: string } = {
    Medicine: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    Product: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    Customer: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'
  }
  return colors[type] || 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400'
}

const getAlertColor = (type: 'expired' | 'expiring' | 'lowStock'): string => {
  const colors = {
    expired: 'border-red-500 bg-red-50 dark:bg-red-900/20',
    expiring: 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
    lowStock: 'border-orange-500 bg-orange-50 dark:bg-orange-900/20'
  }
  return colors[type]
}

const getAlertBadgeColor = (type: 'expired' | 'expiring' | 'lowStock'): string => {
  const colors = {
    expired: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
    expiring: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300',
    lowStock: 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300'
  }
  return colors[type]
}

const formatPrice = (price?: number): string => {
  return price ? `$${parseFloat(String(price)).toFixed(2)}` : '-'
}

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Admin', href: '/admin/dashboard' },
]
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Admin Dashboard" />
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <!-- Header -->
      <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
        <div class="w-full mx-auto px-4 md:px-6 py-6">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex-1">
              <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                Welcome back, {{ userName }}
              </h1>
              <div class="flex items-center gap-3 mt-2">
                <span class="text-gray-600 dark:text-gray-400 text-sm">{{ userRole }}</span>
                <span class="flex items-center gap-1 text-gray-500 dark:text-gray-500 text-sm">
                  <Clock class="w-4 h-4" />
                  {{ currentTime }}
                </span>
              </div>
            </div>
            <div class="px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-lg text-sm font-semibold whitespace-nowrap">
              {{ userRole }}
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="w-full mx-auto px-4 md:px-6 py-8 space-y-8">
        <!-- Search Bar -->
        <div class="search-container">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6 space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1 relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 flex-shrink-0" />
                <input
                  v-model="query"
                  @input="onSearch"
                  @keydown="handleKeyDown"
                  type="text"
                  placeholder="Search medicines, products, customers..."
                  class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                />
                <div v-if="query" class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
                  <button
                    v-if="loading"
                    class="text-indigo-500"
                    aria-label="Searching"
                  >
                    <div class="animate-spin h-5 w-5 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                  </button>
                  <button
                    v-else
                    @click="clearSearch"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                    aria-label="Clear search"
                  >
                    <X class="w-5 h-5" />
                  </button>
                </div>
              </div>

              <select
                v-model="searchType"
                @change="performSearch"
                class="px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
              >
                <option value="all">All Types</option>
                <option value="medicine">Medicines</option>
                <option value="product">Products</option>
                <option value="customer">Customers</option>
              </select>
            </div>

            <!-- Search Results -->
            <transition name="fade">
              <div
                v-if="showResults && hasResults"
                class="mt-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden max-h-96 overflow-y-auto"
              >
                <div class="sticky top-0 p-3 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-400">
                  {{ searchStats.totalResults }} results ({{ searchStats.searchTime }}ms)
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                  <button
                    v-for="(item, index) in results"
                    :key="`${item.id}-${index}`"
                    @click="goTo(item)"
                    :class="[
                      'w-full p-4 hover:bg-indigo-50 dark:hover:bg-gray-600/50 transition-colors text-left',
                      selectedResultIndex === index ? 'bg-indigo-100 dark:bg-gray-600' : ''
                    ]"
                  >
                    <div class="flex items-start gap-3">
                      <span class="text-lg flex-shrink-0">{{ getTypeIcon(item.type) }}</span>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                          <p class="font-medium text-gray-900 dark:text-white truncate">{{ item.name }}</p>
                          <span :class="['px-2 py-1 text-xs font-semibold rounded', getTypeColor(item.type)]">
                            {{ item.type }}
                          </span>
                        </div>
                        <p v-if="item.description" class="text-xs text-gray-600 dark:text-gray-400 truncate">
                          {{ item.description }}
                        </p>
                        <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                          <span v-if="item.category">📁 {{ item.category }}</span>
                          <span v-if="item.price" class="text-green-600 dark:text-green-400 font-medium">
                            {{ formatPrice(item.price) }}
                          </span>
                        </div>
                      </div>
                      <ChevronRight class="w-5 h-5 text-gray-300 flex-shrink-0" />
                    </div>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Quote Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 text-white rounded-lg shadow-sm p-6">
          <div class="flex items-start gap-4">
            <span class="text-3xl flex-shrink-0">💡</span>
            <div class="flex-1">
              <p class="font-semibold mb-1">Quote of the Day</p>
              <p class="text-indigo-100 italic text-sm leading-relaxed">{{ dailyQuote }}</p>
            </div>
          </div>
        </div>

        <!-- Alerts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="(alertType, idx) in ['expired', 'expiring', 'lowStock']"
            :key="idx"
            :class="['bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 p-6', getAlertColor(alertType as any)]"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-900 dark:text-white">
                {{ alertType === 'expired' ? 'Expired Items' : alertType === 'expiring' ? 'Expiring Soon' : 'Low Stock' }}
              </h3>
              <span :class="['px-3 py-1 text-xs font-bold rounded-full', getAlertBadgeColor(alertType as any)]">
                {{ alertStats[alertType as any] }}
              </span>
            </div>
            <ul class="space-y-2 max-h-40 overflow-y-auto">
              <li
                v-for="(item, i) in (alertType === 'expired' ? props.expiredItems : alertType === 'expiring' ? props.expiringItems : props.lowStockItems).slice(0, 3)"
                :key="i"
                class="text-xs p-2.5 bg-white/50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-600"
              >
                <div class="font-medium text-gray-900 dark:text-white">
                  {{ item.stockable?.name || item.name || '-' }}
                </div>
                <div v-if="alertType === 'lowStock'" class="text-gray-600 dark:text-gray-400 mt-1">
                  Stock: {{ item.quantity }}/{{ item.minimum }}
                </div>
                <div v-else class="text-gray-600 dark:text-gray-400 mt-1">
                  {{ item.expiration_date }}
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">System Health</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-3">{{ metrics.systemHealth }}%</p>
              </div>
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center">
                ✓
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Active Users</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-3">{{ metrics.activeUsers }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center">
                👥
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Revenue</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-3">{{ currency(metrics.revenue) }}</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center">
                💰
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-start justify-between">
              <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Orders</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-3">{{ metrics.orders }}</p>
              </div>
              <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 rounded-lg flex items-center justify-center">
                📦
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
              <TrendingUp class="w-5 h-5 text-indigo-600" />
              Sales Analytics
            </h2>
            <canvas id="salesChart" height="80"></canvas>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
              <TrendingUp class="w-5 h-5 text-indigo-600" />
              Top Products
            </h2>
            <canvas id="productsChart" height="80"></canvas>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <AlertCircle class="w-5 h-5 text-indigo-600" />
            Recent Activity
          </h2>
          <div class="space-y-3">
            <div
              v-for="(activity, i) in props.activities.slice(0, 5)"
              :key="i"
              class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
            >
              <div class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0 mt-2"></div>
              <p class="text-sm text-gray-700 dark:text-gray-300">{{ activity }}</p>
            </div>
          </div>
        </div>
      </main>
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

.dark .search-container ::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .search-container ::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
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
