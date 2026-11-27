<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Prohibited Items'" />

    <DataTable :store="prohibitedItemStore" :columns="columns" title="Prohibited Items"
      addButtonLabel="Add Prohibited Item" :modalComponent="ProhibitedItemFormModal">
      <!-- Custom icon column rendering -->
      <template #cell-icon="{ item }">
        <div v-if="item.icon" class="text-lg">
          <i :class="item.icon"></i>
        </div>
        <span v-else class="text-gray-400 text-xs">—</span>
      </template>

      <!-- Custom severity column rendering -->
      <template #cell-severity="{ item }">
        <span :class="{
          'px-2 py-1 text-xs font-medium rounded-full': true,
          'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': item.severity === 'high',
          'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': item.severity === 'medium',
          'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': item.severity === 'low',
        }">
          {{ getSeverityLabel(item.severity) }}
        </span>
      </template>

      <!-- Custom status column rendering -->
      <template #cell-status="{ item }">
        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
          :class="item.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
          <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="item.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
          {{ item.is_active ? 'Active' : 'Inactive' }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import ProhibitedItemFormModal from '@/components/admin/ProhibitedItemFormModal.vue'
import { useProhibitedItemStore } from '@/stores/prohibitedItemStore'
import type { ColumnDefinition } from '@/types/table'

const prohibitedItemStore = useProhibitedItemStore()

const getSeverityLabel = (severity: string): string => {
  const labels: Record<string, string> = {
    high: 'High Risk',
    medium: 'Medium Risk',
    low: 'Low Risk',
  }
  return labels[severity] || severity
}

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'icon', label: 'Icon', sortable: false },
  { key: 'item_name', label: 'Item Name', sortable: true },
  { key: 'category', label: 'Category', sortable: true },
  { key: 'severity', label: 'Severity', sortable: true },
  { key: 'order', label: 'Order', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  prohibitedItemStore.fetchItems(1)
  prohibitedItemStore.fetchCategories()
})
</script>
