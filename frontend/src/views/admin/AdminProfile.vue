<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Profile</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your profile information</p>
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

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="w-8 h-8 border-4 border-gray-200 dark:border-gray-700 border-t-brand-500 rounded-full animate-spin">
      </div>
    </div>

    <!-- Profile Content -->
    <div v-else
      class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
      <!-- Profile Header with Avatar -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6">
          <!-- Avatar -->
          <div class="relative">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-brand-500">
              <img :src="avatarUrl" alt="Profile" class="w-full h-full object-cover" @error="handleImageError" />
            </div>
            <label for="avatar-upload-admin"
              class="absolute bottom-0 right-0 p-1.5 bg-brand-500 rounded-full cursor-pointer hover:bg-brand-600 transition"
              title="Upload new avatar">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </label>
            <input id="avatar-upload-admin" type="file" accept="image/*" class="hidden" @change="handleAvatarUpload" />
          </div>

          <!-- User Info -->
          <div class="flex-1 text-center sm:text-left">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ user?.name || 'N/A' }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.email || 'N/A' }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.phone || 'N/A' }}</p>
            <div class="flex flex-wrap items-center gap-3 mt-3">
              <span
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Admin
              </span>
              <span class="text-sm text-gray-400 dark:text-gray-500">|</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ user?.city?.city_name || 'N/A' }}</span>
              <span class="text-sm text-gray-400 dark:text-gray-500">|</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">PCode: {{ user?.pcode || 'N/A' }}</span>
            </div>
          </div>

          <!-- Edit Button -->
          <div class="flex-shrink-0">
            <button @click="openEditModal"
              class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
              Edit Profile
            </button>
          </div>
        </div>

        <!-- Upload Status -->
        <div v-if="uploading" class="mt-4 text-center text-sm text-brand-500">Uploading avatar...</div>
        <div v-if="uploadError" class="mt-4 text-center text-sm text-red-500">{{ uploadError }}</div>
      </div>

      <!-- Profile Information -->
      <div class="p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
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
          <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg sm:col-span-2">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</span>
            <span class="font-medium text-gray-800 dark:text-white">{{ user?.address || 'N/A' }}</span>
          </div>
          <div v-if="user?.bio" class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg sm:col-span-2">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bio</span>
            <span class="font-medium text-gray-800 dark:text-white">{{ user.bio }}</span>
          </div>
          <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member Since</span>
            <span class="font-medium text-gray-800 dark:text-white">{{ formatDate(user?.created_at) }}</span>
          </div>
          <div class="flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Status</span>
            <span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              {{ user?.status || 'Active' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Additional Admin Section -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Admin Information</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div class="flex flex-col p-3 bg-white dark:bg-gray-700/30 rounded-lg">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</span>
            <span class="font-medium text-gray-800 dark:text-white">Administrator</span>
          </div>
          <div class="flex flex-col p-3 bg-white dark:bg-gray-700/30 rounded-lg">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Type</span>
            <span class="font-medium text-gray-800 dark:text-white">Admin Panel Access</span>
          </div>
          <div class="flex flex-col p-3 bg-white dark:bg-gray-700/30 rounded-lg">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permissions</span>
            <span class="font-medium text-gray-800 dark:text-white">Full Access</span>
          </div>
          <div class="flex flex-col p-3 bg-white dark:bg-gray-700/30 rounded-lg">
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</span>
            <span class="font-medium text-gray-800 dark:text-white">{{ formatDate(user?.updated_at) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
const loading = ref(false)
const uploading = ref(false)
const uploadError = ref('')

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

const handleAvatarUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  const formData = new FormData()
  formData.append('avatar', file)

  uploading.value = true
  uploadError.value = ''

  try {
    await authStore.updateAvatar(file)
    await authStore.fetchUser()
    alert('Avatar updated successfully!')
  } catch (error: any) {
    uploadError.value = error.response?.data?.message || 'Failed to upload avatar'
  } finally {
    uploading.value = false
    input.value = ''
  }
}

const openEditModal = () => {
  alert('Edit profile functionality coming soon!')
}

const formatDate = (date: string | undefined) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(async () => {
  loading.value = true
  try {
    await authStore.fetchUser()
  } finally {
    loading.value = false
  }
})
</script>
