<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Users'" />
    <DataTable :store="userStore" :columns="columns" title="Users" addButtonLabel="Add User"
      :modalComponent="UserFormModal" hideDelete>
      <!-- Custom slot for Status column with badge + dropdown -->
      <template #cell-status="{ item }">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="{
            'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400': item.status === 'pending',
            'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400': item.status === 'verified',
            'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400': item.status === 'approved',
          }">
            <span class="h-1.5 w-1.5 rounded-full" :class="{
              'bg-yellow-500': item.status === 'pending',
              'bg-blue-500': item.status === 'verified',
              'bg-green-500': item.status === 'approved',
            }"></span>
            {{ item.status }}
          </span>
          <select @change="updateStatus(item.id, ($event.target as HTMLSelectElement).value)"
            class="h-6 rounded border border-gray-200 bg-white text-xs dark:border-gray-700 dark:bg-gray-800">
            <option value="pending" :selected="item.status === 'pending'">Pending</option>
            <option value="verified" :selected="item.status === 'verified'">Verified</option>
            <option value="approved" :selected="item.status === 'approved'">Approved</option>
          </select>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import UserFormModal from '@/components/admin/UserFormModal.vue'
import { useUserStore } from '@/stores/userStore'
import type { ColumnDefinition } from '@/types/table'

const userStore = useUserStore()

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'address', label: 'Address' },
  {
    key: 'created_at',
    label: 'Dated',
    format: (value: string) => new Date(value).toLocaleDateString(),
  },
  { key: 'status', label: 'Status' }, // custom rendered via slot
  {
    key: 'city',
    label: 'City',
    format: (value: any) => value?.city_name || 'N/A',
  },
  { key: 'pcode', label: 'PCode' },
  { key: 'phone', label: 'Phone #' },
  { key: 'source', label: 'Source' },
]

const updateStatus = async (userId: number, status: string) => {
  try {
    await userStore.updateStatus(userId, status)
  } catch (error) {
    console.error('Status update failed:', error)
  }
}

onMounted(() => {
  userStore.fetchItems(1)
})
</script>
