<template>
  <PageBreadcrumb :pageTitle="'P&L - Yearly'" />
  <div class="mb-4 flex items-center gap-3">
    <label class="text-sm font-medium">Year:</label>
    <select v-model="year" @change="fetchData" class="rounded border p-2 dark:bg-gray-800">
      <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
    </select>
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
        <!-- Show only the main P&L numbers -->
        <tr v-for="(value, key) in flatData" :key="key">
          <td class="font-semibold">{{ formatKey(key) }}</td>
          <td class="text-right" :class="value < 0 ? 'text-red-500' : 'text-green-600'">
            {{ value.toFixed(2) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const year = ref(new Date().getFullYear());
const years = Array.from({ length: 10 }, (_, i) => year.value - i);
const data = ref<any>({});
const loading = ref(false);
const error = ref('');

// Computed property to filter only number fields from top level
const flatData = computed(() => {
  if (!data.value) return {};
  const result: Record<string, number> = {};
  const excludeKeys = ['allocations', 'details'];

  for (const [key, value] of Object.entries(data.value)) {
    if (typeof value === 'number' && !excludeKeys.includes(key)) {
      result[key] = value;
    }
  }
  return result;
});

function formatKey(key: string): string {
  return key.replace(/_/g, ' ').toUpperCase();
}

async function fetchData() {
  loading.value = true;
  try {
    const res = await api.get('/admin/pandl/yearly', { params: { year: year.value } });
    data.value = res.data;
  } catch (e: any) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
