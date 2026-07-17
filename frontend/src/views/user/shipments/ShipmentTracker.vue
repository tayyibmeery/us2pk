<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Track Shipment</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Real-time tracking of your shipment</p>
      </div>
      <router-link to="/user/my-shipments"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
      </router-link>
    </div>

    <!-- Tracking Input -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <input v-model="trackingNumber" type="text" placeholder="Enter tracking number or shipment code"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
        </div>
        <button @click="trackShipment" :disabled="!trackingNumber || loading"
          class="px-6 py-3 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
          {{ loading ? 'Tracking...' : 'Track Now' }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="w-8 h-8 border-4 border-gray-200 dark:border-gray-700 border-t-brand-500 rounded-full animate-spin">
      </div>
    </div>

    <!-- Tracking Result -->
    <div v-else-if="shipment"
      class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
      <!-- Shipment Info -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ shipment.shipment_code }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Status: <span
                class="font-medium">{{ shipment.shipment_status?.name }}</span></p>
          </div>
          <ShipmentStatusBadge :status="shipment.shipment_status?.name" />
        </div>
      </div>

      <!-- Tracking Timeline -->
      <div class="p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-6">Tracking Timeline</h3>
        <TrackingTimeline :events="timeline" />
      </div>
    </div>

    <!-- Not Found -->
    <div v-else-if="searched" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white">Shipment Not Found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">No shipment found with the provided tracking number.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '@/services/api'
import ShipmentStatusBadge from '@/components/user/ShipmentStatusBadge.vue'
import TrackingTimeline from '@/components/user/TrackingTimeline.vue'

console.log('📍 ShipmentTracker component loaded!')

const trackingNumber = ref('')
const loading = ref(false)
const shipment = ref(null)
const timeline = ref([])
const searched = ref(false)

const trackShipment = async () => {
  if (!trackingNumber.value) return

  loading.value = true
  searched.value = true

  try {
    const res = await api.get(`/user/track/${trackingNumber.value}`)
    shipment.value = res.data.shipment
    timeline.value = res.data.timeline || []
  } catch (error) {
    console.error('Failed to track shipment:', error)
    shipment.value = null
    timeline.value = []
  } finally {
    loading.value = false
  }
}
</script>
