<!-- src/components/common/TruncateTooltip.vue -->
<template>
  <div class="relative inline-block group max-w-full">
    <span class="block truncate cursor-default">{{ display }}</span>

    <div v-if="isTruncated" class="pointer-events-none absolute left-0 top-full z-50 mt-1.5 hidden w-max max-w-xs
             rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white shadow-lg
             group-hover:block dark:bg-gray-700">
      {{ text || 'N/A' }}
      <div class="absolute -top-1 left-3 h-2 w-2 rotate-45 bg-gray-900 dark:bg-gray-700"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  text: string | null | undefined
  limit?: number
}>()

const limit = computed(() => props.limit ?? 18)

const display = computed(() => {
  if (!props.text) return 'N/A'
  return props.text.length > limit.value ? props.text.slice(0, limit.value) + '…' : props.text
})

const isTruncated = computed(() => !!props.text && props.text.length > limit.value)
</script>
