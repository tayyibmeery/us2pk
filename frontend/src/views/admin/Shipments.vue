<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Shipments'" />

    <div
      class="flex flex-wrap items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
        <select v-model="statusFilter" @change="onStatusChange"
          class="h-9 rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
          <option value="">All</option>
          <option v-for="status in statusOptions" :key="status.id" :value="status.id">
            {{ status.name }}
          </option>
        </select>
      </div>
      <button @click="clearFilters" class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">
        Clear
      </button>
    </div>

    <DataTable :store="shipmentStore" :columns="columns" title="All Shipments" addButtonLabel="New Shipment"
      :modalComponent="ShipmentFormModal" :selfSaving="true" @saved="handleSaved">

      <template #cell-status="{ item }">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
            :class="statusBadgeClass(item.shipment_status?.name || '')">
            <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(item.shipment_status?.name || '')"></span>
            {{ item.shipment_status?.name || 'Unknown' }}
          </span>
          <select @change="updateStatus(item.id, Number(($event.target as HTMLSelectElement).value))"
            class="h-6 rounded border border-gray-200 bg-white text-xs dark:border-gray-700 dark:bg-gray-800">
            <option v-for="status in statusOptions" :key="status.id" :value="status.id"
              :selected="status.id === item.shipment_status_id">
              {{ status.name }}
            </option>
          </select>
        </div>
      </template>

      <template #cell-total="{ item }">
        <span class="font-mono">{{ formatCurrency(item.total) }}</span>
      </template>

      <template #cell-user="{ item }">
        {{ item.user?.name || 'N/A' }}
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import ShipmentFormModal from '@/components/admin/ShipmentFormModal.vue'
import { useShipmentStore } from '@/stores/shipmentStore'
import { useShipmentStatusStore } from '@/stores/shipmentStatusStore'
import type { ColumnDefinition } from '@/types/table'

const shipmentStore = useShipmentStore()
const statusStore = useShipmentStatusStore()
const statusOptions = ref<{ id: number; name: string }[]>([])
const statusFilter = ref('')

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'pcode', label: 'PCode' },
  { key: 'user', label: 'User' },
  {
    key: 'weight',
    label: 'Weight',
    format: (v: any) => {
      if (v === undefined || v === null || v === '') return '0.00'
      const num = typeof v === 'string' ? parseFloat(v) : v
      return isNaN(num) ? '0.00' : num.toFixed(2)
    },
  },
  {
    key: 'shipment_status',
    label: 'Status',
    format: (value: any) => value?.name || 'Unknown',
  },
  {
    key: 'site',
    label: 'Site',
    format: (value: any) => value?.name || '-',
  },
  {
    key: 'arrival_date',
    label: 'Arrival',
    format: (v: string) => (v ? new Date(v).toLocaleDateString() : '-'),
  },
  {
    key: 'total',
    label: 'Total (PKR)',
    format: (v: any) => formatCurrency(v),
  },
  {
    key: 'delivery_charges',
    label: 'Delivery Charges',
    format: (v: any) => {
      if (v === undefined || v === null || v === '') return '0.00'
      const num = typeof v === 'string' ? parseFloat(v) : v
      return isNaN(num) ? '0.00' : num.toFixed(2)
    },
  },
  {
    key: 'created_at',
    label: 'Created',
    format: (v: string) => (v ? new Date(v).toLocaleDateString() : '-'),
  },
]

const statusBadgeClass = (status: string) => {
  const map: Record<string, string> = {
    Delivered: 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
    'Out for Delivery': 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
    'Reached Lahore Company Office': 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
    'Custom Office at Lahore Airport': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
    'Departed Operations Facility - In Transit': 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
    'Reached Shipment in USA facility': 'bg-cyan-50 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400',
    'Bought by Company': 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
    'Bought by Customer': 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
  }
  return map[status] || 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400'
}

const statusDotClass = (status: string) => {
  const map: Record<string, string> = {
    Delivered: 'bg-green-500',
    'Out for Delivery': 'bg-blue-500',
    'Reached Lahore Company Office': 'bg-purple-500',
    'Custom Office at Lahore Airport': 'bg-yellow-500',
    'Departed Operations Facility - In Transit': 'bg-indigo-500',
    'Reached Shipment in USA facility': 'bg-cyan-500',
    'Bought by Company': 'bg-gray-500',
    'Bought by Customer': 'bg-gray-500',
  }
  return map[status] || 'bg-gray-500'
}

const formatCurrency = (value: any) => {
  const num = typeof value === 'string' ? parseFloat(value) : value
  if (isNaN(num)) return 'PKR 0.00'
  return new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(num)
}

const fetchStatuses = async () => {
  await statusStore.fetchItems(1, { per_page: 100 })
  statusOptions.value = statusStore.items
}

const updateStatus = async (id: number, statusId: number) => {
  try {
    await shipmentStore.updateStatus(id, statusId)
  } catch (error) {
    console.error('Status update failed:', error)
  }
}

const onStatusChange = () => {
  shipmentStore.setStatusFilter(String(statusFilter.value))
}

const clearFilters = () => {
  statusFilter.value = ''
  shipmentStore.setStatusFilter('')
}

// ✅ Called by DataTable after modal emits 'saved'
const handleSaved = async () => {
  await shipmentStore.fetchItems(shipmentStore.pagination?.current_page || 1)
}

onMounted(() => {
  shipmentStore.fetchItems(1)
  fetchStatuses()
})
</script>
