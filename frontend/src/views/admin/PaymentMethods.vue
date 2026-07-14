<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Payment Methods'" />
    <DataTable :store="paymentMethodStore" :columns="columns" title="Payment Methods"
      addButtonLabel="Add Payment Method" :modalComponent="PaymentMethodFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import PaymentMethodFormModal from '@/components/admin/PaymentMethodFormModal.vue'
import { usePaymentMethodStore } from '@/stores/paymentMethodStore'
import type { ColumnDefinition } from '@/types/table'

const paymentMethodStore = usePaymentMethodStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  {
    key: 'account',
    label: 'Linked Account',
    format: (value: any) => value?.name || 'Default (Cash Account)'
  },
  {
    key: 'status',
    label: 'Status',
    format: (value: boolean) => value ? 'Active' : 'Inactive',
  },
]

onMounted(() => {
  paymentMethodStore.fetchItems(1)
})
</script>
