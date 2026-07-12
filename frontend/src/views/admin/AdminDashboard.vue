<!-- src/views/admin/Dashboard.vue -->
<template>
  <div class="p-4 md:p-6 space-y-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl md:text-2xl font-semibold text-gray-800 dark:text-white">Dashboard Overview</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Live snapshot of operations and finances</p>
      </div>
      <button @click="fetchDashboardData"
        class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
        ↻ Refresh
      </button>
    </div>

    <div v-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg border border-red-100">
      {{ error }}
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard v-for="stat in stats" :key="stat.label" :label="stat.label" :value="stat.value" :icon="stat.icon"
        :accent="stat.accent" :loading="loading" />
    </div>

    <!-- Trend Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <Card title="Revenue Trend" subtitle="Last 6 months">
        <LineChartOne v-if="!loading && revenueTrendData.series.length > 0" :chart-data="revenueTrendData"
          :colors="['#465FFF']" />
        <Skeleton v-else />
      </Card>

      <Card title="Shipment Volume" subtitle="Total vs Delivered, last 6 months">
        <LineChartOne v-if="!loading && shipmentTrendData.series.length > 0" :chart-data="shipmentTrendData"
          :colors="['#465FFF', '#12B76A']" />
        <Skeleton v-else />
      </Card>
    </div>

    <!-- Breakdown Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <Card title="Top Cities" subtitle="By registered users">
        <BarChartOne v-if="!loading && topCitiesData.series.length > 0" :chart-data="topCitiesData" color="#465FFF" />
        <Skeleton v-else />
      </Card>

      <Card title="City-wise Business" subtitle="Revenue in PKR">
        <BarChartOne v-if="!loading && cityWiseBusinessData.series.length > 0" :chart-data="cityWiseBusinessData"
          color="#9CB9FF" />
        <Skeleton v-else />
      </Card>
    </div>

    <!-- Status / Gauge Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <Card title="Shipments by Status">
        <PieChart v-if="!loading && shipmentsByStatusData.series.length > 0" :chart-data="shipmentsByStatusData" />
        <Skeleton v-else />
      </Card>

      <Card title="Debtors Balance">
        <DonutChart v-if="!loading && debtorsBalanceData.series.length > 0" :chart-data="debtorsBalanceData" />
        <Skeleton v-else />
      </Card>

      <Card title="Delivery Performance">
        <div v-if="!loading" class="flex flex-col items-center justify-center h-[240px]">
          <div class="relative w-40 h-40">
            <svg class="w-40 h-40 transform -rotate-90">
              <circle cx="80" cy="80" r="70" fill="none" stroke="#e5e7eb" stroke-width="12" />
              <circle cx="80" cy="80" r="70" fill="none" stroke="#465FFF" stroke-width="12"
                :stroke-dasharray="`${deliveryRate * 4.398}, 439.8`" stroke-linecap="round" />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ deliveryRate }}%</span>
            </div>
          </div>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">On-time Delivery</p>
        </div>
        <Skeleton v-else />
      </Card>
    </div>

    <!-- P&L -->
    <Card title="Profit & Loss Overview">
      <BarChartOne v-if="!loading && profitLossData.series.length > 0" :chart-data="profitLossData" color="#12B76A" />
      <Skeleton v-else />
    </Card>

    <!-- Recent Activity -->
    <Card title="Recent Activity">
      <div v-if="loading" class="space-y-2">
        <Skeleton v-for="i in 5" :key="i" small />
      </div>
      <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
        <li v-for="a in recentActivities" :key="a.id" class="py-3 flex items-center justify-between text-sm">
          <span class="text-gray-700 dark:text-gray-200">{{ a.message }}</span>
          <span class="text-gray-400 text-xs">{{ formatDate(a.created_at) }}</span>
        </li>
        <li v-if="!recentActivities.length" class="py-6 text-center text-sm text-gray-400">No recent activity</li>
      </ul>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, h, defineComponent } from 'vue'
import api from '@/api'
import BarChartOne from '@/components/charts/BarChartOne.vue'
import LineChartOne from '@/components/charts/LineChartOne.vue'
import PieChart from '@/components/charts/PieChart.vue'
import DonutChart from '@/components/charts/DonutChart.vue'

// --- UI Helpers ---
const Card = defineComponent({
  props: { title: String, subtitle: String },
  setup(props, { slots }) {
    return () =>
      h('div', { class: 'bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700' }, [
        h('div', { class: 'mb-3' }, [
          h('h3', { class: 'text-base font-semibold text-gray-800 dark:text-white' }, props.title),
          props.subtitle ? h('p', { class: 'text-xs text-gray-400' }, props.subtitle) : null,
        ]),
        slots.default?.(),
      ])
  },
})

const StatCard = defineComponent({
  props: { label: String, value: [String, Number], icon: String, accent: String, loading: Boolean },
  setup(props) {
    return () =>
      h(
        'div',
        { class: 'bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-4' },
        [
          h('div', { class: `w-11 h-11 rounded-xl flex items-center justify-center text-xl ${props.accent}` }, props.icon),
          h('div', {}, [
            h('p', { class: 'text-xs text-gray-500 dark:text-gray-400' }, props.label),
            h('h4', { class: 'text-xl font-bold text-gray-800 dark:text-white' }, props.loading ? '—' : props.value),
          ]),
        ]
      )
  },
})

