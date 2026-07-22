<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">My Shipments</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Track and manage all your shipments</p>
      </div>
      <!-- <router-link to="/user/new-shipment"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        New Shipment
      </router-link> -->
    </div>

    <!-- Filters -->
    <div
      class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <div class="flex-1 min-w-[200px]">
        <input v-model="search" type="text" placeholder="Search shipments..."
          class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
      </div>
      <select v-model="statusFilter"
        class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent">
        <option value="">All Statuses</option>
        <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
      </select>
      <button @click="clearFilters"
        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Clear</button>
    </div>

    <!-- Shipments Grid -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="w-8 h-8 border-4 border-gray-200 dark:border-gray-700 border-t-brand-500 rounded-full animate-spin">
      </div>
    </div>

    <div v-else-if="shipments.length === 0"
      class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
        viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
      <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white">No Shipments Yet</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">US2PK hasn't processed any shipments for you at this time.</p>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Once your first shipment is processed, it will appear
        here.</p>
      <router-link to="/"
        class="inline-block mt-4 text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">Contact Support
        →</router-link>
    </div>

    <div v-else class="grid grid-cols-1 gap-4">
      <ShipmentCard v-for="shipment in shipments" :key="shipment.id" :shipment="shipment" />
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="flex items-center justify-between py-4">
      <p class="text-sm text-gray-500 dark:text-gray-400">Showing {{ pagination.from || 0 }} - {{ pagination.to || 0 }}
        of {{ pagination.total || 0 }}</p>
      <div class="flex gap-2">
        <button @click="prevPage" :disabled="pagination.current_page <= 1"
          class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50 transition">Previous</button>
        <button @click="nextPage" :disabled="pagination.current_page >= pagination.last_page"
          class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50 transition">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import api from '@/services/api'
import ShipmentCard from '@/components/user/ShipmentCard.vue'

console.log('📦 MyShipments component loaded!')

const loading = ref(false)
const search = ref('')
const statusFilter = ref('')
const shipments = ref([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0,
})

const statuses = ['Pending', 'In Transit', 'Delivered', 'Cancelled']

const fetchShipments = async (page = 1) => {
  loading.value = true
  try {
    const params: any = { page, per_page: 10 }
    if (search.value) params.search = search.value
    if (statusFilter.value) params.status = statusFilter.value

    const res = await api.get('/user/shipments', { params })
    shipments.value = res.data.data
    pagination.value = {
      current_page: res.data.current_page || 1,
      last_page: res.data.last_page || 1,
      per_page: res.data.per_page || 10,
      total: res.data.total || 0,
      from: res.data.from || 0,
      to: res.data.to || 0,
    }
  } catch (error) {
    console.error('Failed to fetch shipments:', error)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  search.value = ''
  statusFilter.value = ''
  fetchShipments(1)
}

const prevPage = () => {
  if (pagination.value.current_page > 1) {
    fetchShipments(pagination.value.current_page - 1)
  }
}

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    fetchShipments(pagination.value.current_page + 1)
  }
}

watch([search, statusFilter], () => {
  fetchShipments(1)
})

onMounted(() => {
  fetchShipments()
})
</script>
