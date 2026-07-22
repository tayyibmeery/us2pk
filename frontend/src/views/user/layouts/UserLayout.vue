<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside
      class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700">
          <router-link to="/user/dashboard" class="flex items-center">
            <img src="/images/logo/logo.png" class="h-8 mr-2" alt="US2PK Logo" />
            <!-- <span class="text-xl font-bold text-gray-800 dark:text-white">US2PK</span> -->
          </router-link>
        </div>

        <!-- User Info -->
        <div class="flex items-center p-4 border-b border-gray-200 dark:border-gray-700">
          <div class="w-10 h-10 rounded-full overflow-hidden">
            <img :src="userAvatar" alt="User avatar" class="w-full h-full object-cover" @error="handleImageError" />
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ user?.name || 'User' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ user?.email || '' }}</p>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto">
          <ul class="space-y-1">
            <li>
              <router-link to="/user/dashboard"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="[
                  $route.path === '/user/dashboard'
                    ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400'
                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                ]">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
              </router-link>
            </li>
            <li>
              <router-link to="/user/my-shipments"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="[
                  $route.path.startsWith('/user/my-shipments')
                    ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400'
                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                ]">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                My Shipments
              </router-link>
            </li>
            <li>
              <router-link to="/user/track-shipment"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="[
                  $route.path === '/user/track-shipment'
                    ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400'
                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                ]">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Track Shipment
              </router-link>
            </li>
            <li>
              <router-link to="/user/profile"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="[
                  $route.path === '/user/profile' || $route.path === '/user/settings'
                    ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400'
                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                ]">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
              </router-link>
            </li>
            <li>
              <router-link to="/user/settings"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="[
                  $route.path === '/user/settings'
                    ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400'
                    : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                ]">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                Settings
              </router-link>
            </li>
          </ul>
        </nav>

        <!-- Footer - Sign Out -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
          <button @click="logout"
            class="flex items-center w-full px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Sign Out
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
      <!-- Header -->
      <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between px-4 py-3">
          <div>
            <h1 class="text-lg font-semibold text-gray-800 dark:text-white">{{ pageTitle }}</h1>
          </div>
          <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600 dark:text-gray-300">{{ user?.name || 'User' }}</span>
            <button @click="logout" class="px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
              Logout
            </button>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <div class="p-4 md:p-6">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

console.log('✅ UserLayout loaded!')

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const user = computed(() => authStore.user)

const pageTitle = computed(() => {
  return (route.meta?.title as string) || 'Dashboard'
})

const userAvatar = computed(() => {
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

const logout = async () => {
  await authStore.logout()
  router.push('/signin')
}

onMounted(() => {
  console.log('✅ User mounted, user:', user.value)
})
</script>
