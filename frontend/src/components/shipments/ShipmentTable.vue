<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3">ID</th>
          <th scope="col" class="px-6 py-3">Tracking No.</th>
          <th scope="col" class="px-6 py-3">User</th>
          <th scope="col" class="px-6 py-3">Origin</th>
          <th scope="col" class="px-6 py-3">Destination</th>
          <th scope="col" class="px-6 py-3">Status</th>
          <th scope="col" class="px-6 py-3">Created</th>
          <th v-if="canManage" scope="col" class="px-6 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="shipment in shipments" :key="shipment.id"
          class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <td class="px-6 py-4">{{ shipment.id }}</td>
          <td class="px-6 py-4">{{ shipment.tracking_number }}</td>
          <td class="px-6 py-4">{{ shipment.user?.name || 'N/A' }}</td>
          <td class="px-6 py-4">{{ shipment.origin }}</td>
          <td class="px-6 py-4">{{ shipment.destination }}</td>
          <td class="px-6 py-4">
            <span :class="statusClass(shipment.status)" class="px-2 py-1 rounded-full text-xs">
              {{ shipment.status }}
            </span>
          </td>
          <td class="px-6 py-4">{{ formatDate(shipment.created_at) }}</td>
          <td v-if="canManage" class="px-6 py-4 flex gap-2">
            <button @click="emit('edit', shipment)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="emit('delete', shipment.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex justify-between items-center mt-4">
      <button @click="prevPage" :disabled="currentPage === 1"
        class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50">Previous</button>
      <span class="text-sm">Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages"
        class="px-4 py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50">Next</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps<{
  shipments: any[]
  currentPage: number
  totalPages: number
  canManage?: boolean // for admin
}>()

const emit = defineEmits(['edit', 'delete', 'page-change'])

const statusClass = (status: string) => {
  const map: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    shipped: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    delivered: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

const prevPage = () => {
  if (props.currentPage > 1) emit('page-change', props.currentPage - 1)
}
const nextPage = () => {
  if (props.currentPage < props.totalPages) emit('page-change', props.currentPage + 1)
}
</script>
