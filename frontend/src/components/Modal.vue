<template>
  <teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" @click.self="close">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="close"></div>

      <!-- Modal Container -->
      <div class="flex min-h-full items-center justify-center p-4">
        <div
          class="relative w-full transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 shadow-xl transition-all"
          :class="{
            'max-w-md': size === 'sm',
            'max-w-lg': size === 'md' || !size,
            'max-w-2xl': size === 'lg',
            'max-w-4xl': size === 'xl',
            'max-w-6xl': size === '2xl',
          }">
          <slot name="body"></slot>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup lang="ts">
defineProps<{
  isOpen: boolean
  size?: 'sm' | 'md' | 'lg' | 'xl' | '2xl'
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const close = () => emit('close')
</script>
