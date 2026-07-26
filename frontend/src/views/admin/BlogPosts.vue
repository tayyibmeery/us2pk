<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Blog Posts'" />
    <DataTable :store="blogPostStore" :columns="columns" title="Blog Posts" addButtonLabel="Add Blog Post"
      :modalComponent="BlogPostFormModal">
      <!-- Custom image column rendering -->
      <template #cell-image="{ item }">
        <div class="flex-shrink-0">
          <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.title"
            class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600 shadow-sm"
            @error="handleImageError" />
          <div v-else
            class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
            <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
          </div>
        </div>
      </template>

      <!-- Custom published date column rendering -->
      <template #cell-published_at="{ item }">
        <span v-if="item.published_at" class="text-sm text-gray-600 dark:text-gray-400">
          {{ new Date(item.published_at).toLocaleDateString() }}
        </span>
        <span v-else class="text-gray-400 text-xs">Not published</span>
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
import BlogPostFormModal from '@/components/admin/BlogPostFormModal.vue'
import { useBlogPostStore } from '@/stores/blogPostStore'
import type { ColumnDefinition } from '@/types/table'

const blogPostStore = useBlogPostStore()

const getImageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com'
  if (imagePath.startsWith('/storage/')) return `${baseUrl}${imagePath}`
  if (imagePath.startsWith('storage/')) return `${baseUrl}/${imagePath}`
  if (imagePath.startsWith('blog/')) return `${baseUrl}/storage/${imagePath}`
  return `${baseUrl}/storage/${imagePath}`
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.style.display = 'none'
  const parent = img.parentElement
  if (parent) {
    const fallback = document.createElement('div')
    fallback.className = 'w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600'
    fallback.innerHTML = '<i class="fas fa-image text-gray-400 dark:text-gray-500"></i>'
    parent.appendChild(fallback)
  }
}

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'image', label: 'Image', sortable: false },
  { key: 'title', label: 'Title', sortable: true },
  { key: 'author', label: 'Author', sortable: true },
  { key: 'published_at', label: 'Published', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  blogPostStore.fetchItems(1)
})
</script>
