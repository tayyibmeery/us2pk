<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Shipment Statuses'" />
    <DataTable :store="shipmentStatusStore" :columns="columns" title="Shipment Statuses" addButtonLabel="Add Status"
      :modalComponent="ShipmentStatusFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import ShipmentStatusFormModal from '@/components/admin/ShipmentStatusFormModal.vue'
import { useShipmentStatusStore } from '@/stores/shipmentStatusStore'
import type { ColumnDefinition } from '@/types/table'

const shipmentStatusStore = useShipmentStatusStore()

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
  shipmentStatusStore.fetchItems(1)
})
</script>
