<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Warehouses'" />
    <DataTable :store="warehouseStore" :columns="columns" title="Warehouses" addButtonLabel="Add Warehouse"
      :modalComponent="WarehouseFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import DataTable from '@/components/common/DataTable.vue';
import WarehouseFormModal from '@/components/admin/WarehouseFormModal.vue';
import { useWarehouseStore } from '@/stores/warehouseStore';
import type { ColumnDefinition } from '@/types/table';

const warehouseStore = useWarehouseStore();

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'code', label: 'Code' },
  { key: 'address', label: 'Address' },
  {
    key: 'status',
    label: 'Status',
    render: (row: any) => (row.status ? 'Active' : 'Inactive'),
  },
];

onMounted(() => {
  warehouseStore.fetchItems(1);
});
</script>
