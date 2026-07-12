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
        <!-- Display main P&L items -->
        <tr v-for="(value, key) in flatData" :key="key">
          <td class="font-semibold">{{ formatKey(key) }}</td>
          <td class="text-right" :class="value < 0 ? 'text-red-500' : 'text-green-600'">
            {{ value.toFixed(2) }}
          </td>
        </tr>

        <!-- Display allocations if they exist -->
        <tr v-if="data.allocations && Object.keys(data.allocations).length > 0">
          <td colspan="2" class="font-bold pt-4">ALLOCATIONS</td>
        </tr>
        <tr v-for="(value, key) in data.allocations" :key="'alloc-' + key" v-if="data.allocations">
          <td class="pl-4">{{ key }} Allocation</td>
          <td class="text-right" :class="value < 0 ? 'text-red-500' : 'text-green-600'">
            {{ value.toFixed(2) }}
          </td>
        </tr>

        <!-- Display grouped details -->
        <template v-for="(group, groupName) in data.details" :key="groupName">
          <tr v-if="group.details && Object.keys(group.details).length > 0">
            <td colspan="2" class="font-bold pt-4">{{ formatKey(groupName) }}</td>
          </tr>
          <tr v-for="(value, key) in group.details" :key="groupName + '-' + key">
            <td class="pl-4">{{ formatKey(key) }}</td>
            <td class="text-right" :class="value < 0 ? 'text-red-500' : 'text-green-600'">
              {{ value.toFixed(2) }}
            </td>
          </tr>
          <tr v-if="group.details && Object.keys(group.details).length > 0">
            <td class="font-bold pl-4">Total {{ formatKey(groupName) }}</td>
            <td class="text-right font-bold" :class="group.total < 0 ? 'text-red-500' : 'text-green-600'">
              {{ group.total.toFixed(2) }}
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

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
