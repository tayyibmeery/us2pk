<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Employees'" />

    <DataTable :store="employeeStore" :columns="columns" title="Employees" addButtonLabel="Add Employee"
      :modalComponent="EmployeeFormModal" :selfSaving="true" @saved="handleSaved">
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
import EmployeeFormModal from '@/components/admin/EmployeeFormModal.vue';
import { useEmployeeStore } from '@/stores/employeeStore';
import type { ColumnDefinition } from '@/types/table';

const employeeStore = useEmployeeStore();

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'position', label: 'Position' },
  { key: 'monthly_salary', label: 'Monthly Salary', format: (v) => formatCurrency(v) },
  { key: 'joining_date', label: 'Joining Date' },
  { key: 'status', label: 'Status' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(value || 0);
};

const handleSaved = async () => {
  await employeeStore.fetchAll();
};

onMounted(() => {
  employeeStore.fetchItems(1);
});
</script>
