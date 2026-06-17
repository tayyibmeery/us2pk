<template>
  <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
      <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
        <!-- Avatar with overlay -->
        <div class="relative w-20 h-20 group">
          <div class="w-full h-full overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
            <img :src="avatarUrl" alt="user avatar" class="object-cover w-full h-full" @error="handleImageError" />
          </div>
          <!-- Overlay with upload trigger -->
          <label for="avatar-upload"
            class="absolute inset-0 flex items-center justify-center transition-opacity bg-black/40 rounded-full opacity-0 cursor-pointer group-hover:opacity-100">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </label>
          <input name="avatar" id="avatar-upload" type="file" accept="image/*" class="hidden"
            @change="uploadAvatar($event)" />
        </div>

        <div class="order-3 xl:order-2">
          <h4 class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
            {{ user?.name }}
          </h4>
          <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.bio || 'No bio' }}</p>
            <div class="hidden w-px h-3.5 bg-gray-300 dark:bg-gray-700 xl:block"></div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ user?.city?.city_name || 'Unknown city' }}, {{ user?.country || 'Pakistan' }}
            </p>
          </div>
        </div>
      </div>
      <!-- Edit button (commented out) -->
      <!-- <button @click="$emit('edit')" ... >Edit</button> -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const emit = defineEmits(['edit', 'avatar-updated'])

const avatarUrl = computed(() => {
  if (user.value?.avatar) {
    // If avatar is stored in 'storage/avatars/...', build the full URL
    if (user.value.avatar.startsWith('avatars/')) {
      return `${import.meta.env.VITE_BASE_URL}/storage/${user.value.avatar}`
    }
    // Otherwise assume it's a full URL or a relative path
    return user.value.avatar
  }
  // Fallback default avatar
  return '/images/user/owner.jpg'
})

const handleImageError = (e: Event) => {
  const img = e.target as HTMLImageElement
  img.src = '/images/user/owner.jpg'
}

const uploadAvatar = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  try {
    // ✅ Use the store action (which handles the API call and updates the user state)
    await authStore.updateAvatar(file)
    emit('avatar-updated')
    alert('Avatar updated successfully!')
  } catch (err: any) {
    alert(err.response?.data?.message || 'Avatar upload failed')
  } finally {
    input.value = '' // Reset input so same file can be re-uploaded
  }
}
</script>
