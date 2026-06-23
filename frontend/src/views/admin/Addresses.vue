<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Addresses'" />
    <DataTable :store="addressStore" :columns="columns" title="Addresses" addButtonLabel="Add Address"
      :modalComponent="AddressFormModal" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import AddressFormModal from '@/components/admin/AddressFormModal.vue'
import { useAddressStore } from '@/stores/addressStore'
import type { ColumnDefinition } from '@/types/table'

const addressStore = useAddressStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'warehouse', label: 'Warehouse' },
  { key: 'address', label: 'Address' },
]

onMounted(() => {
  addressStore.fetchItems(1)
})
</script>
