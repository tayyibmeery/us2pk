<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Team Members'" />
    <DataTable :store="teamMemberStore" :columns="columns" title="Team Members" addButtonLabel="Add Team Member"
      :modalComponent="TeamMemberFormModal">
      <!-- Custom image column rendering -->
      <template #cell-image="{ item }">
        <div class="flex-shrink-0">
          <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.name"
            class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600 shadow-sm"
            @error="handleImageError" />
          <div v-else
            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
            <span class="text-gray-400 dark:text-gray-500 font-medium text-sm">
              {{ getInitials(item.name) }}
            </span>
          </div>
        </div>
      </template>

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
import TeamMemberFormModal from '@/components/admin/TeamMemberFormModal.vue'
import { useTeamMemberStore } from '@/stores/teamMemberStore'
import type { ColumnDefinition } from '@/types/table'

const teamMemberStore = useTeamMemberStore()

const getImageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com'
  if (imagePath.startsWith('/storage/')) return `${baseUrl}${imagePath}`
  if (imagePath.startsWith('storage/')) return `${baseUrl}/${imagePath}`
  if (imagePath.startsWith('team/')) return `${baseUrl}/storage/${imagePath}`
  return `${baseUrl}/storage/${imagePath}`
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.style.display = 'none'
  const parent = img.parentElement
  if (parent) {
    const fallback = document.createElement('div')
    fallback.className = 'w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600'
    fallback.innerHTML = '<i class="fas fa-user text-gray-400 dark:text-gray-500"></i>'
    parent.appendChild(fallback)
  }
}

const getInitials = (name: string): string => {
  if (!name) return '?'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'image', label: 'Image', sortable: false },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'position', label: 'Position', sortable: true },
  { key: 'order', label: 'Order', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  teamMemberStore.fetchItems(1)
})
</script>
