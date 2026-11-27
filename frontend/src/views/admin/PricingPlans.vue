<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Pricing Plans'" />
    <DataTable :store="pricingPlanStore" :columns="columns" title="Pricing Plans" addButtonLabel="Add Pricing Plan"
      :modalComponent="PricingPlanFormModal">
      <!-- Custom featured column rendering -->
      <template #cell-featured="{ item }">
        <span v-if="item.featured" class="text-yellow-500">
          <i class="fas fa-star"></i>
        </span>
        <span v-else class="text-gray-300 dark:text-gray-600">—</span>
      </template>

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
import PricingPlanFormModal from '@/components/admin/PricingPlanFormModal.vue'
import { usePricingPlanStore } from '@/stores/pricingPlanStore'
import type { ColumnDefinition } from '@/types/table'

const pricingPlanStore = usePricingPlanStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'title', label: 'Title', sortable: true },
  { key: 'price', label: 'Price', sortable: true },
  { key: 'interval', label: 'Interval', sortable: true },
  { key: 'featured', label: 'Featured', sortable: true },
  { key: 'order', label: 'Order', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  pricingPlanStore.fetchItems(1)
})
</script>
