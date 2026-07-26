<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Statistics'" />
    <DataTable :store="statStore" :columns="columns" title="Statistics" addButtonLabel="Add Stats"
      :modalComponent="StatFormModal">
      <!-- Custom status column rendering -->
      <template #cell-status="{ item }">
        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
          :class="item.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
          <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="item.status ? 'bg-green-500' : 'bg-red-500'"></span>
          {{ item.status ? 'Active' : 'Inactive' }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import StatFormModal from '@/components/admin/StatFormModal.vue'
import { useStatStore } from '@/stores/statStore'
import type { ColumnDefinition } from '@/types/table'

const statStore = useStatStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'happy_clients', label: 'Happy Clients', sortable: true },
  { key: 'complete_shipments', label: 'Complete Shipments', sortable: true },
  { key: 'customer_reviews', label: 'Customer Reviews', sortable: true },
  { key: 'active_services', label: 'Active Services', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  statStore.fetchItems(1)
})
</script>
