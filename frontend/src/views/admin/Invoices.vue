<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Invoices'" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400">Total Invoices</p>
        <p class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ invoiceStore.pagination.total || 0 }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
          {{ formatCurrency(invoiceStore.summary.total_amount || 0) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400">Total COD</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
          {{ formatCurrency(invoiceStore.summary.total_cod || 0) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400">Total Tax</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
          {{ formatCurrency(invoiceStore.summary.total_tax || 0) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
        <p class="text-xs text-gray-500 dark:text-gray-400">Paid / Pending</p>
        <p class="text-2xl font-bold">
          <span class="text-emerald-600 dark:text-emerald-400">{{ invoiceStore.summary.total_paid || 0 }}</span>
          <span class="text-gray-300 dark:text-gray-600">/</span>
          <span class="text-amber-600 dark:text-amber-400">{{ invoiceStore.summary.total_pending || 0 }}</span>
        </p>
      </div>
    </div>

    <DataTable :store="invoiceStore" :columns="columns" title="Invoices" addButtonLabel="Create Invoice"
      :modalComponent="InvoiceFormModal" @saved="handleSaved">
      <!-- Custom Actions Slot -->
      <template #actions="{ item }">
        <div class="flex items-center justify-end gap-0.5">
          <button @click="viewInvoice(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-blue-400 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
            title="View">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </button>
          <button @click="markAsPaid(item)" v-if="item.status === 'pending'"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-400 transition hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400"
            title="Mark as Paid">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 13l4 4L19 7" />
            </svg>
          </button>
          <button @click="downloadInvoice(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-purple-400 transition hover:bg-purple-50 hover:text-purple-600 dark:hover:bg-purple-500/10 dark:hover:text-purple-400"
            title="Download PDF">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="7 10 12 15 17 10" />
              <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
          </button>
          <button @click="printInvoice(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-50 hover:text-gray-600 dark:hover:bg-gray-500/10 dark:hover:text-gray-400"
            title="Print">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 6 2 18 2 18 9" />
              <path d="M18 9H6" />
              <rect x="6" y="13" width="12" height="8" />
              <line x1="9" y1="13" x2="9" y2="17" />
            </svg>
          </button>
        </div>
      </template>

      <!-- Custom Status Cell -->
      <template #cell-status="{ item }">
        <span :class="{
          'inline-flex px-2 py-1 rounded-full text-xs font-medium': true,
          'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': item.status === 'paid',
          'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': item.status === 'pending',
          'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': item.status === 'overdue',
          'bg-gray-100 text-gray-700 dark:bg-gray-800/50 dark:text-gray-400': item.status === 'cancelled',
        }">
          {{ item.status }}
        </span>
      </template>
    </DataTable>

    <!-- Invoice View Modal -->
    <InvoiceViewModal :isOpen="viewModalOpen" :invoice="selectedInvoice" @close="viewModalOpen = false"
      @download="downloadInvoice" @print="printInvoice" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import InvoiceFormModal from '@/components/admin/InvoiceFormModal.vue'
import InvoiceViewModal from '@/components/admin/InvoiceViewModal.vue'
import { useInvoiceStore } from '@/stores/invoiceStore'
import { useToastStore } from '@/stores/toastStore'
import type { ColumnDefinition } from '@/types/table'

const invoiceStore = useInvoiceStore()
const toastStore = useToastStore()
const viewModalOpen = ref(false)
const selectedInvoice = ref<any>(null)

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value || 0)
}

const columns: ColumnDefinition[] = [
  { key: 'invoice_no', label: 'Invoice No', sortable: true },
  { key: 'date', label: 'Date', sortable: true, format: (value: string) => value ? new Date(value).toLocaleDateString() : 'N/A' },
  { key: 'shipment', label: 'Shipment', format: (value: any) => value?.shipment_code || 'N/A' },
  { key: 'amount_due', label: 'Amount Due', sortable: true, format: (value: number) => formatCurrency(value || 0) },
  { key: 'cod', label: 'COD', format: (value: number) => formatCurrency(value || 0) },
  { key: 'output_tax', label: 'Tax', format: (value: number) => formatCurrency(value || 0) },
  { key: 'total', label: 'Total', format: (value: any, row: any) => formatCurrency((row?.amount_due || 0) + (row?.output_tax || 0)) },
  { key: 'status', label: 'Status' },
]

const viewInvoice = (item: any) => {
  selectedInvoice.value = item
  viewModalOpen.value = true
}

const markAsPaid = async (item: any) => {
  if (!confirm(`Mark invoice ${item.invoice_no} as paid?`)) return

  try {
    await invoiceStore.markAsPaid(item.id, { payment_method: 'Cash' })
    toastStore.success('Invoice marked as paid successfully')
    await invoiceStore.fetchItems(invoiceStore.pagination.current_page)
  } catch (error) {
    toastStore.error('Failed to mark invoice as paid')
    console.error(error)
  }
}

const downloadInvoice = (item: any) => {
  toastStore.info(`Downloading invoice ${item.invoice_no}...`)
  // Implement PDF generation here
}

const printInvoice = (item: any) => {
  toastStore.info(`Printing invoice ${item.invoice_no}...`)
}

const handleSaved = () => {
  invoiceStore.fetchItems(invoiceStore.pagination.current_page)
}

onMounted(() => {
  invoiceStore.fetchItems(1)
})
</script>
