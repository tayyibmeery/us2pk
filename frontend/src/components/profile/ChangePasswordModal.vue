<template>
  <Modal :isOpen="isOpen" @close="close">
    <template #body>
      <div
        class="no-scrollbar relative w-full max-w-[500px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-8">
        <button @click="close"
          class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="" />
          </svg>
        </button>
        <div class="px-2 pr-14">
          <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Change Password</h4>
          <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
            Enter your current password and choose a new one.
          </p>
        </div>
        <form @submit.prevent="submit" class="flex flex-col">
          <div class="p-2 space-y-5">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Current Password
              </label>
              <input v-model="form.current_password" type="password"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                required />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                New Password
              </label>
              <input v-model="form.new_password" type="password"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                required />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Confirm New Password
              </label>
              <input v-model="form.new_password_confirmation" type="password"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                required />
            </div>
            <div v-if="error" class="text-sm text-red-500">{{ error }}</div>
            <div v-if="success" class="text-sm text-green-500">{{ success }}</div>
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
import Modal from '@/components/Modal.vue'          // ✅ correct import
import { useAuthStore } from '@/stores/authStore'

const props = defineProps<{ isOpen: boolean }>()
const emit = defineEmits(['close'])

const authStore = useAuthStore()
const loading = ref(false)
const error = ref('')
const success = ref('')

const form = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const close = () => {
  emit('close')
  Object.assign(form, { current_password: '', new_password: '', new_password_confirmation: '' })
  error.value = ''
  success.value = ''
}

const submit = async () => {
  error.value = '';
  success.value = '';
  loading.value = true;
  try {
    await authStore.changePassword(
      form.current_password,
      form.new_password,
      form.new_password_confirmation
    );
    success.value = 'Password updated successfully!';
    setTimeout(() => close(), 1500);
  } catch (err: any) {
    console.error('Change password error:', err);
    if (err.response) {
      // Server responded with an error
      const data = err.response.data;
      if (data.errors) {
        const firstKey = Object.keys(data.errors)[0];
        error.value = data.errors[firstKey][0];
      } else {
        error.value = data.message || 'Server error';
      }
    } else if (err.request) {
      // Request was sent but no response
      error.value = 'No response from server. Check your network.';
    } else {
      // Something else
      error.value = err.message || 'Failed to update password';
    }
  } finally {
    loading.value = false;
  }
};
</script>
