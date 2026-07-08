<template>
  <div class="fixed top-114 right-4 z-50 flex flex-col gap-2 max-w-sm w-full">
    <TransitionGroup name="toast">
      <div v-for="toast in toasts" :key="toast.id" class="rounded-lg p-4 shadow-lg transition-all duration-300" :class="{
        'bg-green-100 border-l-4 border-green-500 text-green-700': toast.type === 'success',
        'bg-red-100 border-l-4 border-red-500 text-red-700': toast.type === 'error',
        'bg-blue-100 border-l-4 border-blue-500 text-blue-700': toast.type === 'info',
        'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700': toast.type === 'warning',
      }">
        <div class="flex items-start gap-2">
          <span class="text-lg">
            <span v-if="toast.type === 'success'">✅</span>
            <span v-else-if="toast.type === 'error'">❌</span>
            <span v-else-if="toast.type === 'info'">ℹ️</span>
            <span v-else-if="toast.type === 'warning'">⚠️</span>
          </span>
          <span class="flex-1">{{ toast.message }}</span>
          <button @click="removeToast(toast.id)" class="text-gray-500 hover:text-gray-700">×</button>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useToastStore } from '@/stores/toastStore';

const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);
const { removeToast } = toastStore;
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