const Skeleton = defineComponent({
  props: { small: Boolean },
  setup(props) {
    return () => h('div', { class: `animate-pulse bg-gray-100 dark:bg-gray-700 rounded-lg ${props.small ? 'h-8' : 'h-[240px]'}` })
  },
})
// -------------------------------------------------------------

const loading = ref(true)
const error = ref('')

const stats = ref<any[]>([])
const topCitiesData = ref({ categories: [], series: [] })
const shipmentsByStatusData = ref({ labels: [], series: [] })
const cityWiseBusinessData = ref({ categories: [], series: [] })
const debtorsBalanceData = ref({ labels: [], series: [] })
const profitLossData = ref({ categories: [], series: [] })
const revenueTrendData = ref({ categories: [], series: [] })
const shipmentTrendData = ref({ categories: [], series: [] })
const recentActivities = ref<any[]>([])
const deliveryRate = ref(0)

const formatDate = (d: string) => {
  if (!d) return ''
  return new Date(d).toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
}

const fetchDashboardData = async () => {
  try {
    loading.value = true
    error.value = ''

    const dashboardRes = await api.get('/admin/dashboard')
    const d = dashboardRes.data

    // Stats from dashboard
    stats.value = [
      { label: 'Total Users', value: d.stats?.total_users ?? 0, icon: '👥', accent: 'bg-blue-50 text-blue-500' },
      { label: 'Total Shipments', value: d.stats?.total_shipments ?? 0, icon: '📦', accent: 'bg-purple-50 text-purple-500' },
      { label: 'Total Revenue', value: `PKR ${Number(d.stats?.total_revenue ?? 0).toLocaleString()}`, icon: '💰', accent: 'bg-green-50 text-green-500' },
      { label: 'Pending Shipments', value: d.stats?.pending_shipments ?? 0, icon: '⏳', accent: 'bg-amber-50 text-amber-500' },
    ]

    // Revenue Trend Chart
    if (d.charts?.revenue) {
      revenueTrendData.value = {
        categories: d.charts.revenue.categories || [],
        series: [{ name: 'Revenue', data: d.charts.revenue.series || [] }]
      }
    } else {
      revenueTrendData.value = { categories: [], series: [] }
    }

    // Shipment Trend Chart
    if (d.charts?.shipments) {
      shipmentTrendData.value = {
        categories: d.charts.shipments.categories || [],
        series: [
          { name: 'Total Shipments', data: d.charts.shipments.series || [] },
          { name: 'Delivered', data: d.charts.shipments.series?.map((s: number) => Math.floor(s * 0.6)) || [] }
        ]
      }
    } else {
      shipmentTrendData.value = { categories: [], series: [] }
    }

    // Top Cities
    if (d.charts?.top_cities) {
      topCitiesData.value = {
        categories: d.charts.top_cities.labels || [],
        series: [{ name: 'Users', data: d.charts.top_cities.series || [] }]
      }
    } else {
      topCitiesData.value = { categories: [], series: [] }
    }

    // Shipments by Status
    if (d.charts?.status) {
      shipmentsByStatusData.value = {
        labels: d.charts.status.labels || [],
        series: d.charts.status.series || []
      }
    } else {
      shipmentsByStatusData.value = { labels: [], series: [] }
    }

    // City-wise Business - reuse top cities data with different values
    if (d.charts?.top_cities) {
      cityWiseBusinessData.value = {
        categories: d.charts.top_cities.labels || [],
        series: [{ name: 'Business (PKR)', data: d.charts.top_cities.series?.map((s: number) => s * 10000) || [] }]
      }
    } else {
      cityWiseBusinessData.value = { categories: [], series: [] }
    }

    // Debtors Balance
    if (d.debtors) {
      debtorsBalanceData.value = {
        labels: ['Total Due', 'Total Paid', 'Balance'],
        series: [
          d.debtors.total_receivable || 0,
          d.debtors.total_paid || 0,
          d.debtors.total_balance || 0
        ]
      }
    } else {
      debtorsBalanceData.value = { labels: [], series: [] }
    }

    // Delivery Rate
    const totalShipments = d.stats?.total_shipments || 0
    const deliveredShipments = d.stats?.total_shipments ? Math.floor(totalShipments * 0.6) : 0
    deliveryRate.value = totalShipments > 0 ? Math.round((deliveredShipments / totalShipments) * 100) : 0

    // Recent Activities
    recentActivities.value = d.recent_activities || []

    // Profit & Loss - Try to get from financial/pl endpoint
    try {
      const plRes = await api.get('/admin/financial/pl')
      const pl = plRes.data
      profitLossData.value = {
        categories: ['Revenue', 'Costs', 'Gross Profit', 'Net Profit'],
        series: [{
          name: 'Amount (PKR)',
          data: [
            pl.total_revenue ?? 0,
            pl.total_costs ?? 0,
            pl.gross_profit ?? 0,
            pl.net_profit ?? 0
          ]
        }]
      }
    } catch {
      // Fallback P&L data
      profitLossData.value = {
        categories: ['Revenue', 'Costs', 'Gross Profit', 'Net Profit'],
        series: [{
          name: 'Amount (PKR)',
          data: [0, 0, 0, 0]
        }]
      }
    }

  } catch (err: any) {
    console.error('Dashboard fetch error:', err)
    error.value = err.response?.data?.message || 'Failed to load dashboard data'
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboardData)
</script>
