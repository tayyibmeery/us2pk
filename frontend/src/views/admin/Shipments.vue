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
      <template #actions="{ item, edit, delete: deleteFn }">
        <div class="flex items-center justify-end gap-0.5">
          <!-- View -->
          <router-link :to="`/admin/shipments/${item.id}`"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
            title="View">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </router-link>

          <!-- Edit -->
          <button @click="edit(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
            title="Edit">
            <!-- reuse the edit SVG -->
            <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M17.0911 3.53206C16.2124 2.65338 14.7878 2.65338 13.9091 3.53206L5.6074 11.8337C5.29899 12.1421 5.08687 12.5335 4.99684 12.9603L4.26177 16.445C4.20943 16.6931 4.286 16.9508 4.46529 17.1301C4.64458 17.3094 4.90232 17.3859 5.15042 17.3336L8.63507 16.5985C9.06184 16.5085 9.45324 16.2964 9.76165 15.988L18.0633 7.68631C18.942 6.80763 18.942 5.38301 18.0633 4.50433L17.0911 3.53206ZM14.9697 4.59272C15.2626 4.29982 15.7375 4.29982 16.0304 4.59272L17.0027 5.56499C17.2956 5.85788 17.2956 6.33276 17.0027 6.62565L16.1043 7.52402L14.0714 5.49109L14.9697 4.59272ZM13.0107 6.55175L6.66806 12.8944C6.56526 12.9972 6.49455 13.1277 6.46454 13.2699L5.96704 15.6283L8.32547 15.1308C8.46772 15.1008 8.59819 15.0301 8.70099 14.9273L15.0436 8.58468L13.0107 6.55175Z"
                fill="currentColor" />
            </svg>
          </button>

          <!-- Delete -->
          <button @click="deleteFn(item.id)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            title="Delete">
            <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.04142 4.29199C7.04142 3.04935 8.04878 2.04199 9.29142 2.04199H11.7081C12.9507 2.04199 13.9581 3.04935 13.9581 4.29199V4.54199H16.1252H17.166C17.5802 4.54199 17.916 4.87778 17.916 5.29199C17.916 5.70621 17.5802 6.04199 17.166 6.04199H16.8752V8.74687V13.7469V16.7087C16.8752 17.9513 15.8678 18.9587 14.6252 18.9587H6.37516C5.13252 18.9587 4.12516 17.9513 4.12516 16.7087V13.7469V8.74687V6.04199H3.8335C3.41928 6.04199 3.0835 5.70621 3.0835 5.29199C3.0835 4.87778 3.41928 4.54199 3.8335 4.54199H4.87516H7.04142V4.29199ZM15.3752 13.7469V8.74687V6.04199H13.9581H13.2081H7.79142H7.04142H5.62516V8.74687V13.7469V16.7087C5.62516 17.1229 5.96095 17.4587 6.37516 17.4587H14.6252C15.0394 17.4587 15.3752 17.1229 15.3752 16.7087V13.7469ZM8.54142 4.54199H12.4581V4.29199C12.4581 3.87778 12.1223 3.54199 11.7081 3.54199H9.29142C8.87721 3.54199 8.54142 3.87778 8.54142 4.29199V4.54199ZM8.8335 8.50033C9.24771 8.50033 9.5835 8.83611 9.5835 9.25033V14.2503C9.5835 14.6645 9.24771 15.0003 8.8335 15.0003C8.41928 15.0003 8.0835 14.6645 8.0835 14.2503V9.25033C8.0835 8.83611 8.41928 8.50033 8.8335 8.50033ZM12.9168 9.25033C12.9168 8.83611 12.581 8.50033 12.1668 8.50033C11.7526 8.50033 11.4168 8.83611 11.4168 9.25033V14.2503C11.4168 14.6645 11.7526 15.0003 12.1668 15.0003C12.581 15.0003 12.9168 14.6645 12.9168 14.2503V9.25033Z"
                fill="currentColor" />
            </svg>
          </button>
        </div>
      </template>

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
  // { key: 'id', label: 'ID' },
  { key: 'user', label: 'User' },
  { key: 'shipment_code', label: 'Ship-Code' },

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
  // {
  //   key: 'site',
  //   label: 'Site',
  //   format: (value: any) => value?.name || '-',
  // },
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
    key: 'receivable_cod',
    label: 'COD (PKR)',
    format: (v: any) => formatCurrency(v),
  },
  {
    key: 'amount_due',
    label: ' Amount Due (PKR)',
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
