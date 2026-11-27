<template>
  <div class="relative" ref="dropdownRef">
    <!-- Toggle Button -->
    <button class="flex items-center text-gray-700 dark:text-gray-400" @click.prevent="toggleDropdown">
      <span class="mr-3 overflow-hidden rounded-full h-11 w-11">
        <img :src="avatarUrl" alt="User avatar" class="object-cover w-full h-full" @error="handleImageError" />
      </span>
      <span class="block mr-1 font-medium text-theme-sm">{{ user?.name || 'User' }}</span>
      <ChevronDownIcon :class="{ 'rotate-180': dropdownOpen }" />
    </button>

    <!-- Dropdown -->
    <div v-if="dropdownOpen"
      class="absolute right-0 mt-[17px] flex w-[280px] flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
      <!-- User Avatar & Name (compact) -->
      <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-800">
        <div class="relative w-14 h-14 group">
          <div class="overflow-hidden rounded-full border border-gray-200 dark:border-gray-800 h-full w-full">
            <img :src="avatarUrl" alt="User avatar" class="object-cover w-full h-full" @error="handleImageError" />
          </div>
          <!-- Upload overlay -->
          <label for="avatar-upload-dropdown"
            class="absolute inset-0 flex items-center justify-center transition-opacity bg-black/40 rounded-full opacity-0 cursor-pointer group-hover:opacity-100">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </label>
          <input id="avatar-upload-dropdown" type="file" accept="image/*" class="hidden"
            @change="uploadAvatar($event)" />
        </div>

        <div>
          <p class="font-medium text-gray-700 text-theme-sm dark:text-gray-300">
            {{ user?.name || 'User' }}
          </p>
          <p class="text-theme-xs text-gray-500 dark:text-gray-400">
            {{ user?.email || 'no-email@example.com' }}
          </p>
          <p class="text-xs text-gray-400 dark:text-gray-500">
            {{ user?.city?.city_name || '' }}
          </p>
        </div>
      </div>

      <!-- Dropdown Menu Items - Fixed routes -->
      <ul class="flex flex-col gap-1 pt-3">
        <li>
          <router-link :to="profileRoute"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
            <UserCircleIcon class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
            Edit profile
          </router-link>
        </li>
        <li>
          <router-link :to="settingsRoute"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
            <SettingsIcon class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
            Account settings
          </router-link>
        </li>
        <li v-if="!isAdmin">
          <router-link to="/user/my-shipments"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            My Shipments
          </router-link>
        </li>
        <!-- <li v-if="isAdmin">
          <router-link to="/admin/users"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Users
          </router-link>
        </li> -->
      </ul>

      <!-- Sign Out -->
      <button @click="signOut"
        class="flex items-center gap-3 px-3 py-2 mt-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
        <LogoutIcon class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
        Sign out
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { UserCircleIcon, ChevronDownIcon, LogoutIcon, SettingsIcon } from '@/icons'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const dropdownOpen = ref(false)
const dropdownRef = ref(null)

const user = computed(() => authStore.user)
const isAdmin = computed(() => authStore.isAdmin)

// ✅ FIX: Dynamic routes based on user role
const profileRoute = computed(() => {
  return isAdmin.value ? '/admin/profile' : '/user/profile'
})

const settingsRoute = computed(() => {
  return isAdmin.value ? '/admin/settings' : '/user/settings'
})

const avatarUrl = computed(() => {
  if (user.value?.avatar) {
    if (user.value.avatar.startsWith('avatars/')) {
      return `${import.meta.env.VITE_BASE_URL || ''}/storage/${user.value.avatar}`
    }
    return user.value.avatar
  }
  return '/images/user/owner.jpg'
})

const handleImageError = (e) => {
  if (e.target.src.includes('owner.jpg')) {
    e.target.onerror = null;
    return;
  }
  e.target.src = '/images/user/owner.jpg'
}

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const signOut = async () => {
  await authStore.logout()
  closeDropdown()
}

const uploadAvatar = async (event) => {
  const input = event.target
  if (!input.files?.length) return

  const file = input.files[0]
  try {
    await authStore.updateAvatar(file)
    alert('Avatar updated successfully!')
    closeDropdown()
  } catch (err) {
    alert(err.response?.data?.message || 'Avatar upload failed')
  } finally {
    input.value = ''
  }
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
