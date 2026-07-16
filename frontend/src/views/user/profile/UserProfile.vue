<template>
  <UserLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800 dark:text-white">My Profile</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Manage your personal information</p>
        </div>
        <button @click="openEditModal"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
          </svg>
          Edit Profile
        </button>
      </div>

      <!-- Profile Card -->
      <div
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">
            <!-- Avatar -->
            <div class="relative">
              <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-brand-500">
                <img :src="user?.avatar_url || '/images/user/owner.jpg'" alt="Profile"
                  class="w-full h-full object-cover" />
              </div>
              <label for="avatar-upload"
                class="absolute bottom-0 right-0 p-1.5 bg-brand-500 rounded-full cursor-pointer hover:bg-brand-600 transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </label>
              <input id="avatar-upload" type="file" accept="image/*" class="hidden" @change="uploadAvatar" />
            </div>
            <div class="flex-1 text-center sm:text-left">
              <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ user?.name || 'N/A' }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.email || 'N/A' }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.phone || 'N/A' }}</p>
              <div class="flex flex-wrap items-center gap-3 mt-3">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  {{ user?.role || 'User' }}
                </span>
                <span class="text-sm text-gray-400">|</span>
                <span class="text-sm text-gray-500">{{ user?.city?.city_name || 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Info Grid -->
        <div class="p-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full Name</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ user?.name || 'N/A' }}</span>
            </div>
            <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email Address</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ user?.email || 'N/A' }}</span>
            </div>
            <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone Number</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ user?.phone || 'N/A' }}</span>
            </div>
            <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">PCode</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ user?.pcode || 'N/A' }}</span>
            </div>
            <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ user?.city?.city_name || 'N/A' }}</span>
            </div>
            <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
              <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</span>
              <span class="font-medium text-gray-800 dark:text-white">{{ user?.address || 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'
import UserLayout from '@/views/user/layouts/UserLayout.vue'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const uploadAvatar = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  const formData = new FormData()
  formData.append('avatar', file)

  try {
    await api.put('/user/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    await authStore.fetchUser()
    alert('Avatar updated successfully!')
  } catch (error) {
    alert('Failed to update avatar')
  } finally {
    input.value = ''
  }
}

const openEditModal = () => {
  // Implement edit profile modal
  alert('Edit profile functionality coming soon!')
}
</script>
