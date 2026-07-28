<!-- frontend/src/components/user/profile/UserSettings.vue -->
<template>
  <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Change Password -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
        </svg>
        Change Password
      </h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Update your password to keep your account secure.</p>
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
            placeholder="Enter new password" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
          <input v-model="passwordForm.new_password_confirmation" type="password"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
            placeholder="Confirm new password" required />
        </div>
        <div v-if="passwordError" class="text-sm text-red-500 bg-red-50 dark:bg-red-900/20 p-2 rounded-lg">
          {{ passwordError }}</div>
        <div v-if="passwordSuccess" class="text-sm text-green-500 bg-green-50 dark:bg-green-900/20 p-2 rounded-lg">
          {{ passwordSuccess }}</div>
        <button type="submit" :disabled="submitting"
          class="w-full px-4 py-2.5 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition disabled:opacity-50 shadow-sm hover:shadow-md">
          {{ submitting ? 'Updating...' : 'Update Password' }}
        </button>
      </form>
    </div>

    <!-- Account Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        </svg>
        Account Actions
      </h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Manage your account and data.</p>
      <div class="space-y-3">
        <button @click="exportData"
          class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition group">
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:text-gray-400" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export My Data
          </span>
          <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
        <button @click="deleteAccount"
          class="flex items-center justify-between w-full px-4 py-3 text-sm font-medium text-red-600 border border-red-200 dark:border-red-800/50 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition group">
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500 group-hover:text-red-600" fill="none" stroke="currentColor"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete Account
          </span>
          <svg class="w-4 h-4 text-red-400 group-hover:text-red-600" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast } from '@/composables/useToast'

const authStore = useAuthStore()
const toast = useToast()

const submitting = ref(false)
const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})
const passwordError = ref('')
const passwordSuccess = ref('')

const updatePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    passwordError.value = 'New passwords do not match'
    toast.warning('⚠️ ' + passwordError.value)
    return
  }

  if (passwordForm.value.new_password.length < 8) {
    passwordError.value = 'New password must be at least 8 characters'
    toast.warning('⚠️ ' + passwordError.value)
    return
  }

  submitting.value = true
  try {
    await authStore.changePassword(
      passwordForm.value.current_password,
      passwordForm.value.new_password,
      passwordForm.value.new_password_confirmation
    )
    passwordSuccess.value = 'Password updated successfully!'
    toast.success('✅ Password updated successfully!')
    passwordForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
    }
  } catch (error: any) {
    passwordError.value = error.response?.data?.message || 'Failed to update password'
    toast.error('❌ ' + passwordError.value)
  } finally {
    submitting.value = false
  }
}

const exportData = () => {
  toast.info('ℹ️ Export data functionality coming soon!')
}

const deleteAccount = () => {
  if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    toast.warning('⚠️ Account deletion functionality coming soon!')
  }
}
</script>

