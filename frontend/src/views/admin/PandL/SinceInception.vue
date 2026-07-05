<template>
  <PageBreadcrumb :pageTitle="'P&L - Since Inception'" />
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

const data = ref<any>({});
const loading = ref(false);
const error = ref('');

async function fetchData() {
  loading.value = true;
  try {
    const res = await api.get('/admin/pandl/since-inception');
    data.value = res.data;
  } catch (e: any) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
