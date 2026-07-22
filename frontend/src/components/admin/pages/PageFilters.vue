<!-- frontend/src/components/admin/pages/PageFilters.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Type Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
        <select :value="filters.type"
          @change="$emit('update:filters', { type: ($event.target as HTMLSelectElement).value })"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
          <option value="">All Types</option>
          <option v-for="(label, value) in types" :key="value" :value="value">
            {{ label }}
          </option>
        </select>
      </div>

      <!-- Status Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
        <select :value="filters.status === null ? '' : String(filters.status)"
          @change="$emit('update:filters', { status: ($event.target as HTMLSelectElement).value === '' ? null : ($event.target as HTMLSelectElement).value === 'true' })"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
          <option value="">All Status</option>
          <option value="true">Active</option>
          <option value="false">Inactive</option>
        </select>
      </div>

      <!-- Search -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
        <div class="flex gap-2">
          <input :value="filters.search"
            @input="$emit('update:filters', { search: ($event.target as HTMLInputElement).value })"
            @keyup.enter="$emit('search')" type="text" placeholder="Search by title, slug, or content..."
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />
          <button @click="$emit('search')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-search"></i>
          </button>
          <button @click="$emit('reset')"
            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-undo"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { PageFilters as PageFiltersType } from '@/types/page';

defineProps<{
  filters: PageFiltersType;
  types: Record<string, string>;
}>();

defineEmits<{
  (e: 'update:filters', filters: Partial<PageFiltersType>): void;
  (e: 'search'): void;
  (e: 'reset'): void;
}>();
</script>
