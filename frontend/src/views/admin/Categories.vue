<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Categories'" />
    <DataTable :store="categoryStore" :columns="columns" title="Categories" addButtonLabel="Add Category"
      :modalComponent="CategoryFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import CategoryFormModal from '@/components/admin/CategoryFormModal.vue'
import { useCategoryStore } from '@/stores/categoryStore'
import type { ColumnDefinition } from '@/types/table'

const categoryStore = useCategoryStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'description', label: 'Description' },
  {
    key: 'status',
    label: 'Status',
    format: (value: string) => value, // 'Active' or 'Inactive' – will be shown as badge
  },
]

onMounted(() => {
  categoryStore.fetchItems(1)
})
</script>
