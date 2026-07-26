<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Footer Sections'" />
    <DataTable :store="footerSectionStore" :columns="columns" title="Footer Sections"
      addButtonLabel="Add Footer Section" :modalComponent="FooterSectionFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import FooterSectionFormModal from '@/components/admin/FooterSectionFormModal.vue'
import { useFooterSectionStore } from '@/stores/footerSectionStore'
import type { ColumnDefinition } from '@/types/table'

const footerSectionStore = useFooterSectionStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'title', label: 'Title' },
  { key: 'address', label: 'Address' },
  { key: 'phone', label: 'Phone' },
  { key: 'email', label: 'Email' },
  { key: 'whatsapp_number', label: 'WhatsApp' },
  {
    key: 'status',
    label: 'Status',
    format: (value: boolean) => value ? 'Active' : 'Inactive'
  },
]

onMounted(() => {
  footerSectionStore.fetchItems(1)
})
</script>
