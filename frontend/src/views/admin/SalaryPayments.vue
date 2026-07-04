<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Salary Payments'" />

    <!-- Filter by Employee -->
    <div
      class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <div>
        <label class="text-sm font-medium mr-2">Employee:</label>
        <select v-model="selectedEmployee" @change="onEmployeeFilterChange"
          class="rounded-lg border p-2 dark:bg-gray-800">
          <option :value="null">All</option>
          <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
        </select>
      </div>
      <button @click="clearFilter" class="text-sm text-brand-600">Clear</button>
    </div>

    <DataTable :store="salaryStore" :columns="columns" title="Salary Payments" addButtonLabel="Add Payment"
      :modalComponent="SalaryPaymentFormModal" :selfSaving="true" @saved="handleSaved">
      <template #cell-employee="{ item }">
        {{ item.employee?.name || 'N/A' }}
      </template>
      <template #cell-amount="{ item }">
        {{ formatCurrency(item.amount) }}
      </template>
      <template #cell-month="{ item }">
        {{ getMonthName(item.month) }}
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import DataTable from '@/components/common/DataTable.vue';
import SalaryPaymentFormModal from '@/components/admin/SalaryPaymentFormModal.vue';
import { useSalaryPaymentStore } from '@/stores/salaryPaymentStore';
import { useEmployeeStore } from '@/stores/employeeStore';
import type { ColumnDefinition } from '@/types/table';

const salaryStore = useSalaryPaymentStore();
const employeeStore = useEmployeeStore();

const employees = ref<any[]>([]);
const selectedEmployee = ref<number | null>(null);

const columns: ColumnDefinition[] = [
  { key: 'employee', label: 'Employee' },
  { key: 'month', label: 'Month' },
  { key: 'year', label: 'Year' },
  { key: 'amount', label: 'Amount' },
  { key: 'paid_date', label: 'Paid Date' },
  { key: 'notes', label: 'Notes' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(value || 0);
};

const getMonthName = (month: number) => {
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  return months[month - 1] || month;
};

const onEmployeeFilterChange = () => {
  salaryStore.setEmployeeFilter(selectedEmployee.value);
  salaryStore.fetchItems(1);
};

const clearFilter = () => {
  selectedEmployee.value = null;
  salaryStore.setEmployeeFilter(null);
  salaryStore.fetchItems(1);
};

const handleSaved = async () => {
  await salaryStore.fetchItems(salaryStore.pagination.current_page);
};

onMounted(async () => {
  await employeeStore.fetchAll();
  employees.value = employeeStore.items;
  salaryStore.fetchItems(1);
});
</script>
