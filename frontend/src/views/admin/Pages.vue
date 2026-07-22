<!-- frontend/src/views/admin/Pages.vue -->
<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Pages'" />

    <DataTable :store="pageStore" :columns="columns" title="Pages" addButtonLabel="Add Page"
      :modalComponent="PageFormModal">
      <!-- Custom image column rendering -->
      <template #cell-image="{ item }">
        <div class="flex-shrink-0">
          <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.title"
            class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600 shadow-sm"
            @error="handleImageError" @load="handleImageLoad" />
          <div v-else
            class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
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

      <!-- Custom type column rendering -->
      <template #cell-type="{ item }">
        <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="getTypeColor(item.type)">
          {{ getTypeLabel(item.type) }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import PageFormModal from '@/components/admin/PageFormModal.vue'
import { usePageStore } from '@/stores/pageStore'
import type { ColumnDefinition } from '@/types/table'

const pageStore = usePageStore()

// ============================================================
// IMAGE HANDLING - USING VITE_BASE_URL
// ============================================================

const getImageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return ''

  // If it's already a full URL, return as is
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }

  // Get the base URL from environment variable
  const baseUrl = import.meta.env.VITE_BASE_URL || 'http://localhost:8000'

  // If it starts with /storage/, it's already a storage path
  if (imagePath.startsWith('/storage/')) {
    return `${baseUrl}${imagePath}`
  }

  // If it starts with storage/ (without leading slash), add the slash and base URL
  if (imagePath.startsWith('storage/')) {
    return `${baseUrl}/${imagePath}`
  }

  // If it starts with pages/, it's a storage path
  if (imagePath.startsWith('pages/')) {
    return `${baseUrl}/storage/${imagePath}`
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
    fallback.className = 'w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600 image-fallback'
    fallback.innerHTML = '<i class="fas fa-image text-gray-400 dark:text-gray-500"></i>'
    parent.appendChild(fallback)
  }
}

const handleImageLoad = (event: Event) => {
  const img = event.target as HTMLImageElement
  console.log('Image loaded successfully:', img.src)
}

// ============================================================
// TYPE COLOR & LABEL
// ============================================================

const getTypeColor = (type: string): string => {
  const colors: Record<string, string> = {
    hero: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    about: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    service: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    testimonial: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    team: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
    pricing: 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
    faq: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
    blog: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
    whyus: 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400',
    contact: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    page: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
  }
  return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

const getTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    hero: 'Hero',
    about: 'About',
    service: 'Service',
    testimonial: 'Testimonial',
    team: 'Team',
    pricing: 'Pricing',
    faq: 'FAQ',
    blog: 'Blog',
    whyus: 'Why Us',
    contact: 'Contact',
    page: 'Page',
  }
  return labels[type] || type
}

// ============================================================
// COLUMNS DEFINITION
// ============================================================

const columns: ColumnDefinition[] = [
  {
    key: 'id',
    label: 'ID',
    sortable: true
  },
  {
    key: 'image',
    label: 'Image',
    sortable: false
  },
  {
    key: 'type',
    label: 'Type',
    sortable: true
  },
  {
    key: 'title',
    label: 'Title',
    sortable: true
  },
  {
    key: 'slug',
    label: 'Slug',
    sortable: true
  },
  {
    key: 'status',
    label: 'Status',
    sortable: true
  },
  {
    key: 'order',
    label: 'Order',
    sortable: true
  },
]

// ============================================================
// LIFECYCLE
// ============================================================

onMounted(() => {
  pageStore.fetchTypes()
  pageStore.fetchItems(1)
})
</script>
