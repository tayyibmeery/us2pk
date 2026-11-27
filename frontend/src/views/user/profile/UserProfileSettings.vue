<!-- frontend/src/views/user/profile/UserProfileSettings.vue -->
<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Security</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your account password</p>
      </div>
      <router-link to="/user/profile"
        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Profile
      </router-link>
    </div>

    <div
      class="max-w-xl bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center gap-2 mb-1">
        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Change Password</h3>
      </div>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Use a strong password you don't reuse on other sites.</p>

      <form @submit.prevent="updatePassword" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
          <input v-model="passwordForm.current_password" type="password"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
            placeholder="Enter current password" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
          <input v-model="passwordForm.new_password" type="password"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
            placeholder="At least 8 characters" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
          <input v-model="passwordForm.new_password_confirmation" type="password"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
            placeholder="Confirm new password" required />
        </div>

        <div v-if="passwordError"
          class="text-sm text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 px-3 py-2 rounded-lg">
          {{ passwordError }}
        </div>
        <div v-if="passwordSuccess"
          class="text-sm text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400 px-3 py-2 rounded-lg">
          {{ passwordSuccess }}
        </div>

        <button type="submit" :disabled="submitting"
          class="w-full px-4 py-2.5 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition disabled:opacity-50 shadow-sm hover:shadow-md">
          {{ submitting ? 'Updating...' : 'Update Password' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast } from '@/composables/useToast'

const authStore = useAuthStore()
const toast = useToast()

const submitting = ref(false)
const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})
const passwordError = ref('')
const passwordSuccess = ref('')

const updatePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    passwordError.value = 'New passwords do not match'
    toast.warning(passwordError.value)
    return
  }
  if (passwordForm.new_password.length < 8) {
    passwordError.value = 'New password must be at least 8 characters'
    toast.warning(passwordError.value)
    return
  }

  submitting.value = true
  try {
    await authStore.changePassword(
      passwordForm.current_password,
      passwordForm.new_password,
      passwordForm.new_password_confirmation
    )
    passwordSuccess.value = 'Password updated successfully!'
    toast.success(passwordSuccess.value)
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
  } catch (error: any) {
    passwordError.value = error.response?.data?.message || 'Failed to update password'
    toast.error(passwordError.value)
  } finally {
    submitting.value = false
  }
}
</script>
