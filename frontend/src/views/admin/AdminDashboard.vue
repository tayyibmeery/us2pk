<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <!-- <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Dashboard</h1>
      <button @click="logout" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
        Logout
      </button>
    </div> -->

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-10">
      <div class="loader">Loading...</div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-lg">
      {{ error }}
    </div>

    <!-- Dashboard Content -->
    <template v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="stat in stats" :key="stat.label"
          class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow border border-gray-200 dark:border-gray-700">
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ stat.label }}</p>
          <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ stat.value }}</h4>
        </div>
      </div>

      <!-- Charts Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Cities -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Top Cities (Users)</h3>
          <BarChartOne :chartData="topCitiesData" />
        </div>

        <!-- Shipments by Status -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Shipments by Status</h3>
          <PieChart :chartData="shipmentsByStatusData" />
        </div>

        <!-- City-wise Business -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">City-wise Business (PKR)</h3>
          <BarChartOne :chartData="cityWiseBusinessData" />
        </div>

        <!-- Debtors Balance -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Debtors Balance</h3>
          <DonutChart :chartData="debtorsBalanceData" />
        </div>
      </div>

      <!-- Profit & Loss Overview -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Profit & Loss Overview</h3>
        <LineChartOne :chartData="profitLossData" />
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import api from '@/api'
import BarChartOne from '@/components/charts/BarChartOne.vue'
import LineChartOne from '@/components/charts/LineChartOne.vue'
import PieChart from '@/components/charts/PieChart.vue'
import DonutChart from '@/components/charts/DonutChart.vue'

const router = useRouter()
const authStore = useAuthStore()

// Data
const loading = ref(true)
const error = ref('')

const stats = ref<{ label: string; value: number | string }[]>([])
const topCitiesData = ref({ categories: [], series: [] })
const shipmentsByStatusData = ref({ labels: [], series: [] })
const cityWiseBusinessData = ref({ categories: [], series: [] })
const debtorsBalanceData = ref({ labels: [], series: [] })
const profitLossData = ref({ categories: [], series: [] })

const logout = () => {
  authStore.logout()
  router.push('/signin')
}

const fetchDashboardData = async () => {
  try {
    loading.value = true
    error.value = ''

    // 1. Summary stats
    const dashboardRes = await api.get('/admin/dashboard')
    const { total_users, total_shipments, total_consolidations, pending_approvals } = dashboardRes.data
    stats.value = [
      { label: 'Total Users', value: total_users },
      { label: 'Total Shipments', value: total_shipments },
      { label: 'Total Consolidations', value: total_consolidations },
      { label: 'Pending Approvals', value: pending_approvals },
    ]

    // 2. Top Cities
    const citiesRes = await api.get('/admin/statistics/top-cities')
    topCitiesData.value = {
      categories: citiesRes.data.map((c: any) => c.city ?? 'Unknown'),
      series: [{ name: 'Users', data: citiesRes.data.map((c: any) => c.count ?? 0) }],
    }

    // 3. Shipments by Status
    const statusRes = await api.get('/admin/statistics/shipments')
    shipmentsByStatusData.value = {
      labels: statusRes.data.map((s: any) => s.status),
      series: statusRes.data.map((s: any) => s.total),
    }

    // 4. City-wise Business
    const businessRes = await api.get('/admin/statistics/city-wise-business')
    cityWiseBusinessData.value = {
      categories: businessRes.data.map((b: any) => b.city ?? 'Unknown'),
      series: [{ name: 'Business (PKR)', data: businessRes.data.map((b: any) => b.total ?? 0) }],
    }

    // 5. Debtors Balance
    const debtorsRes = await api.get('/admin/statistics/debtors-balance')
    debtorsBalanceData.value = {
      labels: ['Total Due', 'Total Paid', 'Balance'],
      series: [debtorsRes.total_due, debtorsRes.total_paid, debtorsRes.balance],
    }

    // 6. Profit & Loss
    const plRes = await api.get('/admin/financial/pl')
    profitLossData.value = {
      categories: ['Revenue', 'Costs', 'Gross Profit', 'Net Profit'],
      series: [
        {
          name: 'Amount (PKR)',
          data: [
            plRes.total_revenue ?? 0,
            plRes.total_costs ?? 0,
            plRes.gross_profit ?? 0,
            plRes.net_profit ?? 0,
          ]
        },
      ],
    }
  } catch (err: any) {
    console.error('Dashboard fetch error:', err)
    error.value = err.response?.data?.message || 'Failed to load dashboard data'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #465fff;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
