<template>
  <UserLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Settings</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Manage your account settings and preferences</p>
        </div>
      </div>

      <!-- Settings Cards -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Change Password -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Change Password</h3>
          <form @submit.prevent="updatePassword" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
              <input v-model="passwordForm.current_password" type="password"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                placeholder="Enter current password" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
              <input v-model="passwordForm.new_password" type="password"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                placeholder="Enter new password" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New
                Password</label>
              <input v-model="passwordForm.new_password_confirmation" type="password"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                placeholder="Confirm new password" required />
            </div>
            <div v-if="passwordError" class="text-sm text-red-500">{{ passwordError }}</div>
            <div v-if="passwordSuccess" class="text-sm text-green-500">{{ passwordSuccess }}</div>
            <button type="submit" :disabled="passwordSubmitting"
              class="w-full px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition disabled:opacity-50">
              {{ passwordSubmitting ? 'Updating...' : 'Update Password' }}
            </button>
          </form>
        </div>

        <!-- Preferences -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Preferences</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-700 dark:text-gray-300">Email Notifications</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Receive email updates about your shipments</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="preferences.email_notifications" class="sr-only peer" />
                <div
                  class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500">
                </div>
              </label>
            </div>
            <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
              <div>
                <p class="font-medium text-gray-700 dark:text-gray-300">SMS Notifications</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Receive SMS updates about your shipments</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="preferences.sms_notifications" class="sr-only peer" />
                <div
                  class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500">
                </div>
              </label>
            </div>
            <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
              <div>
                <p class="font-medium text-gray-700 dark:text-gray-300">Dark Mode</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Switch between light and dark theme</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="preferences.dark_mode" class="sr-only peer" />
                <div
                  class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500">
                </div>
              </label>
            </div>
            <button @click="savePreferences"
              class="w-full mt-4 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition">
              Save Preferences
            </button>
          </div>
        </div>

        <!-- Account Actions -->
        <div
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm lg:col-span-2">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Account Actions</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <button @click="exportData"
              class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Export Data
            </button>
            <button @click="deleteAccount"
              class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-red-600 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Delete Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'
import UserLayout from '@/views/user/layouts/UserLayout.vue'

const router = useRouter()
const authStore = useAuthStore()

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const passwordSubmitting = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const preferences = reactive({
  email_notifications: true,
  sms_notifications: false,
  dark_mode: false,
})

const updatePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    passwordError.value = 'New passwords do not match'
    return
  }

  if (passwordForm.new_password.length < 8) {
    passwordError.value = 'New password must be at least 8 characters'
    return
  }

  passwordSubmitting.value = true
  try {
    await api.post('/user/change-password', passwordForm)
    passwordSuccess.value = 'Password updated successfully!'
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
  } catch (error: any) {
    passwordError.value = error.response?.data?.message || 'Failed to update password'
  } finally {
    passwordSubmitting.value = false
  }
}

const savePreferences = () => {
  // Save preferences logic
  alert('Preferences saved successfully!')
}

const exportData = () => {
  alert('Export data functionality coming soon!')
}

const deleteAccount = () => {
  if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    alert('Account deletion functionality coming soon!')
  }
}
</script>
