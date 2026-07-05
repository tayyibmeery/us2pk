<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Chart of Accounts'" />
    <DataTable :store="accountStore" :columns="columns" title="Accounts" addButtonLabel="Add Account"
      :modalComponent="AccountFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import AccountFormModal from '@/components/admin/AccountFormModal.vue'
import { useAccountStore } from '@/stores/accountStore'
import type { ColumnDefinition } from '@/types/table'

const accountStore = useAccountStore()

const columns: ColumnDefinition[] = [
  { key: 'code', label: 'Code' },
  { key: 'name', label: 'Name' },
  { key: 'acc_class', label: 'Class' },
  { key: 'nature', label: 'Nature' },
  { key: 'ownership', label: 'Ownership' },
  { key: 'pandlcategory', label: 'P&L Category' },
  {
    key: 'is_active',
    label: 'Status',
    format: (value: boolean) => value ? 'Active' : 'Inactive'
  },
]

onMounted(() => {
  accountStore.fetchItems(1)
})
</script>
