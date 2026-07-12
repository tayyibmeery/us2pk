<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Invoices'" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Invoices</div>
        <div class="text-2xl font-bold">{{ invoiceStore.pagination.total }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Amount</div>
        <div class="text-2xl font-bold text-green-600">{{ formatCurrency(invoiceStore.totalAmount) }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total COD</div>
        <div class="text-2xl font-bold text-blue-600">{{ formatCurrency(invoiceStore.totalCOD) }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Tax</div>
        <div class="text-2xl font-bold text-yellow-600">{{ formatCurrency(invoiceStore.totalTax) }}</div>
      </div>
    </div>

    <DataTable :store="invoiceStore" :columns="columns" title="Invoices" addButtonLabel="Add Invoice"
      :modalComponent="InvoiceFormModal">
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
          <button @click="downloadInvoice(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-green-400 transition hover:bg-green-50 hover:text-green-600 dark:hover:bg-green-500/10 dark:hover:text-green-400"
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
          <button @click="openEditModal(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
            title="Edit">
            <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M17.0911 3.53206C16.2124 2.65338 14.7878 2.65338 13.9091 3.53206L5.6074 11.8337C5.29899 12.1421 5.08687 12.5335 4.99684 12.9603L4.26177 16.445C4.20943 16.6931 4.286 16.9508 4.46529 17.1301C4.64458 17.3094 4.90232 17.3859 5.15042 17.3336L8.63507 16.5985C9.06184 16.5085 9.45324 16.2964 9.76165 15.988L18.0633 7.68631C18.942 6.80763 18.942 5.38301 18.0633 4.50433L17.0911 3.53206ZM14.9697 4.59272C15.2626 4.29982 15.7375 4.29982 16.0304 4.59272L17.0027 5.56499C17.2956 5.85788 17.2956 6.33276 17.0027 6.62565L16.1043 7.52402L14.0714 5.49109L14.9697 4.59272ZM13.0107 6.55175L6.66806 12.8944C6.56526 12.9972 6.49455 13.1277 6.46454 13.2699L5.96704 15.6283L8.32547 15.1308C8.46772 15.1008 8.59819 15.0301 8.70099 14.9273L15.0436 8.58468L13.0107 6.55175Z"
                fill="currentColor" />
            </svg>
          </button>
          <button @click="deleteInvoice(item.id)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            title="Delete">
            <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.04142 4.29199C7.04142 3.04935 8.04878 2.04199 9.29142 2.04199H11.7081C12.9507 2.04199 13.9581 3.04935 13.9581 4.29199V4.54199H16.1252H17.166C17.5802 4.54199 17.916 4.87778 17.916 5.29199C17.916 5.70621 17.5802 6.04199 17.166 6.04199H16.8752V8.74687V13.7469V16.7087C16.8752 17.9513 15.8678 18.9587 14.6252 18.9587H6.37516C5.13252 18.9587 4.12516 17.9513 4.12516 16.7087V13.7469V8.74687V6.04199H3.8335C3.41928 6.04199 3.0835 5.70621 3.0835 5.29199C3.0835 4.87778 3.41928 4.54199 3.8335 4.54199H4.87516H7.04142V4.29199ZM15.3752 13.7469V8.74687V6.04199H13.9581H13.2081H7.79142H7.04142H5.62516V8.74687V13.7469V16.7087C5.62516 17.1229 5.96095 17.4587 6.37516 17.4587H14.6252C15.0394 17.4587 15.3752 17.1229 15.3752 16.7087V13.7469ZM8.54142 4.54199H12.4581V4.29199C12.4581 3.87778 12.1223 3.54199 11.7081 3.54199H9.29142C8.87721 3.54199 8.54142 3.87778 8.54142 4.29199V4.54199ZM8.8335 8.50033C9.24771 8.50033 9.5835 8.83611 9.5835 9.25033V14.2503C9.5835 14.6645 9.24771 15.0003 8.8335 15.0003C8.41928 15.0003 8.0835 14.6645 8.0835 14.2503V9.25033C8.0835 8.83611 8.41928 8.50033 8.8335 8.50033ZM12.9168 9.25033C12.9168 8.83611 12.581 8.50033 12.1668 8.50033C11.7526 8.50033 11.4168 8.83611 11.4168 9.25033V14.2503C11.4168 14.6645 11.7526 15.0003 12.1668 15.0003C12.581 15.0003 12.9168 14.6645 12.9168 14.2503V9.25033Z"
                fill="currentColor" />
            </svg>
          </button>
        </div>
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
import type { ColumnDefinition } from '@/types/table'

const invoiceStore = useInvoiceStore()
const viewModalOpen = ref(false)
const selectedInvoice = ref<any>(null)

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value)
}

const columns: ColumnDefinition[] = [
  {
    key: 'invoice_no',
    label: 'Invoice No',
    sortable: true
  },
  {
    key: 'date',
    label: 'Date',
    sortable: true,
    format: (value: string) => value ? new Date(value).toLocaleDateString() : 'N/A'
  },
  {
    key: 'shipment',
    label: 'Shipment',
    format: (value: any) => value?.shipment_code || value?.pcode || 'N/A'
  },
  {
    key: 'amount_due',
    label: 'Amount Due',
    sortable: true,
    format: (value: number) => formatCurrency(value || 0),
  },
  {
    key: 'cod',
    label: 'COD',
    format: (value: number) => formatCurrency(value || 0),
  },
  {
    key: 'output_tax',
    label: 'Output Tax',
    format: (value: number) => formatCurrency(value || 0),
  },
  {
    key: 'total',
    label: 'Total',
    format: (value: any, row: any) => {
      const amount = row?.amount_due || 0
      const tax = row?.output_tax || 0
      return formatCurrency(amount + tax)
    },
  },
]

const viewInvoice = (item: any) => {
  selectedInvoice.value = item
  viewModalOpen.value = true
}

const downloadInvoice = (item: any) => {
  // Generate PDF download
  console.log('Download invoice:', item)
  // You can implement PDF generation here using libraries like jsPDF or html2pdf
  alert(`Downloading invoice ${item.invoice_no}...`)
}

const printInvoice = (item: any) => {
  // Open print dialog
  console.log('Print invoice:', item)
  // You can implement print functionality here
  alert(`Printing invoice ${item.invoice_no}...`)
}

const openEditModal = (item: any) => {
  // This will be handled by DataTable
  console.log('Edit invoice:', item)
}

const deleteInvoice = async (id: number) => {
  if (confirm('Delete this invoice? This action cannot be undone.')) {
    try {
      await invoiceStore.delete(id)
      await invoiceStore.fetchItems(invoiceStore.pagination.current_page)
    } catch (error) {
      console.error('Failed to delete invoice:', error)
      alert('Failed to delete invoice')
    }
  }
}

onMounted(() => {
  invoiceStore.fetchItems(1)
})
</script>
