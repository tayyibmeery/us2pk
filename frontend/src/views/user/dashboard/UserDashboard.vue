<template>
  <div class="p-4 md:p-6 space-y-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Welcome Section -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl md:text-2xl font-semibold text-gray-800 dark:text-white">Dashboard Overview</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Welcome back, {{ user?.name }}! Here's what's happening with
          your shipments.</p>
      </div>
      <router-link to="/user/my-shipments"
        class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        View All Shipments
      </router-link>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
        class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-4">
        <div
          class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-xl text-blue-500 dark:text-blue-400">
          📦
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Total Shipments</p>
          <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ stats.total || 0 }}</h4>
        </div>
      </div>
      <div
        class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-4">
        <div
          class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-xl text-amber-500 dark:text-amber-400">
          🚚
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">In Transit</p>
          <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ stats.inTransit || 0 }}</h4>
        </div>
      </div>
      <div
        class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-4">
        <div
          class="w-11 h-11 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-xl text-green-500 dark:text-green-400">
          ✅
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Delivered</p>
          <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ stats.delivered || 0 }}</h4>
        </div>
      </div>
      <div
        class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center gap-4">
        <div
          class="w-11 h-11 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-xl text-red-500 dark:text-red-400">
          ⏳
        </div>
        <div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
          <h4 class="text-xl font-bold text-gray-800 dark:text-white">{{ stats.pending || 0 }}</h4>
        </div>
      </div>
    </div>

    <!-- Recent Shipments -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="text-base font-semibold text-gray-800 dark:text-white">Recent Shipments</h3>
          <p class="text-xs text-gray-400">Your latest shipments</p>
        </div>
        <router-link to="/user/my-shipments"
          class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium">
          View All →
        </router-link>
      </div>
      <div v-if="loading" class="space-y-2">
        <div v-for="i in 3" :key="i" class="animate-pulse bg-gray-100 dark:bg-gray-700 rounded-lg h-16"></div>
      </div>
      <ul v-else class="divide-y divide-gray-100 dark:divide-gray-700">
        <li v-for="shipment in recentShipments" :key="shipment.id" class="py-3 flex items-center justify-between">
          <div>
            <p class="font-medium text-gray-700 dark:text-gray-200">{{ shipment.shipment_code }}</p>
            <p class="text-xs text-gray-400">{{ shipment.description || 'No description' }}</p>
          </div>
          <div class="flex items-center gap-3">
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium" :class="{
              'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': shipment.shipment_status?.name === 'Delivered',
              'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300': shipment.shipment_status?.name === 'In Transit' || shipment.shipment_status?.name === 'Departed Operations Facility - In Transit',
              'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': shipment.shipment_status?.name === 'Pending' || shipment.shipment_status?.name === 'Bought by Customer' || shipment.shipment_status?.name === 'Bought by Company'
            }">
              {{ shipment.shipment_status?.name || 'Pending' }}
            </span>
            <router-link :to="`/user/my-shipments/${shipment.id}`"
              class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">View</router-link>
          </div>
        </li>
        <li v-if="!recentShipments.length" class="py-6 text-center text-sm text-gray-400">No recent shipments</li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useUserShipmentStore } from '@/stores/userShipmentStore'
import { useToast } from '@/composables/useToast'

const authStore = useAuthStore()
const shipmentStore = useUserShipmentStore()
const toast = useToast()

const user = computed(() => authStore.user)
const stats = computed(() => shipmentStore.stats)
const recentShipments = computed(() => shipmentStore.recentShipments)
const loading = ref(false)

const fetchDashboardData = async () => {
  loading.value = true
  try {
    await shipmentStore.fetchDashboardStats()
  } catch (error: any) {
    toast.error('❌ ' + (error.message || 'Failed to load dashboard data'))
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
