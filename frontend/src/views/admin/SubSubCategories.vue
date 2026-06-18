<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Sub Sub Categories'" />
    <DataTable :store="subSubCategoryStore" :columns="columns" title="Sub Sub Categories"
      addButtonLabel="Add Sub Sub Category" :modalComponent="SubSubCategoryFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import SubSubCategoryFormModal from '@/components/admin/SubSubCategoryFormModal.vue'
import { useSubSubCategoryStore } from '@/stores/subSubCategoryStore'
import type { ColumnDefinition } from '@/types/table'

const subSubCategoryStore = useSubSubCategoryStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  {
    key: 'category',
    label: 'Category',
    format: (value: any) => value?.name || 'N/A',
  },
  {
    key: 'sub_category',
    label: 'Sub Category',
    format: (value: any) => value?.name || 'N/A',
  },
  { key: 'description', label: 'Description' },
  {
    key: 'status',
    label: 'Status',
    format: (value: string) => value, // will be rendered as badge
  },
]

onMounted(() => {
  subSubCategoryStore.fetchItems(1)
})
</script>
