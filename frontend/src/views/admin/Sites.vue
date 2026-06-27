<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Sites'" />
    <DataTable :store="siteStore" :columns="columns" title="Sites" addButtonLabel="Add Site"
      :modalComponent="SiteFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import SiteFormModal from '@/components/admin/SiteFormModal.vue'
import { useSiteStore } from '@/stores/siteStore'
import type { ColumnDefinition } from '@/types/table'

const siteStore = useSiteStore()

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
  siteStore.fetchItems(1)
})
</script>
