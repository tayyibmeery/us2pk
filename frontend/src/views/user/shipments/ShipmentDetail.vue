<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Shipment Details</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">View and track your shipment</p>
      </div>
      <router-link to="/user/my-shipments"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="w-8 h-8 border-4 border-gray-200 dark:border-gray-700 border-t-brand-500 rounded-full animate-spin">
      </div>
    </div>

    <!-- Shipment Details -->
    <div v-else-if="shipment"
      class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
      <!-- Status Bar -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ shipment.shipment_code }}</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Created {{ formatDate(shipment.created_at) }}</p>
        </div>
        <ShipmentStatusBadge :status="shipment.shipment_status?.name" />
      </div>

      <!-- Details Grid -->
      <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="space-y-4">
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.description || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Weight</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.weight }}
              {{ shipment.weight_unit || 'kg' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Seller Tracker</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.seller_tracker_id || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Site</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.site?.name || 'N/A' }}</p>
          </div>
        </div>
        <div class="space-y-4">
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bought By</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.bought_by || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment Method</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.payment_method?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Local Courier</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.local_courier?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Consolidation</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.consolidation?.consol_id || 'N/A' }}</p>
          </div>
        </div>
      </div>

      <!-- Financials -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Financial Summary</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Item Value</p>
            <p class="font-bold text-gray-800 dark:text-white">{{ formatCurrency(shipment.item_value_pkr) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Company Charges</p>
            <p class="font-bold text-gray-800 dark:text-white">{{ formatCurrency(shipment.company_charges) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Payable</p>
            <p class="font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(shipment.total) }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Received Amount</p>
            <p class="font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(shipment.received_amount) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Not Found -->
    <div v-else class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
      <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white">Shipment Not Found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">The shipment you're looking for doesn't exist.</p>
      <router-link to="/user/my-shipments" class="inline-block mt-4 text-sm text-brand-500 hover:text-brand-600">Back to
        Shipments</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import ShipmentStatusBadge from '@/components/user/ShipmentStatusBadge.vue'

console.log('📄 ShipmentDetail component loaded!')

const route = useRoute()
const loading = ref(true)
const shipment = ref(null)

const formatDate = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 0
  }).format(value || 0)
}

const fetchShipment = async () => {
  loading.value = true
  try {
    const res = await api.get(`/user/shipments/${route.params.id}`)
    shipment.value = res.data
  } catch (error) {
    console.error('Failed to fetch shipment:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchShipment()
})
</script>
