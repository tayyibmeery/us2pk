<!-- frontend/src/components/common/ConfirmationModal.vue -->
<template>
  <Teleport to="body">
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95" @after-leave="onAfterLeave">
      <div v-if="isOpen"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="cancel">
        <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6">
          <!-- Icon -->
          <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
              <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
          </div>

          <!-- Title & Message -->
          <h3 class="text-lg font-semibold text-center text-gray-800 dark:text-white mb-2">
            {{ title || 'Delete Confirmation' }}
          </h3>
          <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">
            {{ message || 'Are you sure you want to delete this record? This action cannot be undone.' }}
          </p>

          <!-- Actions -->
          <div class="flex gap-3">
            <button @click="cancel"
              class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
              Cancel
            </button>
            <button @click="confirm" :disabled="loading"
              class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition disabled:opacity-50">
              <span v-if="loading">Deleting...</span>
              <span v-else>Delete</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
  isOpen: boolean
  title?: string
  message?: string
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'cancel'): void
  (e: 'after-leave'): void
}>()

function confirm() {
  emit('confirm')
}

function cancel() {
  emit('cancel')
}

function onAfterLeave() {
  emit('after-leave')
}

// Keyboard shortcuts
function handleKeydown(event: KeyboardEvent) {
  if (!props.isOpen) return
  if (event.key === 'Escape') cancel()
  if (event.key === 'Enter') confirm()
}

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
