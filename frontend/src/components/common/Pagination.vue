<!-- src/components/common/Pagination.vue -->
<template>
  <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
    <div class="flex justify-between flex-1 sm:hidden">
      <button @click="$emit('page-change', pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
        Previous
      </button>
      <button @click="$emit('page-change', pagination.current_page + 1)" :disabled="!pagination.next_page_url"
        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
        Next
      </button>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <p class="text-sm text-gray-700">
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
      </p>
      <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
        <button @click="$emit('page-change', pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
          class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
          Previous
        </button>
        <span
          class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button @click="$emit('page-change', pagination.current_page + 1)" :disabled="!pagination.next_page_url"
          class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
          Next
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ pagination: any }>()
defineEmits<{ (e: 'page-change', page: number): void }>()
</script>
