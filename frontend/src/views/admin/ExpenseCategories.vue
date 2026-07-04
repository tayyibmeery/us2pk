<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Expense Categories'" />

    <DataTable :store="categoryStore" :columns="columns" title="Expense Categories" addButtonLabel="Add Category"
      :modalComponent="ExpenseCategoryFormModal" :selfSaving="true" @saved="handleSaved">
      <template #cell-type="{ item }">
        <span class="px-2 py-1 rounded-full text-xs" :class="{
          'bg-blue-100 text-blue-800': item.type === 'operational',
          'bg-green-100 text-green-800': item.type === 'salary',
          'bg-gray-100 text-gray-800': item.type === 'other',
        }">
          {{ item.type }}
        </span>
      </template>
      <template #cell-status="{ item }">
        <span :class="item.status ? 'text-green-600' : 'text-red-600'">
          {{ item.status ? 'Active' : 'Inactive' }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import DataTable from '@/components/common/DataTable.vue';
import ExpenseCategoryFormModal from '@/components/admin/ExpenseCategoryFormModal.vue';
import { useExpenseCategoryStore } from '@/stores/expenseCategoryStore';
import type { ColumnDefinition } from '@/types/table';

const categoryStore = useExpenseCategoryStore();

const columns: ColumnDefinition[] = [
  { key: 'name', label: 'Name' },
  { key: 'type', label: 'Type' },
  { key: 'description', label: 'Description' },
  { key: 'status', label: 'Status' },
];

const handleSaved = async () => {
  await categoryStore.fetchAll();
};

onMounted(() => {
  categoryStore.fetchItems(1);
});
</script>
