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
          <input name="avatar" id="avatar-upload" type="file" accept="image/*" class="hidden" @change="uploadAvatar($event)" />
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
      <!-- <button @click="$emit('edit')"
        class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 lg:inline-flex lg:w-auto">
        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
            fill="" />
        </svg>
        Edit
      </button> -->
    </div>
  </div>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const authStore = useAuthStore()


const user = computed(() => authStore.user)

const avatarUrl = computed(() => {
  if (user.value?.avatar) {
    if (user.value.avatar.startsWith('avatars/')) {
      return `${import.meta.env.VITE_API_BASE_URL}/storage/${user.value.avatar}`
    }
    return user.value.avatar
  }
  return '/images/user/owner.jpg'
})

const handleImageError = (e: Event) => {
  const img = e.target as HTMLImageElement
  img.src = '/images/user/owner.jpg'
}

const emit = defineEmits(['edit', 'avatar-updated'])

const uploadAvatar = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  const formData = new FormData()
  formData.append('avatar', file)
  formData.append('_method', 'PUT')  // 👈 Laravel will treat this as PUT

  try {
    const response = await api.post('/user/avatar', formData, {
      headers: {
        'Content-Type': undefined,   // Let Axios set the correct boundary
      }
    })
    await authStore.fetchUser()
    emit('avatar-updated')
    alert('Avatar updated successfully!')
  } catch (err: any) {
    alert(err.response?.data?.message || 'Avatar upload failed')
  } finally {
    input.value = ''
  }
}
</script>
