<!-- frontend/src/components/admin/pages/PageTable.vue -->
<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden relative border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
          <tr>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">
              <i class="fas fa-grip-lines text-gray-400"></i>
            </th>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Image
            </th>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Type
            </th>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Title
            </th>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Status
            </th>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Order
            </th>
            <th
              class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Created
            </th>
            <th
              class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
          <tr v-for="item in items" :key="item.id" draggable @dragstart="onDragStart($event, item)"
            @dragover="onDragOver($event)" @drop="onDrop($event, item)"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150 cursor-move group"
            :class="{ 'opacity-60': !item.status }">
            <!-- Drag Handle -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 dark:text-gray-500">
              <i class="fas fa-grip-lines opacity-50 group-hover:opacity-100 transition-opacity"></i>
            </td>

            <!-- Image -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex-shrink-0">
                <img v-if="isValidImage(item.image)" :src="getStorageUrl(item.image)" :alt="item.title"
                  class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600 shadow-sm"
                  @error="handleImageError" />
                <div v-else
                  class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
                  <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                </div>
              </div>
            </td>

            <!-- Type -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="getTypeColor(item.type)">
                {{ getTypeLabel(item.type) }}
              </span>
            </td>

            <!-- Title -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ item.title }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">/{{ item.slug }}</div>
              </div>
            </td>

            <!-- Status -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
                :class="item.status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                  :class="item.status ? 'bg-green-500' : 'bg-red-500'"></span>
                {{ item.status ? 'Active' : 'Inactive' }}
              </span>
            </td>

            <!-- Order -->
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-400">
              #{{ item.order }}
            </td>

            <!-- Created -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ formatDate(item.created_at) }}
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="flex items-center justify-end gap-1">
                <button @click="edit(item)"
                  class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200"
                  title="Edit">
                  <i class="fas fa-edit text-sm"></i>
                </button>
                <button @click="deleteItem(item)"
                  class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                  title="Delete">
                  <i class="fas fa-trash-alt text-sm"></i>
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty state -->
          <tr v-if="items.length === 0 && !loading">
            <td colspan="8" class="px-6 py-16 text-center">
              <div class="flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                  <i class="fas fa-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No pages found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or create a new page</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > 0"
      class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="text-sm text-gray-600 dark:text-gray-400">
          Showing
          <span
            class="font-medium text-gray-900 dark:text-white">{{ ((pagination.current_page - 1) * pagination.per_page) + 1 }}</span>
          to
          <span
            class="font-medium text-gray-900 dark:text-white">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
          of
          <span class="font-medium text-gray-900 dark:text-white">{{ pagination.total }}</span>
          entries
        </div>
        <div class="flex items-center gap-2">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
            <i class="fas fa-chevron-left mr-1.5"></i>
            Previous
          </button>
          <span
            class="px-4 py-2 text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg">
            {{ pagination.current_page }}
          </span>
          <button @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
            Next
            <i class="fas fa-chevron-right ml-1.5"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading overlay -->
    <div v-if="loading"
      class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-10">
      <div class="flex flex-col items-center gap-3 bg-white dark:bg-gray-800 rounded-xl px-6 py-4 shadow-lg">
        <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Loading...</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { Page } from '@/types/page';

const props = defineProps<{
  items: Page[];
  loading: boolean;
  pagination: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}>();

const emit = defineEmits<{
  (e: 'edit', item: Page): void;
  (e: 'delete', item: Page): void;
  (e: 'reorder', pages: { id: number; order: number }[]): void;
  (e: 'page-change', page: number): void;
}>();

const dragData = ref<Page | null>(null);

// ============================================================
// IMAGE HANDLING - ONLY UPLOADED IMAGES
// ============================================================

// Only handle storage images - no fallbacks to depot or landing
const getStorageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return '';

  // Allow external URLs
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath;
  }

  // Only accept storage paths
  if (imagePath.startsWith('/storage/')) {
    return imagePath;
  }

  if (imagePath.startsWith('storage/')) {
    return '/' + imagePath;
  }

  if (imagePath.startsWith('pages/')) {
    return `/storage/${imagePath}`;
  }

  // Reject any other paths
  return '';
};

// Check if image is valid (from storage or external)
const isValidImage = (imagePath: string | null | undefined): boolean => {
  if (!imagePath) return false;

  // Allow external URLs
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return true;
  }

  // Only valid storage paths
  return imagePath.startsWith('/storage/') ||
    imagePath.startsWith('storage/') ||
    imagePath.startsWith('pages/');
};

// Handle image loading errors
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.style.display = 'none';

  // Show fallback icon
  const parent = img.parentElement;
  if (parent) {
    const fallback = document.createElement('div');
    fallback.className = 'w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600';
    fallback.innerHTML = '<i class="fas fa-image text-gray-400 dark:text-gray-500"></i>';
    parent.appendChild(fallback);
  }
};

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
  };
  return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

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
  };
  return labels[type] || type;
};

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

// ============================================================
// DRAG & DROP
// ============================================================

const onDragStart = (e: DragEvent, item: Page) => {
  dragData.value = item;
  e.dataTransfer!.effectAllowed = 'move';
  e.dataTransfer!.setData('text/plain', JSON.stringify({ id: item.id }));
};

const onDragOver = (e: DragEvent) => {
  e.preventDefault();
  e.dataTransfer!.dropEffect = 'move';
};

const onDrop = (e: DragEvent, targetItem: Page) => {
  e.preventDefault();
  if (!dragData.value || dragData.value.id === targetItem.id) return;

  const items = [...props.items];
  const draggedIndex = items.findIndex(p => p.id === dragData.value!.id);
  const targetIndex = items.findIndex(p => p.id === targetItem.id);

  if (draggedIndex === -1 || targetIndex === -1) return;

  // Swap orders
  const updatedPages = items.map((p, index) => {
    if (index === draggedIndex) {
      return { id: p.id, order: targetItem.order };
    }
    if (index === targetIndex) {
      return { id: p.id, order: dragData.value!.order };
    }
    return { id: p.id, order: p.order };
  });

  emit('reorder', updatedPages);
  dragData.value = null;
};

// ============================================================
// ACTIONS
// ============================================================

const edit = (item: Page) => emit('edit', item);
const deleteItem = (item: Page) => emit('delete', item);
const changePage = (page: number) => emit('page-change', page);
</script>

<style scoped>
/* Custom scrollbar for better UX */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
