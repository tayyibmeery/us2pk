<template>
  <div class="space-y-6">
    <!-- Welcome Section -->
    <div class="p-6 bg-white rounded-2xl shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Welcome back, {{ user?.name }}! 👋</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Here's what's happening with your shipments today.</p>
        </div>
        <router-link to="/user/my-shipments"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          New Shipment
        </router-link>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Shipments</p>
        <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ stats.total }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Transit</p>
        <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ stats.inTransit }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivered</p>
        <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ stats.delivered }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
        <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ stats.pending }}</p>
      </div>
    </div>

    <!-- Recent Shipments -->
    <div
      class="bg-white rounded-2xl shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Shipments</h2>
        <router-link to="/user/my-shipments"
          class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">View All →</router-link>
      </div>
      <div class="divide-y divide-gray-200 dark:divide-gray-700">
        <div v-for="shipment in recentShipments" :key="shipment.id"
          class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-800 dark:text-white">{{ shipment.shipment_code }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ shipment.description || 'No description' }}</p>
            </div>
            <div class="flex items-center gap-4">
              <ShipmentStatusBadge :status="shipment.shipment_status?.name" />
              <router-link :to="`/user/my-shipments/${shipment.id}`"
                class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">View</router-link>
            </div>
          </div>
        </div>
        <div v-if="recentShipments.length === 0" class="p-8 text-center text-gray-500 dark:text-gray-400">
          No shipments found. Start by creating your first shipment!
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'
import ShipmentStatusBadge from '@/components/user/ShipmentStatusBadge.vue'

console.log('📊 UserDashboard component is rendering!')

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const stats = ref({
  total: 0,
  inTransit: 0,
  delivered: 0,
  pending: 0,
  change: '+12%'
})

const recentShipments = ref([])

const fetchDashboardData = async () => {
  try {
    const res = await api.get('/user/dashboard/stats')
    stats.value = res.data.stats
    recentShipments.value = res.data.recent_shipments
    console.log('📊 Dashboard data fetched:', stats.value)
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  }
}

onMounted(() => {
  console.log('📊 UserDashboard mounted!')
  fetchDashboardData()
})
</script>
