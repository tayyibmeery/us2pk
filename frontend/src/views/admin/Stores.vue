<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Stores'" />
    <DataTable :store="storeStore" :columns="columns" title="Stores" addButtonLabel="Add Store"
      :modalComponent="StoreFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import StoreFormModal from '@/components/admin/StoreFormModal.vue'
import { useStoreStore } from '@/stores/storeStore'
import type { ColumnDefinition } from '@/types/table'

const storeStore = useStoreStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'warehouse', label: 'Warehouse' },
  { key: 'category', label: 'Category' },
  { key: 'days', label: 'Days' },
  {
    key: 'status',
    label: 'Status',
    format: (value: boolean) => value ? 'Active' : 'Inactive',
  },
]

onMounted(() => {
  storeStore.fetchItems(1)
})
</script>
