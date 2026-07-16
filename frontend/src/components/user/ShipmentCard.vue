<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow duration-200">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3">
          <h3 class="font-semibold text-gray-800 dark:text-white">{{ shipment.shipment_code }}</h3>
          <ShipmentStatusBadge :status="shipment.status" />
        </div>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ shipment.description || 'No description' }}</p>
        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
          <span>📦 {{ shipment.weight }} {{ shipment.weight_unit || 'kg' }}</span>
          <span>📍 {{ shipment.destination || 'N/A' }}</span>
          <span>📅 {{ formatDate(shipment.created_at) }}</span>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <router-link :to="`/my-shipments/${shipment.id}`"
          class="px-3 py-1.5 text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 font-medium">View
          Details</router-link>
        <button @click="trackShipment(shipment)"
          class="px-3 py-1.5 text-sm bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition">Track</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import ShipmentStatusBadge from './ShipmentStatusBadge.vue'

const props = defineProps<{
  shipment: any
}>()

const router = useRouter()

const formatDate = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const trackShipment = (shipment: any) => {
  router.push(`/track-shipment/${shipment.id}`)
}
</script>
