// frontend/src/composables/useToast.ts
import { useToastStore } from '@/stores/toastStore'

export function useToast() {
  const toastStore = useToastStore()

  return {
    success: (message: string, duration?: number) => toastStore.success(message, duration),
    error: (message: string, duration?: number) => toastStore.error(message, duration),
    warning: (message: string, duration?: number) => toastStore.warning(message, duration),
    info: (message: string, duration?: number) => toastStore.info(message, duration),
  }
}
