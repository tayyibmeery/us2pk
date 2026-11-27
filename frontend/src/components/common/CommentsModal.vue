<!-- frontend/src/components/common/CommentsModal.vue -->
<template>
  <Teleport to="body">
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
      <div v-if="isOpen"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="close">
        <div
          class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-h-[80vh] flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              Comments
              <span class="text-sm font-normal text-gray-400 dark:text-gray-500">
                ({{ commentCount }})
              </span>
            </h3>
            <button @click="close"
              class="rounded-md p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Comment List -->
          <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <div v-if="!comments.length" class="text-center text-gray-400 dark:text-gray-500 py-8">
              No comments available.
            </div>
            <div v-for="(comment, index) in comments" :key="index"
              class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
              <div
                class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center text-brand-600 dark:text-brand-400 font-semibold text-sm">
                {{ comment.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words">
                  {{ comment }}
                </p>
                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                  Comment #{{ index + 1 }}
                </span>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <button @click="close"
              class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
              Close
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue'

const props = defineProps<{
  isOpen: boolean
  comments: string | string[] | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

// Parse comments into array
const comments = computed(() => {
  if (!props.comments) return []

  // If it's already an array, return it
  if (Array.isArray(props.comments)) {
    return props.comments.filter(c => c && c.trim())
  }

  // If it's a string, split by new lines and filter empty
  return props.comments
    .split(/\n+/)
    .map(c => c.trim())
    .filter(c => c.length > 0)
})

const commentCount = computed(() => comments.value.length)

function close() {
  emit('close')
}

// Keyboard shortcut to close
function handleKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape' && props.isOpen) {
    close()
  }
}

// Add/remove event listener
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    document.addEventListener('keydown', handleKeydown)
    document.body.style.overflow = 'hidden'
  } else {
    document.removeEventListener('keydown', handleKeydown)
    document.body.style.overflow = ''
  }
}, { immediate: true })
</script>
