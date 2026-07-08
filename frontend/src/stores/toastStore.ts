import { defineStore } from 'pinia';

export interface Toast {
  id: number;
  type: 'success' | 'error' | 'info' | 'warning';
  message: string;
  duration?: number;
}

export const useToastStore = defineStore('toast', {
  state: () => ({
    toasts: [] as Toast[],
    nextId: 0,
  }),
  actions: {
    addToast(toast: Omit<Toast, 'id'>) {
      const id = this.nextId++;
      this.toasts.push({ ...toast, id });
      if (toast.duration !== 0) {
        setTimeout(() => this.removeToast(id), toast.duration || 3000);
      }
    },
    removeToast(id: number) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },
    success(message: string, duration = 3000) {
      this.addToast({ type: 'success', message, duration });
    },
    error(message: string, duration = 5000) {
      this.addToast({ type: 'error', message, duration });
    },
    info(message: string, duration = 3000) {
      this.addToast({ type: 'info', message, duration });
    },
    warning(message: string, duration = 4000) {
      this.addToast({ type: 'warning', message, duration });
    },
  },
});
