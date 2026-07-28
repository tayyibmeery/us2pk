<!-- frontend/src/components/common/ImageModal.vue -->
<template>
  <Teleport to="body">
    <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
      <div v-if="isOpen"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        @click.self="close">
        <div class="relative max-w-[90vw] max-h-[90vh]">
          <button @click="close" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <img :src="imageUrl" alt="Full size image"
            class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" />
          <button v-if="hasPrev" @click="prevImage"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors bg-black/50 p-3 rounded-full hover:bg-black/70">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button v-if="hasNext" @click="nextImage"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors bg-black/50 p-3 rounded-full hover:bg-black/70">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          <div v-if="totalImages > 1"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-sm bg-black/50 px-3 py-1 rounded-full">
            {{ currentIndex + 1 }} / {{ totalImages }}
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps<{
  isOpen: boolean
  imageUrl: string
  images?: string[]
  currentIndex?: number
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'next'): void
  (e: 'prev'): void
}>()

const totalImages = computed(() => props.images?.length || 0)
const hasPrev = computed(() => (props.images?.length || 0) > 0 && (props.currentIndex || 0) > 0)
const hasNext = computed(() => (props.images?.length || 0) > 0 && (props.currentIndex || 0) < (props.images?.length || 0) - 1)

function close() {
  emit('close')
}

function nextImage() {
  emit('next')
}

function prevImage() {
  emit('prev')
}

// Keyboard navigation
function handleKeydown(event: KeyboardEvent) {
  if (!props.isOpen) return
  if (event.key === 'Escape') close()
  if (event.key === 'ArrowRight') nextImage()
  if (event.key === 'ArrowLeft') prevImage()
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})

// Watch for isOpen changes to handle body scroll lock
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})
</script>
