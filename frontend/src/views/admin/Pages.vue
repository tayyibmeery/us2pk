<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Pages'" />
    <DataTable :store="pageStore" :columns="columns" title="Pages" addButtonLabel="Add Page"
      :modalComponent="PageFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import PageFormModal from '@/components/admin/PageFormModal.vue'
import { usePageStore } from '@/stores/pageStore'
import type { ColumnDefinition } from '@/types/table'

const pageStore = usePageStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'title', label: 'Title' },
  { key: 'slug', label: 'Slug' },
  {
    key: 'status',
    label: 'Status',
    format: (value: boolean) => value ? 'Active' : 'Inactive',
  },
]

onMounted(() => {
  pageStore.fetchItems(1)
})
</script>
