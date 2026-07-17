<template>
  <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">
    <!-- Avatar -->
    <div class="relative">
      <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-brand-500">
        <img :src="profileStore.userAvatar" alt="Profile" class="w-full h-full object-cover"
          @error="handleImageError" />
      </div>
      <label for="avatar-upload"
        class="absolute bottom-0 right-0 p-1.5 bg-brand-500 rounded-full cursor-pointer hover:bg-brand-600 transition"
        title="Upload new avatar">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </label>
      <input id="avatar-upload" type="file" accept="image/*" class="hidden" @change="handleAvatarUpload" />
    </div>

    <!-- User Info -->
    <div class="flex-1 text-center sm:text-left">
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ profileStore.profile?.name || 'N/A' }}</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ profileStore.profile?.email || 'N/A' }}</p>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ profileStore.profile?.phone || 'N/A' }}</p>
      <div class="flex flex-wrap items-center gap-3 mt-3">
        <span
          class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          {{ profileStore.profile?.role || 'User' }}
        </span>
        <span class="text-sm text-gray-400 dark:text-gray-500">|</span>
        <span
          class="text-sm text-gray-500 dark:text-gray-400">{{ profileStore.profile?.city?.city_name || 'N/A' }}</span>
        <span class="text-sm text-gray-400 dark:text-gray-500">|</span>
        <span class="text-sm text-gray-500 dark:text-gray-400">PCode: {{ profileStore.profile?.pcode || 'N/A' }}</span>
      </div>
    </div>

    <!-- Edit Button -->
    <div class="flex-shrink-0">
      <button @click="$emit('edit')"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        Edit Profile
      </button>
    </div>

    <!-- Upload Status -->
    <div v-if="profileStore.avatarUploading" class="mt-4 text-center text-sm text-brand-500">Uploading avatar...</div>
    <div v-if="profileStore.error" class="mt-4 text-center text-sm text-red-500">{{ profileStore.error }}</div>
  </div>
</template>

<script setup lang="ts">
import { useUserProfileStore } from '@/stores/userProfileStore'

const emit = defineEmits<{
  (e: 'edit'): void
  (e: 'avatar-updated'): void
}>()

const profileStore = useUserProfileStore()

const handleImageError = (e: Event) => {
  const img = e.target as HTMLImageElement
  img.src = '/images/user/owner.jpg'
}

const handleAvatarUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  try {
    await profileStore.uploadAvatar(file)
    emit('avatar-updated')
  } catch (error: any) {
    console.error('Avatar upload failed:', error)
  } finally {
    input.value = ''
  }
}
</script>
