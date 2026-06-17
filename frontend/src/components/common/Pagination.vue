<template>
  <!-- ✅ Only render when pagination object exists -->
  <div v-if="pagination" class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
    <!-- Mobile view -->
    <div class="flex justify-between flex-1 sm:hidden">
      <button @click="changePage(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50">
        Previous
      </button>
      <button @click="changePage(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50">
        Next
      </button>
    </div>

    <!-- Desktop view -->
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing <span class="font-medium">{{ pagination.from }}</span> to
          <span class="font-medium">{{ pagination.to }}</span> of
          <span class="font-medium">{{ pagination.total }}</span> results
        </p>
      </div>
      <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
        <button @click="changePage(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
          class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50">
          Previous
        </button>
        <span
          class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button @click="changePage(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
          class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50">
          Next
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PaginatedResponse } from '@/types'

const props = defineProps<{
  pagination: PaginatedResponse<any> | null
}>()

const emit = defineEmits<{
  (e: 'pageChange', page: number): void
}>()

const changePage = (page: number) => {
  if (!props.pagination) return
  if (page < 1 || page > props.pagination.last_page) return
  emit('pageChange', page)
}
</script>
