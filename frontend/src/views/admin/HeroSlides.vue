<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Hero Slides'" />
    <DataTable :store="heroSlideStore" :columns="columns" title="Hero Slides" addButtonLabel="Add Hero Slide"
      :modalComponent="HeroSlideFormModal">
      <!-- Custom image column rendering -->
      <template #cell-image="{ item }">
        <div class="flex-shrink-0">
          <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.title"
            class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600 shadow-sm"
            @error="handleImageError" />
          <div v-else
            class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
            <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
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
import HeroSlideFormModal from '@/components/admin/HeroSlideFormModal.vue'
import { useHeroSlideStore } from '@/stores/heroSlideStore'
import type { ColumnDefinition } from '@/types/table'

const heroSlideStore = useHeroSlideStore()

// ============================================================
// IMAGE HANDLING
// ============================================================

const getImageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return ''

  // If it's already a full URL, return as is
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }

  // Get the base URL from environment variable
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com'

  // If it starts with /storage/, it's already a storage path
  if (imagePath.startsWith('/storage/')) {
    return `${baseUrl}${imagePath}`
  }

  // If it starts with storage/ (without leading slash), add the slash and base URL
  if (imagePath.startsWith('storage/')) {
    return `${baseUrl}/${imagePath}`
  }

  // If it starts with hero/, it's a storage path
  if (imagePath.startsWith('hero/')) {
    return `${baseUrl}/storage/${imagePath}`
  }

  // If it starts with /, it's a public path
  if (imagePath.startsWith('/')) {
    return `${baseUrl}${imagePath}`
  }

  // If it doesn't have any prefix, assume it's from storage
  return `${baseUrl}/storage/${imagePath}`
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.warn('Image failed to load:', img.src)

  // Show fallback
  img.style.display = 'none'

  const parent = img.parentElement
  if (parent) {
    // Remove existing fallback if any
    const existingFallback = parent.querySelector('.image-fallback')
    if (existingFallback) {
      existingFallback.remove()
    }

    const fallback = document.createElement('div')
    fallback.className = 'w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600 image-fallback'
    fallback.innerHTML = '<i class="fas fa-image text-gray-400 dark:text-gray-500"></i>'
    parent.appendChild(fallback)
  }
}

// ============================================================
// COLUMNS DEFINITION
// ============================================================

const columns: ColumnDefinition[] = [
  { key: 'id', label: 'ID', sortable: true },
  { key: 'image', label: 'Image', sortable: false },
  { key: 'title', label: 'Title', sortable: true },
  { key: 'subtitle', label: 'Subtitle', sortable: true },
  { key: 'order', label: 'Order', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
]

onMounted(() => {
  heroSlideStore.fetchItems(1)
})
</script>
