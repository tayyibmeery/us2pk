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
      <button @click="clearFilters" class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">
        Clear
      </button>
    </div>

    <DataTable :store="voucherStore" :columns="columns" title="Vouchers" addButtonLabel="Create Voucher"
      :modalComponent="VoucherFormModal" @saved="handleSaved">
      <template #actions="{ item, edit, delete: deleteFn }">
        <div class="flex items-center justify-end gap-0.5">
          <router-link :to="`/admin/vouchers/${item.id}`"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-600">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </router-link>
          <button @click="edit(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-brand-50 hover:text-brand-600">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button @click="approveVoucher(item.id)" v-if="!item.approved && !item.is_deleted"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-green-500 hover:bg-green-50">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
          <button @click="deleteFn(item.id)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-500">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </template>
      <template #cell-status="{ item }">
        <span :class="{
          'text-green-600': item.approved,
          'text-yellow-600': !item.approved && !item.is_deleted,
          'text-red-400': item.is_deleted,
        }">
          {{ item.is_deleted ? 'Deleted' : (item.approved ? 'Approved' : 'Not Approved') }}
        </span>
      </template>
      <template #cell-amount="{ item }">
        {{ Number(item.total_debit || 0).toFixed(2) }}
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
  { key: 'amount', label: 'Amount' },
  { key: 'status', label: 'Status' },
]

const handleSaved = () => {
  fetchVouchers()
}

const approveVoucher = async (id: number) => {
  if (!confirm('Approve this voucher?')) return
  try {
    await voucherStore.approve(id)
    toastStore.success('Voucher approved successfully!')
    await fetchVouchers()
  } catch (e: any) {
    toastStore.error(e.response?.data?.message || 'Failed to approve voucher')
  }
}

const fetchVouchers = () => {
  const params: any = {}

  // Map filter status to API parameters
  if (filterStatus.value === '0') {
    params.approved = false
    params.is_deleted = false
  } else if (filterStatus.value === '1') {
    params.approved = true
    params.is_deleted = false
  } else if (filterStatus.value === 'deleted') {
    params.is_deleted = true
  }

  voucherStore.fetchItems(1, params)
}

const onFilterChange = () => {
  // Update URL with filter parameter
  const query: any = {}
  if (filterStatus.value) {
    query.approved = filterStatus.value
  }
  router.push({ query })
  fetchVouchers()
}

const clearFilters = () => {
  filterStatus.value = ''
  router.push({ query: {} })
  fetchVouchers()
}

// Watch for route query changes
watch(() => route.query.approved, (newVal) => {
  if (newVal === '0') {
    filterStatus.value = '0'
  } else if (newVal === '1') {
    filterStatus.value = '1'
  } else if (newVal === 'deleted') {
    filterStatus.value = 'deleted'
  } else {
    filterStatus.value = ''
  }
  fetchVouchers()
}, { immediate: true })

onMounted(() => {
  // Check URL query parameter on mount
  const approvedParam = route.query.approved as string
  if (approvedParam === '0') {
    filterStatus.value = '0'
  } else if (approvedParam === '1') {
    filterStatus.value = '1'
  } else if (approvedParam === 'deleted') {
    filterStatus.value = 'deleted'
  }
  fetchVouchers()
})
</script>
