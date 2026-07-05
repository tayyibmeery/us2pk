<template>
  <PageBreadcrumb :pageTitle="'P&L - Monthly'" />
  <div class="mb-4 flex items-center gap-3">
    <div>
      <label class="text-sm font-medium">Year:</label>
      <select v-model="year" @change="fetchData" class="rounded border p-2 dark:bg-gray-800">
        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
      </select>
    </div>
    <div>
      <label class="text-sm font-medium">Month:</label>
      <select v-model="month" @change="fetchData" class="rounded border p-2 dark:bg-gray-800">
        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
      </select>
    </div>
  </div>
  <div v-if="loading" class="text-center py-10">Loading...</div>
  <div v-else-if="error" class="text-red-500">{{ error }}</div>
  <div v-else class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border p-4">
    <table class="min-w-full text-sm">
      <thead>
        <tr>
          <th class="text-left">Account</th>
          <th class="text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(value, key) in data" :key="key" v-if="typeof value === 'number'">
          <td class="font-semibold">{{ key.replace(/_/g, ' ').toUpperCase() }}</td>
          <td class="text-right" :class="value < 0 ? 'text-red-500' : 'text-green-600'">{{ value.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const year = ref(new Date().getFullYear());
const years = Array.from({ length: 10 }, (_, i) => year.value - i);
const month = ref(new Date().getMonth() + 1);
const months = [
  { value: 1, label: 'Jan' }, { value: 2, label: 'Feb' }, { value: 3, label: 'Mar' },
  { value: 4, label: 'Apr' }, { value: 5, label: 'May' }, { value: 6, label: 'Jun' },
  { value: 7, label: 'Jul' }, { value: 8, label: 'Aug' }, { value: 9, label: 'Sep' },
  { value: 10, label: 'Oct' }, { value: 11, label: 'Nov' }, { value: 12, label: 'Dec' }
];
const data = ref<any>({});
const loading = ref(false);
const error = ref('');

async function fetchData() {
  loading.value = true;
  try {
    const res = await api.get('/admin/pandl/monthly', { params: { year: year.value, month: month.value } });
    data.value = res.data;
  } catch (e: any) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
