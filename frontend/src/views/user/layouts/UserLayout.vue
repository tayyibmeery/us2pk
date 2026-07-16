<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Top Navigation -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
      <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center justify-start">
            <button @click="toggleSidebar"
              class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 lg:hidden">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"></path>
              </svg>
            </button>
            <router-link to="/dashboard" class="flex ml-2 md:mr-24">
              <img src="/images/logo/logo.png" class="h-8 mr-3" alt="US2PK Logo" />
              <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">US2PK</span>
            </router-link>
          </div>
          <div class="flex items-center gap-3">
            <!-- Notification -->
            <button
              class="relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span
                class="absolute top-1 right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-semibold text-white bg-red-500 rounded-full">3</span>
            </button>
            <!-- User Menu -->
            <UserMenu />
          </div>
        </div>
      </div>
    </nav>

    <!-- Sidebar -->
    <aside :class="[
      'fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700',
      isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
      'lg:translate-x-0'
    ]">
      <div class="h-full px-3 pb-4 overflow-y-auto">
        <ul class="space-y-2 font-medium">
          <li v-for="item in menuItems" :key="item.path">
            <router-link :to="item.path"
              class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
              :class="{ 'bg-gray-100 dark:bg-gray-700': isActive(item.path) }">
              <component :is="item.icon"
                class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" />
              <span class="ml-3">{{ item.name }}</span>
              <span v-if="item.badge"
                class="inline-flex items-center justify-center px-2 ml-auto text-xs font-medium text-white bg-blue-600 rounded-full">{{ item.badge }}</span>
            </router-link>
          </li>
          <li>
            <button @click="logout"
              class="flex items-center w-full p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
              <svg
                class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <span class="ml-3">Sign Out</span>
            </button>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 lg:ml-64 pt-20">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import UserMenu from '@/components/layout/header/UserMenu.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const isSidebarOpen = ref(false)

const menuItems = [
  { name: 'Dashboard', path: '/dashboard', icon: 'svg-dashboard' },
  { name: 'My Shipments', path: '/my-shipments', icon: 'svg-shipments', badge: '5' },
  { name: 'Track Shipment', path: '/track-shipment', icon: 'svg-track' },
  { name: 'Profile', path: '/profile', icon: 'svg-profile' },
  { name: 'Settings', path: '/settings', icon: 'svg-settings' },
]

const isActive = (path: string) => route.path === path

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const logout = async () => {
  await authStore.logout()
  router.push('/signin')
}
</script>
