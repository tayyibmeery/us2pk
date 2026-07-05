<template>
  <PageBreadcrumb :pageTitle="'General Journal'" />
  <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-700">
          <tr>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Voucher #</th>
            <th class="px-4 py-2">Shipment</th>
            <th class="px-4 py-2">Source</th>
            <th class="px-4 py-2">Account</th>
            <th class="px-4 py-2 text-right">Debit</th>
            <th class="px-4 py-2 text-right">Credit</th>
            <th class="px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(entry, idx) in journal" :key="idx" class="border-t">
            <td class="px-4 py-2">{{ entry.date }}</td>
            <td class="px-4 py-2"><router-link :to="`/admin/vouchers/${entry.voucher_no}`"
                class="text-brand-500 hover:underline">{{ entry.voucher_no }}</router-link></td>
            <td class="px-4 py-2">{{ entry.shipment_code || '—' }}</td>
            <td class="px-4 py-2">{{ entry.source }}</td>
            <td class="px-4 py-2">{{ entry.account_name }}</td>
            <td class="px-4 py-2 text-right">{{ entry.debit }}</td>
            <td class="px-4 py-2 text-right">{{ entry.credit }}</td>
            <td class="px-4 py-2">{{ entry.balanced }}</td>
          </tr>
          <tr v-if="!journal.length">
            <td colspan="8" class="text-center py-4 text-gray-400">No entries found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <Pagination v-if="pagination" :pagination="pagination" @pageChange="fetchJournal" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import Pagination from '@/components/common/Pagination.vue';

const journal = ref<any[]>([]);
const pagination = ref<any>(null);

async function fetchJournal(page = 1) {
  try {
    const res = await api.get('/admin/journal', { params: { page } });
    journal.value = res.data.data;
    pagination.value = res.data.pagination;
  } catch (e) {
    console.error(e);
  }
}

onMounted(() => fetchJournal());
</script>
