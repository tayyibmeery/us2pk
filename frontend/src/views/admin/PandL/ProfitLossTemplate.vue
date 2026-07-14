<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="pageTitle" />

    <!-- Header Info -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
      <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800 dark:text-white/90 flex items-center gap-2">
            <span class="text-2xl">📈</span> Profit & Loss Statement
          </h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ periodText }}</p>
        </div>
        <div class="text-right">
          <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Period</span>
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ periodRange }}</p>
        </div>
      </div>
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

    <!-- Empty State -->
    <div v-else-if="!data || !Object.keys(data).length"
      class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
      <div class="text-6xl mb-4">📊</div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">No Data Available</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No transactions found for this period.</p>
    </div>

    <!-- Profit & Loss Statement -->
    <div v-else
      class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
      <!-- Summary Cards -->
      <div
        class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
        <div
          class="text-center p-3 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Revenue</p>
          <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(data.revenue) }}</p>
        </div>
        <div
          class="text-center p-3 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gross Profit</p>
          <p class="text-xl font-bold"
            :class="data.gross_profit >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400'">
            {{ formatCurrency(data.gross_profit) }}
          </p>
        </div>
        <div
          class="text-center p-3 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</p>
          <p class="text-xl font-bold"
            :class="data.net_profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
            {{ formatCurrency(data.net_profit) }}
          </p>
        </div>
        <div
          class="text-center p-3 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Margin</p>
          <p class="text-xl font-bold"
            :class="data.net_profit_margin >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
            {{ data.net_profit_margin?.toFixed(1) ?? '0.0' }}%
          </p>
        </div>
      </div>

      <!-- P&L Table -->
      <div class="overflow-x-auto p-6">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
              <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Account</th>
              <th class="text-right py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Amount</th>
            </tr>
          </thead>
          <tbody>
            <!-- Revenue -->
            <template v-if="data.details?.Revenue">
              <tr>
                <td colspan="2" class="pt-4 pb-2 text-sm font-bold text-emerald-600 dark:text-emerald-400">
                  {{ data.details.Revenue.icon || '📈' }} Revenue
                </td>
              </tr>
              <tr v-for="(value, key) in data.details.Revenue.details || {}" :key="'revenue-' + key">
                <td class="pl-6 py-2 text-gray-600 dark:text-gray-300">{{ key }}</td>
                <td class="text-right font-mono text-emerald-600 dark:text-emerald-400">
                  {{ formatCurrency(value.amount ?? value) }}</td>
              </tr>
              <tr v-if="Object.keys(data.details.Revenue.details || {}).length">
                <td class="pl-6 py-2 font-semibold border-t border-gray-200 dark:border-gray-700">Total Revenue</td>
                <td
                  class="text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 border-t border-gray-200 dark:border-gray-700">
                  {{ formatCurrency(data.revenue) }}
                </td>
              </tr>
            </template>

            <!-- Cost of Sales -->
            <template v-if="data.details?.['Cost of Sales']">
              <tr>
                <td colspan="2" class="pt-4 pb-2 text-sm font-bold text-orange-600 dark:text-orange-400">
                  {{ data.details['Cost of Sales'].icon || '📦' }} Cost of Sales
                </td>
              </tr>
              <tr v-for="(value, key) in data.details['Cost of Sales'].details || {}" :key="'cos-' + key">
                <td class="pl-6 py-2 text-gray-600 dark:text-gray-300">{{ key }}</td>
                <td class="text-right font-mono text-orange-600 dark:text-orange-400">
                  {{ formatCurrency(value.amount ?? value) }}</td>
              </tr>
              <tr v-if="Object.keys(data.details['Cost of Sales'].details || {}).length">
                <td class="pl-6 py-2 font-semibold border-t border-gray-200 dark:border-gray-700">Total Cost of Sales
                </td>
                <td
                  class="text-right font-mono font-bold text-orange-600 dark:text-orange-400 border-t border-gray-200 dark:border-gray-700">
                  {{ formatCurrency(data.cost_of_sales) }}
                </td>
              </tr>
            </template>

            <!-- Gross Profit -->
            <tr v-if="data.gross_profit !== undefined">
              <td colspan="2" class="pt-4"></td>
            </tr>
            <tr class="bg-blue-50 dark:bg-blue-900/10">
              <td class="pl-6 py-3 font-bold text-blue-700 dark:text-blue-400">Gross Profit</td>
              <td class="text-right font-mono font-bold text-blue-700 dark:text-blue-400">
                {{ formatCurrency(data.gross_profit) }}
              </td>
            </tr>
            <tr v-if="data.gross_profit_margin !== undefined">
              <td class="pl-8 py-1 text-xs text-gray-400">Gross Profit Margin:
                {{ data.gross_profit_margin?.toFixed(1) ?? '0.0' }}%
              </td>
              <td></td>
            </tr>

            <!-- Operating Expenses -->
            <template v-if="data.details?.['Operating Expenses']">
              <tr>
                <td colspan="2" class="pt-4 pb-2 text-sm font-bold text-blue-600 dark:text-blue-400">
                  {{ data.details['Operating Expenses'].icon || '💼' }} Operating Expenses
                </td>
              </tr>
              <tr v-for="(value, key) in data.details['Operating Expenses'].details || {}" :key="'oe-' + key">
                <td class="pl-6 py-2 text-gray-600 dark:text-gray-300">{{ key }}</td>
                <td class="text-right font-mono text-blue-600 dark:text-blue-400">
                  {{ formatCurrency(value.amount ?? value) }}</td>
              </tr>
              <tr v-if="Object.keys(data.details['Operating Expenses'].details || {}).length">
                <td class="pl-6 py-2 font-semibold border-t border-gray-200 dark:border-gray-700">Total Operating
                  Expenses</td>
                <td
                  class="text-right font-mono font-bold text-blue-600 dark:text-blue-400 border-t border-gray-200 dark:border-gray-700">
                  {{ formatCurrency(data.operating_expenses) }}
                </td>
              </tr>
            </template>

            <!-- Operating Profit -->
            <tr v-if="data.operating_profit !== undefined">
              <td colspan="2" class="pt-4"></td>
            </tr>
            <tr class="bg-indigo-50 dark:bg-indigo-900/10">
              <td class="pl-6 py-3 font-bold text-indigo-700 dark:text-indigo-400">Operating Profit</td>
              <td class="text-right font-mono font-bold text-indigo-700 dark:text-indigo-400">
                {{ formatCurrency(data.operating_profit) }}
              </td>
            </tr>

            <!-- Other Project Expenses -->
            <template v-if="data.details?.['Other Project Expenses']">
              <tr>
                <td colspan="2" class="pt-4 pb-2 text-sm font-bold text-purple-600 dark:text-purple-400">
                  {{ data.details['Other Project Expenses'].icon || '📋' }} Other Project Expenses
                </td>
              </tr>
              <tr v-for="(value, key) in data.details['Other Project Expenses'].details || {}" :key="'ope-' + key">
                <td class="pl-6 py-2 text-gray-600 dark:text-gray-300">{{ key }}</td>
                <td class="text-right font-mono text-purple-600 dark:text-purple-400">
                  {{ formatCurrency(value.amount ?? value) }}</td>
              </tr>
              <tr v-if="Object.keys(data.details['Other Project Expenses'].details || {}).length">
                <td class="pl-6 py-2 font-semibold border-t border-gray-200 dark:border-gray-700">Total Other Project
                  Expenses</td>
                <td
                  class="text-right font-mono font-bold text-purple-600 dark:text-purple-400 border-t border-gray-200 dark:border-gray-700">
                  {{ formatCurrency(data.other_expenses) }}
                </td>
              </tr>
            </template>

            <!-- Net Profit -->
            <tr v-if="data.net_profit !== undefined">
              <td colspan="2" class="pt-4"></td>
            </tr>
            <tr :class="data.net_profit >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20'">
              <td class="pl-6 py-3 text-lg font-bold"
                :class="data.net_profit >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400'">
                {{ data.net_profit >= 0 ? '✅' : '⚠️' }} Net {{ data.net_profit >= 0 ? 'Profit' : 'Loss' }}
              </td>
              <td class="text-right font-mono text-lg font-bold"
                :class="data.net_profit >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400'">
                {{ formatCurrency(data.net_profit) }}
              </td>
            </tr>
            <tr v-if="data.net_profit_margin !== undefined">
              <td class="pl-8 py-1 text-xs text-gray-400">Net Profit Margin:
                {{ data.net_profit_margin?.toFixed(1) ?? '0.0' }}%</td>
              <td></td>
            </tr>

            <!-- Allocations -->
            <template v-if="data.allocations && data.allocations.length">
              <tr>
                <td colspan="2"
                  class="pt-6 pb-2 text-sm font-bold text-gray-700 dark:text-gray-300 border-t-2 border-gray-200 dark:border-gray-700">
                  📊 Profit Allocations
                </td>
              </tr>
              <tr v-for="alloc in data.allocations" :key="'alloc-' + alloc.name">
                <td class="pl-6 py-2 text-gray-600 dark:text-gray-300">{{ alloc.name }}</td>
                <td class="text-right font-mono text-gray-700 dark:text-gray-300">{{ formatCurrency(alloc.amount) }}
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const props = defineProps<{
  pageTitle: string;
  periodText: string;
  periodRange: string;
  data: any;
  loading: boolean;
  error: string;
}>();

const formatCurrency = (value: number) => {
  if (value === undefined || value === null) return 'PKR 0.00';
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
  }).format(value);
};
</script>
