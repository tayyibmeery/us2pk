<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Cities'" />
    <DataTable :store="cityStore" :columns="columns" title="Cities" addButtonLabel="Add City"
      :modalComponent="CityFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import CityFormModal from '@/components/admin/CityFormModal.vue'
import { useCityStore } from '@/stores/cityStore'
import type { ColumnDefinition } from '@/types/table'

const cityStore = useCityStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'city_name', label: 'City Name' },
  { key: 'city_code', label: 'Code' },
  {
    key: 'status',
    label: 'Status',
    // Format is optional – the table automatically renders a badge for status
    format: (value: boolean) => value ? 'Active' : 'Inactive'
  },
]

onMounted(() => {
  cityStore.fetchItems(1)
})
</script>
