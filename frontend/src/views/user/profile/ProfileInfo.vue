<template>
  <div class="p-6">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full Name</span>
        <span class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile?.name || 'N/A' }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email Address</span>
        <span class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile?.email || 'N/A' }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone Number</span>
        <span class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile?.phone || 'N/A' }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">PCode</span>
        <span class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile?.pcode || 'N/A' }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</span>
        <span
          class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile?.city?.city_name || 'N/A' }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg sm:col-span-2">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</span>
        <span class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile?.address || 'N/A' }}</span>
      </div>
      <div v-if="profileStore.profile?.bio"
        class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg sm:col-span-2">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bio</span>
        <span class="font-medium text-gray-800 dark:text-white">{{ profileStore.profile.bio }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member Since</span>
        <span
          class="font-medium text-gray-800 dark:text-white">{{ formatDate(profileStore.profile?.created_at) }}</span>
      </div>
      <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Status</span>
        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          {{ profileStore.profile?.status || 'Active' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useUserProfileStore } from '@/stores/userProfileStore'

const profileStore = useUserProfileStore()

const formatDate = (date: string | undefined) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}
</script>
