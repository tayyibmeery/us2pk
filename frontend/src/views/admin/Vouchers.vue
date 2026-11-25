<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="pageTitle" />

    <!-- Filter Tabs -->
    <div
      class="flex flex-wrap items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter:</label>
        <select v-model="filterStatus" @change="onFilterChange"
          class="h-9 rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
          <option value="">All Vouchers</option>
          <option value="0">Not Approved</option>
          <option value="1">Approved</option>
          <option value="deleted">Deleted</option>
        </select>
      </div>

      <!-- Source filter -->
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Source:</label>
        <select v-model="filterSource" @change="fetchVouchers"
          class="h-9 rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
          <option value="">All</option>
          <option value="manual">Manual</option>
          <option value="system">System</option>
        </select>
      </div>

      <button @click="clearFilters" class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">
        Clear
      </button>
    </div>

    <DataTable :store="voucherStore" :columns="columns" title="Vouchers" addButtonLabel="Create Voucher"
      :modalComponent="VoucherFormModal" @saved="handleSaved">

      <template #actions="{ item, edit }">
        <div class="flex items-center justify-end gap-0.5">

          <!-- View -->
          <router-link :to="`/admin/vouchers/${item.id}`"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
            title="View">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </router-link>

          <!-- Edit: only for manual, unapproved, not deleted -->
          <button v-if="canEdit(item)" @click="edit(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
            title="Edit">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>

          <!-- Approve: only for unapproved, not deleted -->
          <button v-if="canApprove(item)" @click="handleApprove(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-green-500 transition hover:bg-green-50 dark:hover:bg-green-500/10 dark:hover:text-green-400"
            title="Approve">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>

          <!-- Delete: only for manual, unapproved, not deleted -->
          <button v-if="canDelete(item)" @click="handleDelete(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            title="Delete (requires password)">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>

          <!-- Restore: only for trashed vouchers -->
          <button v-if="isTrashed(item)" @click="handleRestore(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-500 transition hover:bg-emerald-50 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400"
            title="Restore (requires password)">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </template>

      <!-- Source column -->
      <template #cell-source="{ item }">
        <span v-if="item.source === 'system'"
          class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400">
          <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
          </svg>
          System
        </span>
        <span v-else-if="item.source === 'manual'"
          class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
          <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Manual
        </span>
        <span v-else class="text-xs text-gray-400">{{ item.source || '—' }}</span>
      </template>

      <!-- Amount column -->
      <template #cell-amount="{ item }">
        <span class="font-mono">{{ formatAmount(item) }}</span>
      </template>

      <!-- Status column -->
      <template #cell-status="{ item }">
        <span :class="{
          'text-green-600 dark:text-green-400': item.approved && !isTrashed(item),
          'text-yellow-600 dark:text-yellow-400': !item.approved && !isTrashed(item),
          'text-red-400 dark:text-red-400 line-through': isTrashed(item),
        }">
          {{ isTrashed(item) ? 'Deleted' : (item.approved ? 'Approved' : 'Not Approved') }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import VoucherFormModal from '@/components/admin/VoucherFormModal.vue'
import { useVoucherStore } from '@/stores/voucherStore'
import { useToastStore } from '@/stores/toastStore'
import type { ColumnDefinition } from '@/types/table'

const route = useRoute()
const router = useRouter()
const voucherStore = useVoucherStore()
const toastStore = useToastStore()

const filterStatus = ref<string>('')
const filterSource = ref<string>('')

const pageTitle = computed(() => {
  if (filterStatus.value === '0') return 'Approval Screen - Not Approved'
  if (filterStatus.value === '1') return 'Approved Vouchers'
  if (filterStatus.value === 'deleted') return 'Deleted Vouchers'
  return 'Vouchers'
})

const columns: ColumnDefinition[] = [
  { key: 'voucher_no', label: 'Voucher #' },
  { key: 'date', label: 'Date' },
  { key: 'description', label: 'Description' },
  { key: 'source', label: 'Source' },
  { key: 'amount', label: 'Amount' },
  { key: 'status', label: 'Status' },
]

// ✅ Helper predicates used by the action buttons
const isTrashed = (item: any) => !!item.deleted_at || item.is_deleted === true
const canEdit = (item: any) => !isTrashed(item) && !item.approved && item.source === 'manual'
const canApprove = (item: any) => !isTrashed(item) && !item.approved
const canDelete = (item: any) => !isTrashed(item) && !item.approved && item.source === 'manual'

const formatAmount = (item: any): string => {
  let total = 0
  if (item.total_debit !== undefined && item.total_debit !== null) {
    total = Number(item.total_debit) || 0
  } else if (Array.isArray(item.details)) {
    total = item.details.reduce((sum: number, d: any) => sum + (Number(d.debit) || 0), 0)
  }
  return total.toLocaleString('en-PK', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

const handleSaved = () => {
  fetchVouchers()
}

const handleApprove = async (item: any) => {
  if (!confirm(`Approve voucher ${item.voucher_no}?`)) return
  try {
    await voucherStore.approve(item.id)
    toastStore.success('Voucher approved successfully!')
    await fetchVouchers()
  } catch (e: any) {
    toastStore.error(e.response?.data?.message || 'Failed to approve voucher')
  }
}

// ✅ Password-protected delete
const handleDelete = async (item: any) => {
  if (!confirm(`Delete voucher ${item.voucher_no}?\n\nThis will hide it from the ledger but you can restore it later.`)) return

  const password = window.prompt('🔐 Enter Admin Password to delete this voucher:')
  if (password === null) return
  if (!password.trim()) {
    alert('Password is required to delete.')
    return
  }

  try {
    await voucherStore.delete(item.id, password)
    toastStore.success('Voucher deleted successfully.')
    await fetchVouchers()
  } catch (e: any) {
    alert(e.response?.data?.message || 'Failed to delete voucher. Incorrect password?')
  }
}

// ✅ Password-protected restore
const handleRestore = async (item: any) => {
  if (!confirm(`Restore voucher ${item.voucher_no}?`)) return

  const password = window.prompt('🔐 Enter Admin Password to restore this voucher:')
  if (password === null) return
  if (!password.trim()) {
    alert('Password is required to restore.')
    return
  }

  try {
    await voucherStore.restore(item.id, password)
    toastStore.success('Voucher restored successfully.')
    await fetchVouchers()
  } catch (e: any) {
    alert(e.response?.data?.message || 'Failed to restore voucher. Incorrect password?')
  }
}

const fetchVouchers = () => {
  const params: any = {}

  if (filterStatus.value === '0') {
    params.approved = false
  } else if (filterStatus.value === '1') {
    params.approved = true
  } else if (filterStatus.value === 'deleted') {
    params.trashed = true
  }

  if (filterSource.value) {
    params.source = filterSource.value
  }

  voucherStore.fetchItems(1, params)
}

const onFilterChange = () => {
  const query: any = {}
  if (filterStatus.value) query.status = filterStatus.value
  if (filterSource.value) query.source = filterSource.value
  router.push({ query })
  fetchVouchers()
}

const clearFilters = () => {
  filterStatus.value = ''
  filterSource.value = ''
  router.push({ query: {} })
  fetchVouchers()
}

watch(() => route.query.status, (newVal) => {
  filterStatus.value = (newVal as string) || ''
  fetchVouchers()
}, { immediate: true })

onMounted(() => {
  const statusParam = route.query.status as string
  const sourceParam = route.query.source as string
  if (statusParam) filterStatus.value = statusParam
  if (sourceParam) filterSource.value = sourceParam
  fetchVouchers()
})
</script>
