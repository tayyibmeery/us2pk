<template>
  <PageBreadcrumb :pageTitle="'General Journal'" />

  <div class="space-y-6">
    <!-- Header Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Vouchers</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">{{ pagination?.total || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6M9 8h6M5 3.75h14A1.25 1.25 0 0 1 20.25 5v14A1.25 1.25 0 0 1 19 20.25H5A1.25 1.25 0 0 1 3.75 19V5A1.25 1.25 0 0 1 5 3.75Z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Debit</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
              {{ formatCurrency(totalDebit) }}
            </p>
          </div>
          <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-9h3m6 0h-3m-6 9h3m6 0h-3" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Credit</p>
            <p class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">
              {{ formatCurrency(totalCredit) }}
            </p>
          </div>
          <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center">
            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-9h3m6 0h-3m-6 9h3m6 0h-3" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Balanced Vouchers</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-1">{{ balancedCount }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="space-y-4">
      <div v-for="n in 3" :key="n"
        class="animate-pulse rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="h-5 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
          <div class="h-5 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
        </div>
        <div class="space-y-2">
          <div class="h-4 w-full bg-gray-100 dark:bg-gray-700/60 rounded"></div>
          <div class="h-4 w-3/4 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!journal.length"
      class="flex flex-col items-center justify-center text-center py-16 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
      <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
          stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12h6m-6 4h6M9 8h6M5 3.75h14A1.25 1.25 0 0 1 20.25 5v14A1.25 1.25 0 0 1 19 20.25H5A1.25 1.25 0 0 1 3.75 19V5A1.25 1.25 0 0 1 5 3.75Z" />
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">No Journal Entries</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
        Posted and approved vouchers will appear here once they're recorded in the system.
      </p>
    </div>

    <!-- Journal entries -->
    <div v-else class="space-y-4">
      <article v-for="(entry, idx) in journal" :key="idx"
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">

        <!-- Card header -->
        <div
          class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100 dark:border-gray-700"
          :class="entry.balanced === 'Balanced' ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : 'bg-rose-50/50 dark:bg-rose-900/10'">

          <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 tabular-nums">
              {{ entry.date }}
            </span>

            <router-link :to="`/admin/vouchers/${entry.voucher_no}`"
              class="text-base font-semibold text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 transition-colors">
              {{ entry.voucher_no }}
            </router-link>

            <span v-if="entry.shipment_code"
              class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-full px-3 py-1">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M20.25 8.25 12 3 3.75 8.25m16.5 0v7.5L12 21m8.25-12.75L12 12.75m0 8.25L3.75 15.75v-7.5M12 12.75 3.75 8.25" />
              </svg>
              {{ entry.shipment_code }}
            </span>

            <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1">
              {{ entry.source }}
            </span>
          </div>

          <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1.5 rounded-full" :class="entry.balanced === 'Balanced'
              ? 'text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/30'
              : 'text-rose-700 dark:text-rose-400 bg-rose-100 dark:bg-rose-900/30'">
              <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
              {{ entry.balanced }}
            </span>
          </div>
        </div>

        <!-- Description -->
        <div v-if="entry.description"
          class="px-6 py-3 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
          <p class="text-sm text-gray-600 dark:text-gray-300 italic flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ entry.description }}
          </p>
        </div>

        <!-- Detail table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Account</th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Debit</th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Credit</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr v-for="(detail, dIdx) in entry.details" :key="dIdx"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                <td class="px-6 py-3 text-gray-700 dark:text-gray-200">
                  <span class="font-medium">{{ detail.account_name }}</span>
                </td>
                <td class="px-6 py-3 text-right font-mono tabular-nums text-gray-700 dark:text-gray-200">
                  {{ detail.debit ? Number(detail.debit).toFixed(2) : '—' }}
                </td>
                <td class="px-6 py-3 text-right font-mono tabular-nums text-gray-700 dark:text-gray-200">
                  {{ detail.credit ? Number(detail.credit).toFixed(2) : '—' }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800/70 border-t-2 border-gray-200 dark:border-gray-700">
              <tr>
                <td class="px-6 py-3 font-semibold text-gray-800 dark:text-white/90">Total</td>
                <td
                  class="px-6 py-3 text-right font-mono font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                  {{ Number(entry.total_debit || 0).toFixed(2) }}
                </td>
                <td class="px-6 py-3 text-right font-mono font-bold tabular-nums text-rose-600 dark:text-rose-400">
                  {{ Number(entry.total_credit || 0).toFixed(2) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </article>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      <Pagination v-if="pagination" :pagination="pagination" @pageChange="fetchJournal" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import Pagination from '@/components/common/Pagination.vue';

const journal = ref<any[]>([]);
const pagination = ref<any>(null);
const loading = ref(false);

const totalDebit = computed(() => {
  return journal.value.reduce((sum, entry) => sum + (Number(entry.total_debit) || 0), 0);
});

const totalCredit = computed(() => {
  return journal.value.reduce((sum, entry) => sum + (Number(entry.total_credit) || 0), 0);
});

const balancedCount = computed(() => {
  return journal.value.filter(entry => entry.balanced === 'Balanced').length;
});

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
  }).format(value || 0);
};

async function fetchJournal(page = 1) {
  loading.value = true;
  try {
    const res = await api.get('/admin/journal', { params: { page } });
    journal.value = res.data.data;
    pagination.value = res.data.pagination;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

onMounted(() => fetchJournal());
</script>
