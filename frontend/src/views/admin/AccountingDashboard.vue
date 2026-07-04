<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Accounting Dashboard'" />

    <!-- Year & Month Selector -->
    <div
      class="flex flex-wrap items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <div>
        <label class="text-sm font-medium mr-2">Year:</label>
        <select v-model="selectedYear" @change="fetchData" class="rounded-lg border p-2 dark:bg-gray-800">
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
      </div>
      <div>
        <label class="text-sm font-medium mr-2">Month:</label>
        <select v-model="selectedMonth" @change="fetchData" class="rounded-lg border p-2 dark:bg-gray-800">
          <option :value="null">All Year</option>
          <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
        </select>
      </div>
      <button @click="fetchData" class="bg-brand-500 text-white px-4 py-2 rounded-lg">Refresh</button>
    </div>

    <!-- Summary Cards -->
    <div v-if="summary" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm">
        <p class="text-sm text-gray-500">Income</p>
        <p class="text-2xl font-bold text-green-600">{{ formatCurrency(summary.income) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm">
        <p class="text-sm text-gray-500">Expenses</p>
        <p class="text-2xl font-bold text-red-600">{{ formatCurrency(summary.expenses.total) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm">
        <p class="text-sm text-gray-500">Profit / Loss</p>
        <p class="text-2xl font-bold" :class="summary.profit >= 0 ? 'text-green-600' : 'text-red-600'">
          {{ formatCurrency(summary.profit) }}
        </p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm">
        <p class="text-sm text-gray-500">Period</p>
        <p class="text-sm font-medium">{{ summary.period.start }} to {{ summary.period.end }}</p>
      </div>
    </div>

    <!-- Expense Breakdown -->
    <div v-if="summary" class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm">
      <h3 class="text-lg font-semibold mb-3">Expense Breakdown</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div><span class="text-sm text-gray-500">Consolidation:</span>
          {{ formatCurrency(summary.expenses.consolidation) }}</div>
        <div><span class="text-sm text-gray-500">Manual:</span> {{ formatCurrency(summary.expenses.manual) }}</div>
        <div><span class="text-sm text-gray-500">Salaries:</span> {{ formatCurrency(summary.expenses.salary) }}</div>
        <div><span class="text-sm text-gray-500 font-medium">Total:</span> {{ formatCurrency(summary.expenses.total) }}
        </div>
      </div>
    </div>

    <!-- Monthly Chart / Table -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border shadow-sm overflow-x-auto">
      <h3 class="text-lg font-semibold mb-3">Monthly Breakdown ({{ selectedYear }})</h3>
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-3 py-2 text-left">Month</th>
            <th class="px-3 py-2 text-right">Income</th>
            <th class="px-3 py-2 text-right">Expenses</th>
            <th class="px-3 py-2 text-right">Profit</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="m in monthlyData" :key="m.month" class="border-b dark:border-gray-700">
            <td class="px-3 py-2">{{ m.month_name }}</td>
            <td class="px-3 py-2 text-right">{{ formatCurrency(m.income) }}</td>
            <td class="px-3 py-2 text-right">{{ formatCurrency(m.expenses.total) }}</td>
            <td class="px-3 py-2 text-right" :class="m.profit >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(m.profit) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const selectedYear = ref(new Date().getFullYear());
const selectedMonth = ref<number | null>(null);
const summary = ref<any>(null);
const monthlyData = ref<any[]>([]);

const years = computed(() => {
  const current = new Date().getFullYear();
  return Array.from({ length: 10 }, (_, i) => current - i);
});

const months = [
  { value: 1, label: 'January' },
  { value: 2, label: 'February' },
  { value: 3, label: 'March' },
  { value: 4, label: 'April' },
  { value: 5, label: 'May' },
  { value: 6, label: 'June' },
  { value: 7, label: 'July' },
  { value: 8, label: 'August' },
  { value: 9, label: 'September' },
  { value: 10, label: 'October' },
  { value: 11, label: 'November' },
  { value: 12, label: 'December' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(value || 0);
};

async function fetchData() {
  try {
    const res = await api.get('/admin/accounting/summary', {
      params: { year: selectedYear.value, month: selectedMonth.value }
    });
    summary.value = res.data;
  } catch (err) {
    console.error('Failed to fetch summary', err);
  }
  try {
    const res = await api.get('/admin/accounting/monthly-breakdown', {
      params: { year: selectedYear.value }
    });
    monthlyData.value = res.data;
  } catch (err) {
    console.error('Failed to fetch monthly breakdown', err);
  }
}

onMounted(fetchData);
</script>
