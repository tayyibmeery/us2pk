<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ title }}</p>
        <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-white">{{ value }}</p>
        <p v-if="change" class="mt-1 text-sm text-emerald-500">{{ change }}</p>
      </div>
      <div :class="[
        'p-3 rounded-xl',
        colorClasses[color]?.bg || 'bg-blue-50 dark:bg-blue-900/20'
      ]">
        <component :is="iconComponent" :class="[
          'w-6 h-6',
          colorClasses[color]?.text || 'text-blue-600 dark:text-blue-400'
        ]" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
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

const iconComponent = computed(() => {
  const icons = {
    package: 'PackageIcon',
    truck: 'TruckIcon',
    check: 'CheckIcon',
    clock: 'ClockIcon',
  }
  return icons[props.icon as keyof typeof icons] || 'PackageIcon'
})
</script>
