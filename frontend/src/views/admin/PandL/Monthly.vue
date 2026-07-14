<template>
  <div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
      <form @submit.prevent="fetchData" class="flex flex-wrap items-end gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Year</label>
          <select v-model="year" @change="fetchData"
            class="h-11 w-40 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Month</label>
          <select v-model="month" @change="fetchData"
            class="h-11 w-44 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
          </select>
        </div>
        <button type="submit"
          class="h-11 rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
          <svg class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Refresh
        </button>
      </form>
    </div>

    <ProfitLossTemplate :pageTitle="'P&L - Monthly'" :periodText="`${monthName} ${year}`"
      :periodRange="`${monthName} 1 → ${monthName} ${daysInMonth}, ${year}`" :data="data" :loading="loading"
      :error="error" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import api from '@/services/api';
import ProfitLossTemplate from './ProfitLossTemplate.vue';

const year = ref(new Date().getFullYear());
const years = Array.from({ length: 10 }, (_, i) => year.value - i);
const month = ref(new Date().getMonth() + 1);

const months = [
  { value: 1, label: 'January' }, { value: 2, label: 'February' }, { value: 3, label: 'March' },
  { value: 4, label: 'April' }, { value: 5, label: 'May' }, { value: 6, label: 'June' },
  { value: 7, label: 'July' }, { value: 8, label: 'August' }, { value: 9, label: 'September' },
  { value: 10, label: 'October' }, { value: 11, label: 'November' }, { value: 12, label: 'December' }
];

const monthName = computed(() => {
  return months.find(m => m.value === month.value)?.label || '';
});

const daysInMonth = computed(() => {
  return new Date(year.value, month.value, 0).getDate();
});

const data = ref<any>({});
const loading = ref(false);
const error = ref('');

async function fetchData() {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get('/admin/pandl/monthly', {
      params: { year: year.value, month: month.value }
    });
    data.value = res.data;
  } catch (e: any) {
    error.value = e.message || 'Failed to fetch P&L data';
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
