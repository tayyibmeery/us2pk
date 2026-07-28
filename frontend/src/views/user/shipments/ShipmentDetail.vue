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

      <!-- Header - Shipment Code & Status -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ shipment.shipment_code }}</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
              <span class="font-medium">Order Date:</span> {{ formatDate(shipment.created_at) }}
            </p>
          </div>
          <div class="flex flex-col items-end gap-2">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
            <ShipmentStatusBadge :status="shipment.shipment_status?.name" class="text-base px-4 py-2" />
          </div>
        </div>
      </div>

      <!-- Key Information Grid -->
      <div class="p-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Column 1 -->
        <div class="space-y-4">
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Weight</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.weight }}
              {{ shipment.weight_unit || 'kg' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Bought By</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.bought_by || 'N/A' }}</p>
          </div>
          <!-- <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Site</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.site?.name || 'N/A' }}</p>
          </div> -->
        </div>

        <!-- Column 2 -->
        <div class="space-y-4">
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold"> Courier</p>
            <p class="font-medium text-gray-800 dark:text-white">{{ shipment.local_courier?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
              {{ shipment.date_delivered ? 'Delivered On' : 'Expected Delivery' }}
            </p>
            <p class="font-medium text-gray-800 dark:text-white">
              {{ shipment.date_delivered ? formatDate(shipment.date_delivered) : formatDate(shipment.expected_delivery_date) || 'N/A' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Description - Full Width at Bottom -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mb-2">Description</p>
        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
          {{ shipment.description || 'No description provided.' }}</p>
      </div>

      <!-- Financial Summary -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Financial
          Summary</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div
            class="bg-white dark:bg-gray-700/30 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-600">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Payable</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ formatCurrency(shipment.total) }}</p>
          </div>
          <div
            class="bg-white dark:bg-gray-700/30 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-600">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Paid Amount</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
              {{ formatCurrency(shipment.received_amount) }}
            </p>
          </div>
          <div
            class="bg-white dark:bg-gray-700/30 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-600">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Payable Amount</p>
            <p class="text-2xl font-bold"
              :class="(shipment.total - shipment.received_amount) > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
              {{ formatCurrency(shipment.total - shipment.received_amount) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Payment History Table -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Payment History
        </h3>

        <div v-if="shipment.payments && shipment.payments.length" class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-800">
              <tr>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Date</th>
                <th
                  class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Amount (PKR)</th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Method</th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Reference</th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Notes</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="payment in shipment.payments" :key="payment.id"
                class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ formatDate(payment.payment_date) }}</td>
                <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">
                  {{ formatCurrency(payment.amount) }}
                </td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ payment.payment_method || '—' }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ payment.reference_number || '—' }}</td>
                <td class="px-4 py-3 max-w-xs truncate text-gray-600 dark:text-gray-400" :title="payment.notes">
                  {{ payment.notes || '—' }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800/80 border-t border-gray-200 dark:border-gray-700">
              <tr>
                <td class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300" colspan="1">Total Paid
                </td>
                <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                  {{ formatCurrency(shipment.received_amount) }}
                </td>
                <td colspan="3"></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div v-else class="text-center py-6 text-gray-400 dark:text-gray-500">
          No payment records found.
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
const shipment = ref<any>(null)

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
