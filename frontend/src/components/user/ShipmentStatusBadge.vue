<template>
  <span :class="[
    'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium',
    statusClasses[status] || statusClasses.default
  ]">
    <span class="w-1.5 h-1.5 rounded-full" :class="dotClasses[status] || dotClasses.default"></span>
    {{ status || 'Unknown' }}
  </span>
</template>

<script setup lang="ts">
const props = defineProps<{
  status?: string
}>()

const statusClasses: Record<string, string> = {
  'Delivered': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
  'In Transit': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
  'Pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
  'Cancelled': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
  'default': 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
}

const dotClasses: Record<string, string> = {
  'Delivered': 'bg-emerald-500',
  'In Transit': 'bg-blue-500',
  'Pending': 'bg-yellow-500',
  'Cancelled': 'bg-red-500',
  'default': 'bg-gray-400',
}
</script>
