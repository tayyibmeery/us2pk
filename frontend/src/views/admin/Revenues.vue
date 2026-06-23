<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Revenues'" />
    <DataTable :store="revenueStore" :columns="columns" title="Revenues" addButtonLabel="Add Revenue"
      :modalComponent="RevenueFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import RevenueFormModal from '@/components/admin/RevenueFormModal.vue'
import { useRevenueStore } from '@/stores/revenueStore'
import type { ColumnDefinition } from '@/types/table'

const revenueStore = useRevenueStore()

const columns: ColumnDefinition[] = [
  { key: 'invoice_no', label: 'Invoice No' },
  { key: 'date', label: 'Date' },
  { key: 'type', label: 'Type' },
  { key: 'pcode', label: 'PCODE' },
  {
    key: 'revenue',
    label: 'Revenue',
    format: (value: number) => `$${value.toFixed(2)}`,
  },
  {
    key: 'net_revenue',
    label: 'Net Revenue',
    format: (value: number) => `$${value.toFixed(2)}`,
  },
]

onMounted(() => {
  revenueStore.fetchItems(1)
})
</script>
