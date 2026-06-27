<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Weight Discounts'" />
    <DataTable :store="weightDiscountStore" :columns="columns" title="Weight Discounts" addButtonLabel="Add Discount"
      :modalComponent="WeightDiscountFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import DataTable from '@/components/common/DataTable.vue';
import WeightDiscountFormModal from '@/components/admin/WeightDiscountFormModal.vue';
import { useWeightDiscountStore } from '@/stores/weightDiscountStore';
import type { ColumnDefinition } from '@/types/table';

const weightDiscountStore = useWeightDiscountStore();

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  {
    key: 'warehouse',
    label: 'Warehouse',
    format: (value: any) => value?.name || 'N/A',
  },
  {
    key: 'discount_percent',
    label: 'Discount %',
    format: (value: number) => `${value}%`,
  },
];

onMounted(() => {
  weightDiscountStore.fetchItems(1);
});
</script>
