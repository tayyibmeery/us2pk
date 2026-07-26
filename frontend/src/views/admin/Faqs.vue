<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'FAQs'" />
    <DataTable :store="faqStore" :columns="columns" title="FAQs" addButtonLabel="Add FAQ"
      :modalComponent="FaqFormModal">
      <!-- Custom status column rendering -->
      <template #cell-status="{ item }">
        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
          :class="item.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
          <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="item.status ? 'bg-green-500' : 'bg-red-500'"></span>
          {{ item.status ? 'Active' : 'Inactive' }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import FaqFormModal from '@/components/admin/FaqFormModal.vue'
import { useFaqStore } from '@/stores/faqStore'
import type { ColumnDefinition } from '@/types/table'

const faqStore = useFaqStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'question', label: 'Question', sortable: true },
  { key: 'order', label: 'Order', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  faqStore.fetchItems(1)
})
</script>
