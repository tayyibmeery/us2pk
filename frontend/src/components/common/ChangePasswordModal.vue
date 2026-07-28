<!-- frontend/src/components/common/ChangePasswordModal.vue -->
<template>
  <Modal :isOpen="isOpen" @close="close" size="md">
    <template #body>
      <div class="relative w-full max-w-md overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900">
        <button @click="close"
          class="transition-color absolute right-4 top-4 z-999 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="currentColor" />
          </svg>
        </button>

        <!-- Icon header -->
        <div class="px-2 pr-10">
          <div class="flex items-center justify-center w-12 h-12 rounded-full bg-brand-50 dark:bg-brand-500/10 mb-4">
            <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
          </div>
          <h4 class="mb-1 text-xl font-semibold text-gray-800 dark:text-white/90">Change Password</h4>
          <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Use a strong password you don't reuse elsewhere.</p>
        </div>

        <form @submit.prevent="submit" class="flex flex-col">
          <div class="space-y-4 px-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Current Password</label>
              <input v-model="form.current_password" type="password"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                placeholder="Enter current password" />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">New Password</label>
              <input v-model="form.new_password" type="password"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                placeholder="At least 8 characters" />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm New
                Password</label>
              <input v-model="form.new_password_confirmation" type="password"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                placeholder="Confirm new password" />
            </div>
          </div>

          <div v-if="error"
            class="mt-4 mx-2 flex items-start gap-2 rounded-lg bg-red-50 dark:bg-red-900/20 p-3 text-sm text-red-600 dark:text-red-400">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ error }}
          </div>
          <div v-if="success"
            class="mt-4 mx-2 flex items-start gap-2 rounded-lg bg-green-50 dark:bg-green-900/20 p-3 text-sm text-green-600 dark:text-green-400">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ success }}
          </div>

          <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
            <button @click="close" type="button"
              class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
              Cancel
            </button>
            <button type="submit" :disabled="loading"
              class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 sm:w-auto">
              {{ loading ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import Modal from '@/components/Modal.vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast } from '@/composables/useToast'

defineProps<{ isOpen: boolean }>()
const emit = defineEmits(['close'])

const authStore = useAuthStore()
const toast = useToast()
const loading = ref(false)
const error = ref('')
const success = ref('')

const form = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const close = () => {
  form.current_password = ''
  form.new_password = ''
  form.new_password_confirmation = ''
  error.value = ''
  success.value = ''
  emit('close')
}

const submit = async () => {
  error.value = ''
  success.value = ''

  if (form.new_password !== form.new_password_confirmation) {
    error.value = 'Passwords do not match'
    toast.warning(error.value)
    return
  }
  if (form.new_password.length < 8) {
    error.value = 'Password must be at least 8 characters'
    toast.warning(error.value)
    return
  }

  loading.value = true
  try {
    await authStore.changePassword(form.current_password, form.new_password, form.new_password_confirmation)
    success.value = 'Password changed successfully!'
    toast.success(success.value)
    setTimeout(close, 1200)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to change password'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}
</script>
