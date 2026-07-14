<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Ledger'" />

    <!-- Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
      <form @submit.prevent="fetchLedger" class="flex flex-wrap items-end gap-4">
        <!-- Account Select -->
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Account</label>
          <select v-model="filters.account_id" @change="onAccountChange"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">All Accounts</option>
            <option v-for="account in accounts" :key="account.id" :value="account.id">
              {{ account.name }}
            </option>
          </select>
        </div>

        <!-- Date Range -->
        <div class="min-w-[180px]">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">From Date</label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.from_date = el }" v-model="filters.from_date" type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Start date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <div class="min-w-[180px]">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">To Date</label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.to_date = el }" v-model="filters.to_date" type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="End date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Source Filter -->
        <div class="min-w-[150px]">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Source</label>
          <select v-model="filters.source"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">All</option>
            <option value="system">System</option>
            <option value="manual">Manual</option>
          </select>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <button type="submit"
            class="inline-flex h-11 items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-brand-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Search
          </button>
          <button type="button" @click="clearFilters"
            class="inline-flex h-11 items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
            Clear
          </button>
        </div>
      </form>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div
        class="w-12 h-12 border-4 border-gray-200 dark:border-gray-700 border-t-brand-600 dark:border-t-brand-400 rounded-full animate-spin">
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!ledgerData.length && !selectedAccount"
      class="flex flex-col items-center justify-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M9 12h6m-6 4h6m-6 4h6M5 3.75h14A1.25 1.25 0 0 1 20.25 5v14A1.25 1.25 0 0 1 19 20.25H5A1.25 1.25 0 0 1 3.75 19V5A1.25 1.25 0 0 1 5 3.75Z" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">No Entries Found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Try adjusting your search filters.</p>
    </div>

    <!-- Ledger Display -->
    <div v-else>
      <!-- When a specific account is selected -->
      <div v-if="selectedAccount"
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <!-- Account Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
          <div class="flex justify-between items-center">
            <div>
              <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ selectedAccount.name }}
              </h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ filters.from_date || 'Start' }} to {{ filters.to_date || 'End' }}
              </p>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-500 dark:text-gray-400">Closing Balance</p>
              <p class="text-lg font-bold"
                :class="selectedAccount.closing_balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                {{ formatCurrency(selectedAccount.closing_balance) }}
                {{ selectedAccount.closing_balance >= 0 ? (selectedAccount.nature === 'Debit' ? 'Dr' : 'Cr') : (selectedAccount.nature === 'Debit' ? 'Cr' : 'Dr') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800/70">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Date</th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Voucher #</th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Particular</th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Debit</th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Credit</th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Balance</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
              <tr v-for="(txn, index) in transactions" :key="index"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-200">{{ formatDate(txn.date) }}</td>
                <td class="px-6 py-3 text-sm">
                  <router-link :to="`/admin/vouchers/${txn.voucher_no}`"
                    class="text-brand-600 dark:text-brand-400 hover:underline font-medium">
                    {{ txn.voucher_no }}
                  </router-link>
                </td>
                <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                  {{ txn.particular || '—' }}
                  <span v-if="txn.shipment_code" class="text-xs text-gray-400 ml-2">({{ txn.shipment_code }})</span>
                </td>
                <td class="px-6 py-3 text-sm font-mono tabular-nums text-right text-emerald-600 dark:text-emerald-400">
                  {{ txn.debit > 0 ? Number(txn.debit).toFixed(2) : '—' }}
                </td>
                <td class="px-6 py-3 text-sm font-mono tabular-nums text-right text-rose-600 dark:text-rose-400">
                  {{ txn.credit > 0 ? Number(txn.credit).toFixed(2) : '—' }}
                </td>
                <td class="px-6 py-3 text-sm font-mono tabular-nums text-right font-semibold"
                  :class="txn.balance >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400'">
                  {{ Number(txn.balance).toFixed(2) }} {{ txn.balance_type }}
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800/70 border-t-2 border-gray-200 dark:border-gray-700">
              <tr>
                <td colspan="3" class="px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Totals</td>
                <td class="px-6 py-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                  {{ selectedAccount.total_debit?.toFixed(2) || '0.00' }}
                </td>
                <td class="px-6 py-3 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                  {{ selectedAccount.total_credit?.toFixed(2) || '0.00' }}
                </td>
                <td class="px-6 py-3 text-right font-mono font-bold text-gray-800 dark:text-white/90">
                  {{ formatCurrency(selectedAccount.closing_balance) }}
                  {{ selectedAccount.closing_balance >= 0 ? (selectedAccount.nature === 'Debit' ? 'Dr' : 'Cr') : (selectedAccount.nature === 'Debit' ? 'Cr' : 'Dr') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- When "All Accounts" is selected - Show all account ledgers -->
      <div v-else class="space-y-8">
        <div v-for="account in ledgerData" :key="account.account_id"
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">

          <!-- Account Header -->
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ account.account_name }}
              </h3>
              <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Closing Balance</p>
                <p class="text-lg font-bold"
                  :class="account.closing_balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                  {{ formatCurrency(account.closing_balance) }}
                  {{ account.closing_balance >= 0 ? (account.account_nature === 'Debit' ? 'Dr' : 'Cr') : (account.account_nature === 'Debit' ? 'Cr' : 'Dr') }}
                </p>
              </div>
            </div>
          </div>

          <!-- Transactions Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-800/70">
                <tr>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Date</th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Voucher #</th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Particular</th>
                  <th
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Debit</th>
                  <th
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Credit</th>
                  <th
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Balance</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <tr v-for="(txn, index) in account.transactions" :key="index"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                  <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-200">{{ formatDate(txn.date) }}</td>
                  <td class="px-6 py-3 text-sm">
                    <router-link :to="`/admin/vouchers/${txn.voucher_no}`"
                      class="text-brand-600 dark:text-brand-400 hover:underline font-medium">
                      {{ txn.voucher_no }}
                    </router-link>
                  </td>
                  <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">
                    {{ txn.particular || '—' }}
                    <span v-if="txn.shipment_code" class="text-xs text-gray-400 ml-2">({{ txn.shipment_code }})</span>
                  </td>
                  <td
                    class="px-6 py-3 text-sm font-mono tabular-nums text-right text-emerald-600 dark:text-emerald-400">
                    {{ txn.debit > 0 ? Number(txn.debit).toFixed(2) : '—' }}
                  </td>
                  <td class="px-6 py-3 text-sm font-mono tabular-nums text-right text-rose-600 dark:text-rose-400">
                    {{ txn.credit > 0 ? Number(txn.credit).toFixed(2) : '—' }}
                  </td>
                  <td class="px-6 py-3 text-sm font-mono tabular-nums text-right font-semibold"
                    :class="txn.balance >= 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-700 dark:text-rose-400'">
                    {{ Number(txn.balance).toFixed(2) }} {{ txn.balance_type }}
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-gray-50 dark:bg-gray-800/70 border-t-2 border-gray-200 dark:border-gray-700">
                <tr>
                  <td colspan="3" class="px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Totals</td>
                  <td class="px-6 py-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                    {{ account.total_debit?.toFixed(2) || '0.00' }}
                  </td>
                  <td class="px-6 py-3 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                    {{ account.total_credit?.toFixed(2) || '0.00' }}
                  </td>
                  <td class="px-6 py-3 text-right font-mono font-bold text-gray-800 dark:text-white/90">
                    {{ formatCurrency(account.closing_balance) }}
                    {{ account.closing_balance >= 0 ? (account.account_nature === 'Debit' ? 'Dr' : 'Cr') : (account.account_nature === 'Debit' ? 'Cr' : 'Dr') }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick, onBeforeUnmount, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import api from '@/services/api'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const accounts = ref<any[]>([])
const ledgerData = ref<any[]>([])
const selectedAccount = ref<any>(null)
const transactions = ref<any[]>([])

const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

const filters = ref({
  account_id: '',
  from_date: '',
  to_date: '',
  source: '',
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
  }).format(value || 0)
}

const formatDate = (date: string) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const fetchAccounts = async () => {
  try {
    const res = await api.get('/admin/accounts', { params: { per_page: 1000 } })
    accounts.value = res.data.data || res.data
  } catch (e) {
    console.error('Failed to fetch accounts', e)
  }
}

const onAccountChange = () => {
  fetchLedger()
}

const fetchLedger = async () => {
  loading.value = true
  try {
    const params: any = {}
    if (filters.value.account_id) params.account_id = filters.value.account_id
    if (filters.value.from_date) params.from_date = filters.value.from_date
    if (filters.value.to_date) params.to_date = filters.value.to_date
    if (filters.value.source) params.source = filters.value.source

    const res = await api.get('/admin/ledger', { params })

    if (filters.value.account_id) {
      // Single account selected
      selectedAccount.value = res.data.account || null
      transactions.value = res.data.transactions || []
      ledgerData.value = []
    } else {
      // All accounts
      selectedAccount.value = null
      transactions.value = []
      ledgerData.value = res.data.accounts || []
    }
  } catch (e) {
    console.error('Failed to fetch ledger', e)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.value = {
    account_id: '',
    from_date: '',
    to_date: '',
    source: '',
  }
  router.push({ query: {} })
  fetchLedger()
}

const initDatePickers = () => {
  const dateFields = ['from_date', 'to_date']
  dateFields.forEach(field => {
    const el = datePickerRefs.value[field]
    if (el) {
      if (flatpickrInstances[field]) flatpickrInstances[field].destroy()
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          filters.value[field as keyof typeof filters.value] = dateStr
        },
      })
      const val = filters.value[field as keyof typeof filters.value]
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
        flatpickrInstances[field].setDate(clean)
      }
    }
  })
}

