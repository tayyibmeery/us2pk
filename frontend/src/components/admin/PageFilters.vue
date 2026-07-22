<!-- frontend/src/components/admin/PageFilters.vue -->
<template>
  <div class="flex flex-wrap items-center gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
      <select v-model="filters.type" @change="applyFilters"
        class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option value="">All Types</option>
        <option v-for="(label, value) in types" :key="value" :value="value">
          {{ label }}
        </option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
      <select v-model="filters.status" @change="applyFilters"
        class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
        <option :value="null">All Status</option>
        <option :value="true">Active</option>
        <option :value="false">Inactive</option>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
      <div class="flex gap-2">
        <input v-model="filters.search" @input="applyFilters" type="text" placeholder="Search..."
          class="dark:bg-dark-900 h-10 w-48 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
      </div>
    </div>

    <button @click="resetFilters"
      class="h-10 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
      Reset
    </button>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'

const props = defineProps<{
  types: Record<string, string>
}>()

const emit = defineEmits<{
  (e: 'filter', filters: { type?: string; status?: boolean | null; search?: string }): void
}>()

const filters = reactive({
  type: '',
  status: null as boolean | null,
  search: '',
})

const applyFilters = () => {
  emit('filter', filters)
}

const resetFilters = () => {
  filters.type = ''
  filters.status = null
  filters.search = ''
  emit('filter', filters)
}

// Watch for changes and auto-apply
watch(filters, () => {
  applyFilters()
}, { deep: true })
</script>
