<template>
  <PageBreadcrumb :pageTitle="'Voucher Details'" />

  <!-- Loading State -->
  <div v-if="loading" class="flex flex-col items-center justify-center py-16">
    <div
      class="w-12 h-12 border-4 border-gray-200 dark:border-gray-700 border-t-teal-600 dark:border-t-teal-400 rounded-full animate-spin">
    </div>
    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Loading voucher details...</p>
  </div>

  <!-- Error State -->
  <div v-else-if="error" class="flex flex-col items-center justify-center py-16">
    <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mb-4">
      <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Error Loading Voucher</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ error }}</p>
    <button @click="goBack"
      class="mt-4 px-4 py-2 text-sm font-medium text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 transition-colors">
      ← Back to Vouchers
    </button>
  </div>

  <!-- Not Found -->
  <div v-else-if="!voucher" class="flex flex-col items-center justify-center py-16">
    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
      <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12h6m-6 4h6M9 8h6M5 3.75h14A1.25 1.25 0 0 1 20.25 5v14A1.25 1.25 0 0 1 19 20.25H5A1.25 1.25 0 0 1 3.75 19V5A1.25 1.25 0 0 1 5 3.75Z" />
      </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Voucher Not Found</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">The voucher you're looking for doesn't exist or has been
      deleted.</p>
    <button @click="goBack"
      class="mt-4 px-4 py-2 text-sm font-medium text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 transition-colors">
      ← Back to Vouchers
    </button>
  </div>

  <!-- Voucher Details -->
  <div v-else class="max-w-5xl mx-auto">
    <!-- Voucher Card -->
    <div
      class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">

      <!-- Header -->
      <div
        class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 border-b border-gray-100 dark:border-gray-700"
        :class="voucher.approved ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : 'bg-amber-50/50 dark:bg-amber-900/10'">

        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-full flex items-center justify-center"
            :class="voucher.approved ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-amber-100 dark:bg-amber-900/30'">
            <svg class="w-6 h-6"
              :class="voucher.approved ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'"
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6M9 8h6M5 3.75h14A1.25 1.25 0 0 1 20.25 5v14A1.25 1.25 0 0 1 19 20.25H5A1.25 1.25 0 0 1 3.75 19V5A1.25 1.25 0 0 1 5 3.75Z" />
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ voucher.voucher_no }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ voucher.description || 'No description' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold" :class="voucher.approved
            ? 'text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/30'
            : 'text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30'">
            <span class="w-2 h-2 rounded-full bg-current"></span>
            {{ voucher.approved ? 'Approved' : 'Not Approved' }}
          </span>
          <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1">
            {{ voucher.source }}
          </span>
        </div>
      </div>

      <!-- Details Grid -->
      <div
        class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex items-center gap-2 text-sm">
          <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span class="font-medium text-gray-500 dark:text-gray-400">Date:</span>
          <span class="text-gray-700 dark:text-gray-200">{{ formatDate(voucher.date) }}</span>
        </div>
        <div class="flex items-center gap-2 text-sm">
          <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="font-medium text-gray-500 dark:text-gray-400">Source:</span>
          <span class="text-gray-700 dark:text-gray-200 capitalize">{{ voucher.source }}</span>
        </div>
        <div class="sm:col-span-2 flex items-start gap-2 text-sm">
          <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mt-0.5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 12h6m-6 4h6M9 8h6M5 3.75h14A1.25 1.25 0 0 1 20.25 5v14A1.25 1.25 0 0 1 19 20.25H5A1.25 1.25 0 0 1 3.75 19V5A1.25 1.25 0 0 1 5 3.75Z" />
          </svg>
          <span class="font-medium text-gray-500 dark:text-gray-400">Description:</span>
          <span class="text-gray-700 dark:text-gray-200">{{ voucher.description || '—' }}</span>
        </div>
      </div>

      <!-- Entries Table -->
      <div class="p-6">
        <h3
          class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          Journal Entries
        </h3>

        <div v-if="!voucher.details || voucher.details.length === 0"
          class="text-center py-8 text-yellow-500 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl border border-yellow-200 dark:border-yellow-800">
          <svg class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <p class="text-sm font-medium">No entries found for this voucher</p>
        </div>

        <div v-else class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/70">
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
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
              <tr v-for="detail in voucher.details" :key="detail.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-6 py-3.5 text-gray-700 dark:text-gray-200">
                  <span class="font-medium">{{ detail.account?.name || 'N/A' }}</span>
                </td>
                <td class="px-6 py-3.5 text-right font-mono tabular-nums text-gray-700 dark:text-gray-200">
                  {{ Number(detail.debit || 0).toFixed(2) }}
                </td>
                <td class="px-6 py-3.5 text-right font-mono tabular-nums text-gray-700 dark:text-gray-200">
                  {{ Number(detail.credit || 0).toFixed(2) }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800/70 border-t-2 border-gray-200 dark:border-gray-700">
              <tr>
                <td class="px-6 py-3.5 font-semibold text-gray-800 dark:text-white/90">Total</td>
                <td
                  class="px-6 py-3.5 text-right font-mono font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                  {{ Number(totalDebit).toFixed(2) }}
                </td>
                <td class="px-6 py-3.5 text-right font-mono font-bold tabular-nums text-rose-600 dark:text-rose-400">
                  {{ Number(totalCredit).toFixed(2) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Balance Status -->
        <div v-if="voucher.details && voucher.details.length > 0" class="mt-4">
          <div v-if="totalDebit !== totalCredit"
            class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
              <p class="font-medium">Unbalanced Voucher</p>
              <p class="text-sm">Debit ({{ Number(totalDebit).toFixed(2) }}) ≠ Credit
                ({{ Number(totalCredit).toFixed(2) }})</p>
            </div>
          </div>
          <div v-else
            class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-400">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <p class="font-medium">Balanced Voucher</p>
              <p class="text-sm">Debit matches Credit ({{ Number(totalDebit).toFixed(2) }})</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div
        class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <button @click="goBack"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Vouchers
        </button>

        <div class="flex items-center gap-3">
          <button v-if="!voucher.approved" @click="approve"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow transition-all duration-200">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Approve Voucher
          </button>
          <button v-else @click="goBack"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14" />
            </svg>
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const error = ref('');
const voucher = ref<any>(null);

const totalDebit = computed(() => {
  if (!voucher.value?.details || voucher.value.details.length === 0) return 0;
  return voucher.value.details.reduce((sum: number, d: any) => {
    return sum + (Number(d.debit) || 0);
  }, 0);
});

const totalCredit = computed(() => {
  if (!voucher.value?.details || voucher.value.details.length === 0) return 0;
  return voucher.value.details.reduce((sum: number, d: any) => {
    return sum + (Number(d.credit) || 0);
  }, 0);
});

const formatDate = (date: string | null) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
};

async function fetchVoucher() {
  loading.value = true;
  error.value = '';
  try {
    const id = route.params.id;
    let response;

    try {
      response = await api.get(`/admin/vouchers/${id}`);
    } catch (firstError: any) {
      if (firstError.response?.status === 404 || isNaN(Number(id))) {
        response = await api.get(`/admin/vouchers/by-number/${id}`);
      } else {
        throw firstError;
      }
    }

    voucher.value = response.data;
  } catch (e: any) {
    console.error('Error fetching voucher:', e);
    error.value = e.response?.data?.message || 'Failed to load voucher';
  } finally {
    loading.value = false;
  }
}

async function approve() {
  if (!confirm('Approve this voucher?')) return;
  try {
    await api.post(`/admin/vouchers/${voucher.value.id}/approve`);
    await fetchVoucher();
  } catch (e: any) {
    alert(e.response?.data?.message || 'Approval failed');
  }
}

function goBack() {
  router.push('/admin/vouchers');
}

onMounted(fetchVoucher);
</script>
