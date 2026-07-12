<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Users'" />
    <DataTable :store="userStore" :columns="columns" title="Users" addButtonLabel="Add User"
      :modalComponent="UserFormModal" hideDelete>

      <!-- Name + avatar -->
      <template #cell-name="{ item }">
        <div class="flex items-center gap-3">
          <!-- <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
            {{ initials(item.name) }}
          </div> -->
          <TruncateTooltip :text="item.name" :limit="20" class="font-medium text-gray-800 dark:text-white" />
        </div>
      </template>

      <!-- Email -->
      <template #cell-email="{ item }">
        <TruncateTooltip :text="item.email" :limit="22" class="text-gray-600 dark:text-gray-300" />
      </template>

      <!-- Address, truncated with hover tooltip -->
      <template #cell-address="{ item }">
        <TruncateTooltip :text="item.address" :limit="14" class="text-gray-600 dark:text-gray-300" />
      </template>

      <!-- Status column: badge + dropdown -->
      <template #cell-status="{ item }">
        <div class="flex items-center gap-2">
          <span
            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
            :class="statusStyles[item.status as keyof typeof statusStyles]?.badge">
            <span class="h-1.5 w-1.5 rounded-full"
              :class="statusStyles[item.status as keyof typeof statusStyles]?.dot"></span>
            {{ item.status }}
          </span>

          <select :value="item.status" @change="updateStatus(item.id, ($event.target as HTMLSelectElement).value)"
            class="h-7 cursor-pointer rounded-md border border-gray-200 bg-white px-1.5 text-xs text-gray-600
                   transition hover:border-gray-300 focus:border-brand-300 focus:outline-none focus:ring-2
                   focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="approved">Approved</option>
          </select>
        </div>
      </template>

      <!-- PCode as a monospace chip -->
      <template #cell-pcode="{ item }">
        <span
          class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
          {{ item.pcode || '—' }}
        </span>
      </template>

      <!-- City as a subtle badge -->
      <template #cell-city="{ item }">
        <span class="text-sm text-gray-600 dark:text-gray-300">{{ item.city?.city_name || 'N/A' }}</span>
      </template>

    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import TruncateTooltip from '@/components/common/TruncateTooltip.vue'

import UserFormModal from '@/components/admin/UserFormModal.vue'
import { useUserStore } from '@/stores/userStore'
import type { ColumnDefinition } from '@/types/table'

const userStore = useUserStore()

const statusStyles = {
  pending: { badge: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400', dot: 'bg-yellow-500' },
  verified: { badge: 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', dot: 'bg-blue-500' },
  approved: { badge: 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400', dot: 'bg-green-500' },
}

const initials = (name: string | null | undefined) => {
  if (!name) return '?'
  return name.trim().split(/\s+/).slice(0, 2).map(n => n[0]?.toUpperCase()).join('')
}

const columns: ColumnDefinition[] = [
  // { key: 'id', label: 'ID' },
  { key: 'pcode', label: 'PCode' },      // rendered via slot

  { key: 'name', label: 'Name' },        // rendered via slot
  { key: 'email', label: 'Email' },      // rendered via slot
  { key: 'address', label: 'Address' },  // rendered via slot, truncated + tooltip

  { key: 'city', label: 'City' },        // rendered via slot
  { key: 'phone', label: 'Phone #' },
  { key: 'status', label: 'Status' },    // rendered via slot

  {
    key: 'created_at',
    label: 'Dated',
    format: (value: string) => new Date(value).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }),
  },
  // { key: 'source', label: 'Source' },
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
