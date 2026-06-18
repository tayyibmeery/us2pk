<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Sub Categories'" />
    <DataTable :store="subCategoryStore" :columns="columns" title="Sub Categories" addButtonLabel="Add Sub Category"
      :modalComponent="SubCategoryFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import SubCategoryFormModal from '@/components/admin/SubCategoryFormModal.vue'
import { useSubCategoryStore } from '@/stores/subCategoryStore'
import type { ColumnDefinition } from '@/types/table'

const subCategoryStore = useSubCategoryStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  {
    key: 'category',
    label: 'Category',
    format: (value: any) => value?.name || 'N/A',
  },
  { key: 'description', label: 'Description' },
  {
    key: 'status',
    label: 'Status',
    format: (value: string) => value, // 'Active' or 'Inactive' → will render as badge
  },
]

onMounted(() => {
  subCategoryStore.fetchItems(1)
})
</script>
