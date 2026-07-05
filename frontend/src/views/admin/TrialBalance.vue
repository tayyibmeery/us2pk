<template>
  <PageBreadcrumb :pageTitle="'Trial Balance'" />
  <div class="mb-4 flex items-center gap-3">
    <label class="text-sm font-medium">Date:</label>
    <input type="date" v-model="date" class="rounded border p-2 dark:bg-gray-800" />
    <button @click="fetchData" class="bg-brand-500 text-white px-4 py-2 rounded">Go</button>
  </div>
  <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-700">
          <tr>
            <th class="px-4 py-2 text-left">Account</th>
            <th class="px-4 py-2 text-right">Debit</th>
            <th class="px-4 py-2 text-right">Credit</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in data" :key="row.account_name" class="border-t">
            <td class="px-4 py-2">{{ row.account_name }}</td>
            <td class="px-4 py-2 text-right">{{ row.debit }}</td>
            <td class="px-4 py-2 text-right">{{ row.credit }}</td>
          </tr>
          <tr v-if="!data.length">
            <td colspan="3" class="text-center py-4 text-gray-400">No data.</td>
          </tr>
        </tbody>
        <tfoot class="font-bold bg-gray-50 dark:bg-gray-800">
          <tr>
            <td class="px-4 py-2">Total</td>
            <td class="px-4 py-2 text-right">{{ totalDebit }}</td>
            <td class="px-4 py-2 text-right">{{ totalCredit }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const date = ref(new Date().toISOString().split('T')[0]);
const data = ref<any[]>([]);
const totalDebit = ref(0);
const totalCredit = ref(0);

async function fetchData() {
  try {
    const res = await api.get('/admin/trial-balance', { params: { date: date.value } });
    data.value = res.data.data;
    totalDebit.value = res.data.total_debit;
    totalCredit.value = res.data.total_credit;
  } catch (e) {
    console.error(e);
  }
}

onMounted(fetchData);
</script>
