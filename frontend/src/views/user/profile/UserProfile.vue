<template>
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

    <!-- Show user data directly from auth store -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center gap-4 mb-4">
        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-brand-500">
          <img :src="avatarUrl" alt="Profile" class="w-full h-full object-cover" @error="handleImageError" />
        </div>
        <div>
          <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ user?.name || 'N/A' }}</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.email || 'N/A' }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.phone || 'N/A' }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">Full Name</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.name || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.email || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.phone || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">City</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.city?.city_name || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">PCode</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.pcode || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">Role</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.role || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg md:col-span-2">
          <p class="text-xs text-gray-500 dark:text-gray-400">Address</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ user?.address || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
          <p class="font-medium text-emerald-600">{{ user?.status || 'N/A' }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
          <p class="text-xs text-gray-500 dark:text-gray-400">Member Since</p>
          <p class="font-medium text-gray-800 dark:text-white">{{ formatDate(user?.created_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'

console.log('🔵 UserProfile component is loading!')

const authStore = useAuthStore()
const user = computed(() => authStore.user)

console.log('🔵 User data in profile:', user.value)

const avatarUrl = computed(() => {
  if (user.value?.avatar) {
    if (user.value.avatar.startsWith('avatars/')) {
      return `${import.meta.env.VITE_BASE_URL || ''}/storage/${user.value.avatar}`
    }
    return user.value.avatar
  }
  return '/images/user/owner.jpg'
})

const handleImageError = (e: Event) => {
  const img = e.target as HTMLImageElement
  img.src = '/images/user/owner.jpg'
}

const formatDate = (date: string | undefined) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

const openEditModal = () => {
  alert('Edit profile functionality coming soon!')
}
</script>
