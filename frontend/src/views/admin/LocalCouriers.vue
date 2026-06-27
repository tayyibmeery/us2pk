<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Local Couriers'" />
    <DataTable :store="store" :columns="columns" title="Local Couriers" addButtonLabel="Add Courier"
      :modalComponent="LocalCourierFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import LocalCourierFormModal from '@/components/admin/LocalCourierFormModal.vue'
import { useLocalCourierStore } from '@/stores/localCourierStore'
import type { ColumnDefinition } from '@/types/table'

const store = useLocalCourierStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  {
    key: 'status',
    label: 'Status',
    format: (value: boolean) => value ? 'Active' : 'Inactive',
  },
]

onMounted(() => {
  store.fetchItems(1)
})
</script>