watch(() => route.query, (newQuery) => {
  const accountId = newQuery.account_id as string
  const fromDate = newQuery.from_date as string
  const toDate = newQuery.to_date as string
  const source = newQuery.source as string

  if (accountId) filters.value.account_id = accountId
  if (fromDate) filters.value.from_date = fromDate
  if (toDate) filters.value.to_date = toDate
  if (source) filters.value.source = source

  if (accountId || fromDate || toDate || source) {
    fetchLedger()
  }
}, { immediate: false })

onMounted(async () => {
  await fetchAccounts()
  await nextTick()
  initDatePickers()

  const query = route.query
  if (query.account_id || query.from_date || query.to_date || query.source) {
    if (query.account_id) filters.value.account_id = query.account_id as string
    if (query.from_date) filters.value.from_date = query.from_date as string
    if (query.to_date) filters.value.to_date = query.to_date as string
    if (query.source) filters.value.source = query.source as string
    await fetchLedger()
  } else {
    const today = new Date()
    const thirtyDaysAgo = new Date()
    thirtyDaysAgo.setDate(today.getDate() - 30)
    filters.value.from_date = thirtyDaysAgo.toISOString().split('T')[0]
    filters.value.to_date = today.toISOString().split('T')[0]
    await fetchLedger()
  }
})

onBeforeUnmount(() => {
  Object.values(flatpickrInstances).forEach(inst => {
    if (inst) inst.destroy()
  })
})
</script>
