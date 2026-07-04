<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Expenses'" />

    <DataTable :store="expenseStore" :columns="columns" title="All Expenses" addButtonLabel="Add Expense"
      :modalComponent="ExpenseFormModal" :selfSaving="true" @saved="handleSaved">
      <template #cell-category="{ item }">
        {{ item.category?.name || 'Uncategorized' }}
      </template>
      <template #cell-amount="{ item }">
        {{ formatCurrency(item.amount) }}
      </template>
      <template #cell-date="{ item }">
        {{ item.date }}
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import DataTable from '@/components/common/DataTable.vue';
import ExpenseFormModal from '@/components/admin/ExpenseFormModal.vue';
import { useExpenseStore } from '@/stores/expenseStore';
import type { ColumnDefinition } from '@/types/table';

const expenseStore = useExpenseStore();

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'category', label: 'Category' },
  { key: 'amount', label: 'Amount' },
  { key: 'date', label: 'Date' },
  { key: 'description', label: 'Description' },
  { key: 'reference', label: 'Reference' },
  { key: 'creator', label: 'Created By', format: (v: any) => v?.name || '—' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(value || 0);
};

const handleSaved = async () => {
  await expenseStore.fetchItems(expenseStore.pagination.current_page);
};

onMounted(() => {
  expenseStore.fetchItems(1);
});
</script>
