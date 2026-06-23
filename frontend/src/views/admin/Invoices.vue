<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Invoices'" />
    <DataTable :store="invoiceStore" :columns="columns" title="Invoices" addButtonLabel="Add Invoice"
      :modalComponent="InvoiceFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import InvoiceFormModal from '@/components/admin/InvoiceFormModal.vue'
import { useInvoiceStore } from '@/stores/invoiceStore'
import type { ColumnDefinition } from '@/types/table'

const invoiceStore = useInvoiceStore()

const columns: ColumnDefinition[] = [
  { key: 'invoice_no', label: 'Invoice No' },
  { key: 'date', label: 'Date' },
  {
    key: 'amount_due',
    label: 'Amount Due',
    format: (value: number) => `$${value.toFixed(2)}`,
  },
  {
    key: 'cod',
    label: 'COD',
    format: (value: number) => `$${value.toFixed(2)}`,
  },
  {
    key: 'output_tax',
    label: 'Output Tax',
    format: (value: number) => `$${value.toFixed(2)}`,
  },
]

onMounted(() => {
  invoiceStore.fetchItems(1)
})
</script>
