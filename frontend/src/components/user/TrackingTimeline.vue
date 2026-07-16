<template>
  <div class="relative">
    <!-- Vertical line -->
    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

    <div v-for="(event, index) in events" :key="index" class="relative flex items-start gap-4 mb-6 last:mb-0">
      <!-- Circle -->
      <div class="relative z-10 flex-shrink-0">
        <div :class="[
          'w-8 h-8 rounded-full flex items-center justify-center',
          event.completed ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-gray-700'
        ]">
          <svg v-if="event.completed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span v-else class="w-2 h-2 rounded-full bg-gray-400 dark:bg-gray-500"></span>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 pt-1">
        <p class="font-medium text-gray-800 dark:text-white">{{ event.title }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ event.description }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ event.date }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  events: Array<{
    title: string
    description: string
    date: string
    completed: boolean
  }>
}>()
</script>
