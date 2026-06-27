<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'International Couriers'" />
    <DataTable :store="store" :columns="columns" title="International Couriers" addButtonLabel="Add Courier"
      :modalComponent="InternationalCourierFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import InternationalCourierFormModal from '@/components/admin/InternationalCourierFormModal.vue'
import { useInternationalCourierStore } from '@/stores/internationalCourierStore'
import type { ColumnDefinition } from '@/types/table'

const store = useInternationalCourierStore()

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
