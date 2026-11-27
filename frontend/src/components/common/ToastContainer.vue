<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-[9999] pointer-events-none flex justify-center pt-20 md:pt-24">
      <div class="flex flex-col gap-3 w-full max-w-md px-4 pointer-events-none">
        <TransitionGroup enter-active-class="transition-all duration-500 ease-out"
          enter-from-class="opacity-0 -translate-y-8 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition-all duration-300 ease-in"
          leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 -translate-y-8 scale-95">
          <div v-for="toast in toasts" :key="toast.id"
            class="pointer-events-auto relative overflow-hidden rounded-2xl p-5 shadow-2xl border transition-all"
            :class="getToastClasses(toast.type)" role="alert">
            <div class="flex items-start gap-4">
              <!-- Icon with gradient background -->
              <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                :class="getIconBgClasses(toast.type)">
                <svg v-if="toast.type === 'success'" class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else-if="toast.type === 'error'" class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <svg v-else-if="toast.type === 'warning'" class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>

              <!-- Message -->
              <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm font-semibold" :class="getTextClasses(toast.type)">
                  {{ toast.message }}
                </p>
              </div>

              <!-- Close button -->
              <button @click="removeToast(toast.id)"
                class="flex-shrink-0 -mt-1 -mr-1 p-1.5 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition">
                <svg class="w-4 h-4" :class="getCloseIconClasses(toast.type)" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Progress bar -->
            <div class="absolute bottom-0 left-0 h-1 rounded-b-full transition-all duration-100 ease-linear"
              :class="getProgressClasses(toast.type)" :style="{ width: `${getProgressWidth(toast.id)}%` }" />
          </div>
        </TransitionGroup>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useToastStore } from '@/stores/toastStore'
import { onMounted, onUnmounted } from 'vue'

const toastStore = useToastStore()
const { toasts } = storeToRefs(toastStore)
const { removeToast } = toastStore

// Track progress for each toast
const progressMap = new Map<number, number>()
const progressIntervals = new Map<number, ReturnType<typeof setInterval>>()

function getToastClasses(type: string) {
  const base = 'bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl shadow-2xl'
  const borders = {
    success: 'border-green-200 dark:border-green-800 shadow-green-100/50 dark:shadow-green-900/30',
    error: 'border-red-200 dark:border-red-800 shadow-red-100/50 dark:shadow-red-900/30',
    warning: 'border-amber-200 dark:border-amber-800 shadow-amber-100/50 dark:shadow-amber-900/30',
    info: 'border-blue-200 dark:border-blue-800 shadow-blue-100/50 dark:shadow-blue-900/30',
  }
  return `${base} ${borders[type as keyof typeof borders] || borders.info}`
}

function getIconBgClasses(type: string) {
  const colors = {
    success: 'bg-gradient-to-br from-green-500 to-green-600',
    error: 'bg-gradient-to-br from-red-500 to-red-600',
    warning: 'bg-gradient-to-br from-amber-500 to-amber-600',
    info: 'bg-gradient-to-br from-blue-500 to-blue-600',
  }
  return colors[type as keyof typeof colors] || colors.info
}

function getTextClasses(type: string) {
  const colors = {
    success: 'text-green-800 dark:text-green-300',
    error: 'text-red-800 dark:text-red-300',
    warning: 'text-amber-800 dark:text-amber-300',
    info: 'text-blue-800 dark:text-blue-300',
  }
  return colors[type as keyof typeof colors] || colors.info
}

function getCloseIconClasses(type: string) {
  const colors = {
    success: 'text-green-500 dark:text-green-400',
    error: 'text-red-500 dark:text-red-400',
    warning: 'text-amber-500 dark:text-amber-400',
    info: 'text-blue-500 dark:text-blue-400',
  }
  return colors[type as keyof typeof colors] || colors.info
}

function getProgressClasses(type: string) {
  const colors = {
    success: 'bg-gradient-to-r from-green-400 to-green-600',
    error: 'bg-gradient-to-r from-red-400 to-red-600',
    warning: 'bg-gradient-to-r from-amber-400 to-amber-600',
    info: 'bg-gradient-to-r from-blue-400 to-blue-600',
  }
  return colors[type as keyof typeof colors] || colors.info
}

function getProgressWidth(id: number) {
  return progressMap.get(id) || 100
}

// Start progress tracking for new toasts
onMounted(() => {
  const interval = setInterval(() => {
    toasts.value.forEach(toast => {
      if (!progressMap.has(toast.id)) {
        progressMap.set(toast.id, 100)

        const duration = toast.duration || 3000
        const step = 50
        const totalSteps = duration / step
        let steps = 0

        const progressInterval = setInterval(() => {
          steps++
          const progress = Math.max(0, 100 - (steps / totalSteps) * 100)
          progressMap.set(toast.id, progress)

          if (steps >= totalSteps) {
            clearInterval(progressInterval)
            progressIntervals.delete(toast.id)
            progressMap.delete(toast.id)
          }
        }, step)

        progressIntervals.set(toast.id, progressInterval)
      }
    })
  }, 100)

  onUnmounted(() => {
    clearInterval(interval)
    progressIntervals.forEach(interval => clearInterval(interval))
    progressIntervals.clear()
    progressMap.clear()
  })
})
</script>
