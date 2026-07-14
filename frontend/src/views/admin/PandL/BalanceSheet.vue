<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Balance Sheet'" />

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
      <form @submit.prevent="fetchData" class="flex flex-wrap items-end gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Year</label>
          <select v-model="year" @change="updateDays"
            class="h-11 w-32 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Month</label>
          <select v-model="month" @change="updateDays"
            class="h-11 w-40 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Day</label>
          <select v-model="day" @change="fetchData"
            class="h-11 w-24 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
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

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="w-12 h-12 border-4 border-gray-200 dark:border-gray-700 border-t-brand-600 rounded-full animate-spin">
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error"
      class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
      <p class="text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Balance Sheet -->
    <div v-else-if="balanceSheet && balanceSheet.date" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Assets -->
      <div
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div
          class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-emerald-700 dark:text-emerald-400">📊 ASSETS</h3>
            <span
              class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ formatCurrency(balanceSheet.assets?.total || 0) }}</span>
          </div>
        </div>
        <div class="overflow-x-auto p-4">
          <table class="w-full text-sm">
            <tbody>
              <tr v-for="item in balanceSheet.assets?.items || []" :key="'asset-' + item.id"
                class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="py-2.5 px-2 text-gray-700 dark:text-gray-300">{{ item.name }}</td>
                <td class="py-2.5 px-2 text-right font-mono"
                  :class="item.balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                  {{ formatCurrency(item.balance) }}
                </td>
              </tr>
              <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50">
                <td class="py-3 px-2 font-bold text-gray-800 dark:text-white/90">Total Assets</td>
                <td class="py-3 px-2 text-right font-mono font-bold text-emerald-700 dark:text-emerald-400">
                  {{ formatCurrency(balanceSheet.assets?.total || 0) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Liabilities & Equity -->
      <div class="space-y-6">
        <!-- Liabilities -->
        <div
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
          <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-rose-50 to-rose-100 dark:from-rose-900/20 dark:to-rose-900/10">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-bold text-rose-700 dark:text-rose-400">📋 LIABILITIES</h3>
              <span
                class="text-xs font-medium text-rose-600 dark:text-rose-400">{{ formatCurrency(balanceSheet.liabilities?.total || 0) }}</span>
            </div>
          </div>
          <div class="overflow-x-auto p-4">
            <table class="w-full text-sm">
              <tbody>
                <tr v-for="item in balanceSheet.liabilities?.items || []" :key="'liab-' + item.id"
                  class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                  <td class="py-2.5 px-2 text-gray-700 dark:text-gray-300">{{ item.name }}</td>
                  <td class="py-2.5 px-2 text-right font-mono"
                    :class="item.balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                    {{ formatCurrency(item.balance) }}
                  </td>
                </tr>
                <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50">
                  <td class="py-3 px-2 font-bold text-gray-800 dark:text-white/90">Total Liabilities</td>
                  <td class="py-3 px-2 text-right font-mono font-bold text-rose-700 dark:text-rose-400">
                    {{ formatCurrency(balanceSheet.liabilities?.total || 0) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Equity -->
        <div
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
          <div
            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/10">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-bold text-blue-700 dark:text-blue-400">👥 EQUITY</h3>
              <span
                class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ formatCurrency(balanceSheet.equity?.total || 0) }}</span>
            </div>
          </div>
          <div class="overflow-x-auto p-4">
            <table class="w-full text-sm">
              <tbody>
                <tr v-for="item in balanceSheet.equity?.items || []" :key="'eq-' + item.id"
                  class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                  <td class="py-2.5 px-2 text-gray-700 dark:text-gray-300">{{ item.name }}</td>
                  <td class="py-2.5 px-2 text-right font-mono"
                    :class="item.balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                    {{ formatCurrency(item.balance) }}
                  </td>
                </tr>
                <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50">
                  <td class="py-3 px-2 font-bold text-gray-800 dark:text-white/90">Total Equity</td>
                  <td class="py-3 px-2 text-right font-mono font-bold text-blue-700 dark:text-blue-400">
                    {{ formatCurrency(balanceSheet.equity?.total || 0) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Summary -->
      <div
        class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div
          class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">📐 Accounting Equation Summary</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">As of
            {{ balanceSheet.period?.formatted || balanceSheet.date || 'N/A' }}</p>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div
              class="text-center p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/30">
              <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assets</p>
              <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                {{ formatCurrency(balanceSheet.summary?.assets || 0) }}</p>
            </div>
            <div
              class="text-center p-4 rounded-lg bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800/30">
              <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Liabilities</p>
              <p class="text-xl font-bold text-rose-600 dark:text-rose-400">
                {{ formatCurrency(balanceSheet.summary?.liabilities || 0) }}</p>
            </div>
            <div
              class="text-center p-4 rounded-lg bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30">
              <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Equity</p>
              <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                {{ formatCurrency(balanceSheet.summary?.equity || 0) }}</p>
            </div>
            <div class="text-center p-4 rounded-lg"
              :class="balanceSheet.equation_balanced ? 'bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/30' : 'bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30'">
              <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Liabilities + Equity</p>
              <p class="text-xl font-bold"
                :class="balanceSheet.equation_balanced ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                {{ formatCurrency(balanceSheet.summary?.total || 0) }}
              </p>
            </div>
          </div>

          <!-- Balance Status -->
          <div class="mt-6 text-center">
            <div v-if="balanceSheet.equation_balanced"
              class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
              <span class="text-2xl">✅</span>
              <span class="font-bold">Balance Sheet is Balanced!</span>
              <span class="text-sm font-normal opacity-75">Assets = Liabilities + Equity</span>
            </div>
            <div v-else
              class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
              <span class="text-2xl">⚠️</span>
              <span class="font-bold">Balance Sheet is NOT Balanced!</span>
              <span class="text-sm font-normal opacity-75">Difference:
                {{ formatCurrency(balanceSheet.difference) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && !error"
      class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
      <div class="text-6xl mb-4">📊</div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">No Balance Sheet Data</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select a date and click Refresh to view the balance
        sheet.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const year = ref(new Date().getFullYear());
const month = ref(new Date().getMonth() + 1);
const day = ref(new Date().getDate());

const years = Array.from({ length: 10 }, (_, i) => year.value - i);
const months = [
  { value: 1, label: 'January' }, { value: 2, label: 'February' }, { value: 3, label: 'March' },
  { value: 4, label: 'April' }, { value: 5, label: 'May' }, { value: 6, label: 'June' },
  { value: 7, label: 'July' }, { value: 8, label: 'August' }, { value: 9, label: 'September' },
  { value: 10, label: 'October' }, { value: 11, label: 'November' }, { value: 12, label: 'December' }
];
const days = ref<number[]>([]);

const balanceSheet = ref<any>({});
const loading = ref(false);
const error = ref('');

const formatCurrency = (value: number) => {
  if (value === undefined || value === null) return 'PKR 0.00';
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
  }).format(value);
};

function updateDays() {
  const daysInMonth = new Date(year.value, month.value, 0).getDate();
  days.value = Array.from({ length: daysInMonth }, (_, i) => i + 1);
  if (day.value > daysInMonth) {
    day.value = daysInMonth;
  }
  fetchData();
}

async function fetchData() {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get('/admin/pandl/balance-sheet', {
      params: {
        year: year.value,
        month: month.value,
        day: day.value
      }
    });
    balanceSheet.value = res.data;
  } catch (e: any) {
    error.value = e.message || 'Failed to fetch balance sheet';
  } finally {
    loading.value = false;
  }
}

watch([year, month], () => {
  updateDays();
});

onMounted(() => {
  updateDays();
});
</script>
