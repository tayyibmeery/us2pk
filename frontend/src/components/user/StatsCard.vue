<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</p>
        <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-white">{{ value }}</p>
        <p v-if="change" class="mt-1 text-sm text-emerald-500">{{ change }}</p>
      </div>
      <div class="p-3 rounded-xl" :class="colorClasses[color]?.bg || 'bg-blue-50 dark:bg-blue-900/20'">
        <svg v-if="icon === 'package'" class="w-6 h-6"
          :class="colorClasses[color]?.text || 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <svg v-else-if="icon === 'truck'" class="w-6 h-6"
          :class="colorClasses[color]?.text || 'text-yellow-600 dark:text-yellow-400'" fill="none" stroke="currentColor"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 18a2 2 0 110-4 2 2 0 010 4zm8 0a2 2 0 110-4 2 2 0 010 4zm-8-4V6h8v8m-8 0h8m-8 0H6m8 0h4m-8 0V6h8v8m0 0H6" />
        </svg>
        <svg v-else-if="icon === 'check'" class="w-6 h-6"
          :class="colorClasses[color]?.text || 'text-emerald-600 dark:text-emerald-400'" fill="none"
          stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <svg v-else-if="icon === 'clock'" class="w-6 h-6"
          :class="colorClasses[color]?.text || 'text-red-600 dark:text-red-400'" fill="none" stroke="currentColor"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  value: number | string
  icon: string
  color?: 'blue' | 'green' | 'yellow' | 'red' | 'purple'
  change?: string
}>()

const colorClasses = {
  blue: { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-600 dark:text-blue-400' },
  green: { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-600 dark:text-emerald-400' },
  yellow: { bg: 'bg-yellow-50 dark:bg-yellow-900/20', text: 'text-yellow-600 dark:text-yellow-400' },
  red: { bg: 'bg-red-50 dark:bg-red-900/20', text: 'text-red-600 dark:text-red-400' },
  purple: { bg: 'bg-purple-50 dark:bg-purple-900/20', text: 'text-purple-600 dark:text-purple-400' },
}
</script>
