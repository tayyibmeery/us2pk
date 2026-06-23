<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Debtors'" />
    <DataTable :store="debtorStore" :columns="columns" title="Debtors" addButtonLabel="Add Debtor"
      :modalComponent="DebtorFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import DebtorFormModal from '@/components/admin/DebtorFormModal.vue'
import { useDebtorStore } from '@/stores/debtorStore'
import type { ColumnDefinition } from '@/types/table'

const debtorStore = useDebtorStore()

const columns: ColumnDefinition[] = [
  { key: 'invoice_no', label: 'Invoice No' },
  {
    key: 'user',
    label: 'User',
    format: (value: any) => value?.name || 'N/A',
  },
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
    key: 'balance',
    label: 'Balance',
    format: (value: number) => `$${value.toFixed(2)}`,
  },
]

onMounted(() => {
  debtorStore.fetchItems(1)
})
</script>
