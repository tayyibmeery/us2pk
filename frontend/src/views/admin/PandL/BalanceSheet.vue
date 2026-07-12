<template>
  <PageBreadcrumb :pageTitle="'Balance Sheet'" />

  <div class="mb-4 flex items-center gap-3 flex-wrap">
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
    <div>
      <label class="text-sm font-medium">Day:</label>
      <select v-model="day" @change="fetchData" class="rounded border p-2 dark:bg-gray-800">
        <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
      </select>
    </div>
    <button @click="fetchData" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
      Refresh
    </button>
  </div>

  <div v-if="loading" class="text-center py-10">Loading...</div>
  <div v-else-if="error" class="text-red-500">{{ error }}</div>
  <div v-else-if="!balanceSheet.date" class="text-center py-10">No data available</div>

  <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Assets Column -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
      <h3 class="text-lg font-bold mb-4 text-green-600">ASSETS</h3>
      <table class="w-full text-sm">
        <tbody>
          <tr v-for="item in balanceSheet.assets.items" :key="item.id">
            <td class="py-1">{{ item.name }}</td>
            <td class="text-right font-mono" :class="item.balance < 0 ? 'text-red-500' : 'text-green-600'">
              {{ item.balance.toFixed(2) }}
            </td>
          </tr>
          <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-bold">
            <td class="py-2">Total Assets</td>
            <td class="text-right font-mono text-green-600">
              {{ balanceSheet.assets.total.toFixed(2) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Liabilities & Equity Column -->
    <div class="space-y-6">
      <!-- Liabilities -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <h3 class="text-lg font-bold mb-4 text-red-600">LIABILITIES</h3>
        <table class="w-full text-sm">
          <tbody>
            <tr v-for="item in balanceSheet.liabilities.items" :key="item.id">
              <td class="py-1">{{ item.name }}</td>
              <td class="text-right font-mono" :class="item.balance < 0 ? 'text-red-500' : 'text-green-600'">
                {{ item.balance.toFixed(2) }}
              </td>
            </tr>
            <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-bold">
              <td class="py-2">Total Liabilities</td>
              <td class="text-right font-mono text-red-600">
                {{ balanceSheet.liabilities.total.toFixed(2) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Equity -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <h3 class="text-lg font-bold mb-4 text-blue-600">EQUITY</h3>
        <table class="w-full text-sm">
          <tbody>
            <tr v-for="item in balanceSheet.equity.items" :key="item.id">
              <td class="py-1">{{ item.name }}</td>
              <td class="text-right font-mono" :class="item.balance < 0 ? 'text-red-500' : 'text-green-600'">
                {{ item.balance.toFixed(2) }}
              </td>
            </tr>
            <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-bold">
              <td class="py-2">Total Equity</td>
              <td class="text-right font-mono text-blue-600">
                {{ balanceSheet.equity.total.toFixed(2) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Summary -->
      <div class="bg-gray-100 dark:bg-gray-700 rounded-xl border p-4">
        <div class="grid grid-cols-2 gap-2 text-sm">
          <div class="font-semibold">Total Assets:</div>
          <div class="text-right font-mono text-green-600">{{ balanceSheet.assets.total.toFixed(2) }}</div>

          <div class="font-semibold">Total Liabilities:</div>
          <div class="text-right font-mono text-red-600">{{ balanceSheet.liabilities.total.toFixed(2) }}</div>

          <div class="font-semibold">Total Equity:</div>
          <div class="text-right font-mono text-blue-600">{{ balanceSheet.equity.total.toFixed(2) }}</div>

          <div class="font-semibold border-t pt-2">Liabilities + Equity:</div>
          <div class="text-right font-mono border-t pt-2">{{ balanceSheet.total_liabilities_equity.toFixed(2) }}</div>

          <div class="font-bold text-lg border-t pt-2">Difference:</div>
          <div class="text-right font-mono border-t pt-2"
            :class="balanceSheet.difference === 0 ? 'text-green-600' : 'text-red-600'">
            {{ balanceSheet.difference.toFixed(2) }}
          </div>

          <div class="col-span-2 text-center mt-2">
            <span v-if="balanceSheet.equation_balanced" class="text-green-600 font-bold">
              ✅ Balance Sheet Balanced!
            </span>
            <span v-else class="text-red-600 font-bold">
              ⚠️ Balance Sheet NOT Balanced! Difference: {{ balanceSheet.difference.toFixed(2) }}
            </span>
          </div>
        </div>
        <div class="text-xs text-gray-500 mt-2 text-center">
          As of {{ balanceSheet.date }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const year = ref(new Date().getFullYear());
const month = ref(new Date().getMonth() + 1);
const day = ref(new Date().getDate());

const years = Array.from({ length: 10 }, (_, i) => year.value - i);
const months = [
  { value: 1, label: 'Jan' }, { value: 2, label: 'Feb' }, { value: 3, label: 'Mar' },
  { value: 4, label: 'Apr' }, { value: 5, label: 'May' }, { value: 6, label: 'Jun' },
  { value: 7, label: 'Jul' }, { value: 8, label: 'Aug' }, { value: 9, label: 'Sep' },
  { value: 10, label: 'Oct' }, { value: 11, label: 'Nov' }, { value: 12, label: 'Dec' }
];

const days = ref<number[]>([]);

// Generate days for the selected month/year
function generateDays() {
  const daysInMonth = new Date(year.value, month.value, 0).getDate();
  days.value = Array.from({ length: daysInMonth }, (_, i) => i + 1);
  // Ensure day is valid for the month
  if (day.value > daysInMonth) {
    day.value = daysInMonth;
  }
}

const balanceSheet = ref<any>({});
const loading = ref(false);
const error = ref('');

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
    console.error('Balance sheet error:', e);
  } finally {
    loading.value = false;
  }
}

// Watch for month/year changes to update days
import { watch } from 'vue';
watch([year, month], () => {
  generateDays();
  fetchData();
});

onMounted(() => {
  generateDays();
  fetchData();
});
</script>

<style scoped>
/* Optional styles */
table {
  border-collapse: collapse;
}

td {
  padding: 4px 8px;
}
</style>
