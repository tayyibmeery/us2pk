<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Quote Requests'" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
      <!-- Stats cards (same as before) -->
      <!-- <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</span>
          <span
            class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
            {{ quoteRequestStore.stats?.total || 0 }}
          </span>
        </div>
        <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ quoteRequestStore.stats?.total || 0 }}</p>
      </div> -->
      <!-- ... other stats cards ... -->
    </div>

    <DataTable :store="quoteRequestStore" :columns="columns" title="Quote Requests" addButtonLabel=""
      :modalComponent="null" :showAddButton="false">
      <!-- Custom status column rendering -->
      <template #cell-status="{ item }">
        <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusClass(item.status)">
          {{ getStatusLabel(item.status) }}
        </span>
      </template>

      <!-- Custom actions column with View, Status Change, and Delete -->
      <template #actions="{ item }">
        <div class="flex items-center justify-end gap-1">
          <!-- View Button -->
          <button @click="openViewModal(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
            title="View Details">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </button>

          <!-- Status Change Dropdown -->
          <div class="relative" @click.stop>
            <button @click="toggleStatusDropdown(item.id)"
              class="flex h-8 items-center gap-1 rounded-lg px-2 text-xs font-medium text-gray-600 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
              title="Change Status">
              <span>Status</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div v-if="activeDropdown === item.id"
              class="absolute right-0 mt-1 w-40 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800 z-10">
              <button v-for="status in statusOptions" :key="status.value" @click="updateStatus(item.id, status.value)"
                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                :class="{ 'font-semibold': item.status === status.value }">
                <span class="w-2 h-2 rounded-full mr-2" :class="status.color"></span>
                {{ status.label }}
              </button>
            </div>
          </div>

          <!-- Delete Button -->
          <button @click="deleteItem(item.id)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            title="Delete">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
            </svg>
          </button>
        </div>
      </template>
    </DataTable>

    <!-- View Quote Modal -->
    <QuoteRequestFormModal :isOpen="viewModalOpen" :initialData="selectedQuote" @close="viewModalOpen = false" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import QuoteRequestFormModal from '@/components/admin/QuoteRequestFormModal.vue'
import { useQuoteRequestStore } from '@/stores/quoteRequestStore'
import type { ColumnDefinition } from '@/types/table'
import type { QuoteRequest } from '@/stores/quoteRequestStore'

const quoteRequestStore = useQuoteRequestStore()
const viewModalOpen = ref(false)
const selectedQuote = ref<QuoteRequest | null>(null)
const activeDropdown = ref<number | null>(null)

const statusOptions = [
  { value: 'pending', label: 'Pending', color: 'bg-yellow-500' },
  { value: 'contacted', label: 'Contacted', color: 'bg-blue-500' },
  { value: 'completed', label: 'Completed', color: 'bg-green-500' },
]

const getStatusClass = (status: string): string => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    contacted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    pending: 'Pending',
    contacted: 'Contacted',
    completed: 'Completed'
  }
  return labels[status] || status
}

const toggleStatusDropdown = (id: number) => {
  activeDropdown.value = activeDropdown.value === id ? null : id
}

const openViewModal = (item: QuoteRequest) => {
  selectedQuote.value = item
  viewModalOpen.value = true
  activeDropdown.value = null
}

const updateStatus = async (id: number, status: string) => {
  try {
    await quoteRequestStore.updateStatus(id, status)
    await quoteRequestStore.fetchItems(quoteRequestStore.pagination?.current_page || 1)
    await quoteRequestStore.fetchStats()
    activeDropdown.value = null
  } catch (error) {
    alert('Failed to update status')
  }
}

const deleteItem = async (id: number) => {
  if (!confirm('Delete this quote request? This action cannot be undone.')) return
  try {
    await quoteRequestStore.delete(id)
    await quoteRequestStore.fetchItems(quoteRequestStore.pagination?.current_page || 1)
    await quoteRequestStore.fetchStats()
  } catch (error) {
    alert('Failed to delete quote request')
  }
}

// Close dropdown when clicking outside
const handleClickOutside = () => {
  activeDropdown.value = null
}

// Add click outside listener
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  quoteRequestStore.fetchItems(1)
  quoteRequestStore.fetchStats()
})

// Clean up
import { onBeforeUnmount } from 'vue'
onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'mobile', label: 'Mobile', sortable: true },
  { key: 'service', label: 'Service', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'created_at', label: 'Created', sortable: true },
]
</script>
