<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Settings'" />
    <DataTable :store="settingStore" :columns="columns" title="Settings" addButtonLabel="Add Setting"
      :modalComponent="SettingFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import SettingFormModal from '@/components/admin/SettingFormModal.vue'
import { useSettingStore } from '@/stores/settingStore'
import type { ColumnDefinition } from '@/types/table'

const settingStore = useSettingStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'value', label: 'Value' },
  { key: 'description', label: 'Description' },
]

onMounted(() => {
  settingStore.fetchItems(1)
})
</script>
