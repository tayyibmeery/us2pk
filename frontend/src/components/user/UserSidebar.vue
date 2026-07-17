<template>
  <aside :class="[
    'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200',
    {
      'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
      'lg:w-[90px]': !isExpanded && !isHovered,
      'translate-x-0 w-[290px]': isMobileOpen,
      '-translate-x-full': !isMobileOpen,
      'lg:translate-x-0': true,
    },
  ]" @mouseenter="!isExpanded && (isHovered = true)" @mouseleave="isHovered = false">

    <!-- Logo -->
    <div :class="[
      'py-8 flex',
      !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
    ]">
      <router-link to="/user/dashboard">
        <img v-if="isExpanded || isHovered || isMobileOpen" class="dark:hidden" src="/images/logo/logo.png" alt="Logo"
          width="150" height="40" />
        <img v-if="isExpanded || isHovered || isMobileOpen" class="hidden dark:block" src="/images/logo/logo-dark.svg"
          alt="Logo" width="150" height="40" />
        <img v-else src="/images/logo/logo-icon.png" alt="Logo" width="32" height="32" />
      </router-link>
    </div>

    <!-- User Info -->
    <div :class="[
      'flex items-center px-3 pb-4 mb-4 border-b border-gray-200 dark:border-gray-800',
      !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
    ]">
      <div class="relative w-10 h-10 overflow-hidden rounded-full flex-shrink-0">
        <img :src="userAvatar" alt="User avatar" class="object-cover w-full h-full" @error="handleImageError" />
      </div>
      <div v-if="isExpanded || isHovered || isMobileOpen" class="ml-3 min-w-0">
        <p class="font-medium text-gray-800 dark:text-white truncate">{{ user?.name || 'User' }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user?.email || '' }}</p>
      </div>
    </div>

    <!-- Navigation -->
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
      <nav class="mb-6">
        <div class="flex flex-col">
          <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex" class="mb-4">
            <h2 :class="[
              'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
              !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
            ]">
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <span v-else class="w-5 h-5 text-gray-400">•••</span>
            </h2>
            <ul class="flex flex-col">
              <li v-for="item in menuGroup.items" :key="item.name" class="mb-1">
                <router-link :to="item.path" :class="[
                  'flex items-center px-3 py-2 rounded-lg text-sm transition-all duration-200',
                  isActive(item.path)
                    ? 'bg-brand-50 dark:bg-brand-900/20 text-brand-600 dark:text-brand-400'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
                  !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
                ]">
                  <span :class="[
                    isActive(item.path)
                      ? 'text-brand-600 dark:text-brand-400'
                      : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-white',
                  ]">
                    <component :is="item.icon" />
                  </span>
                  <span v-if="isExpanded || isHovered || isMobileOpen" class="ml-3 font-medium">{{ item.name }}</span>
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>

    <!-- Footer - Sign Out -->
    <div class="mt-auto pb-4 border-t border-gray-200 dark:border-gray-800 pt-4">
      <button @click="handleLogout" :class="[
        'flex items-center w-full px-3 py-2 rounded-lg text-sm transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400',
        !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
      ]">
        <span class="text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </span>
        <span v-if="isExpanded || isHovered || isMobileOpen" class="ml-3 font-medium">Sign Out</span>
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useSidebar } from '@/composables/useSidebar'
import {
  GridIcon,
  BoxCubeIcon,
  UserCircleIcon,
  SettingsIcon,
  SearchIcon,
} from '@/icons'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { isExpanded, isMobileOpen, isHovered } = useSidebar()

const user = computed(() => authStore.user)

const menuGroups = [
  {
    title: "Main",
    items: [
      { icon: GridIcon, name: "Dashboard", path: "/user/dashboard" },
      { icon: BoxCubeIcon, name: "My Shipments", path: "/user/my-shipments" },
      { icon: SearchIcon, name: "Track Shipment", path: "/user/track-shipment" },
    ],
  },
  {
    title: "Account",
    items: [
      { icon: UserCircleIcon, name: "Profile", path: "/user/profile" },
      { icon: SettingsIcon, name: "Settings", path: "/user/settings" },
    ],
  },
]

const isActive = (path: string) => route.path === path

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

const handleLogout = async () => {
  await authStore.logout()
  router.push('/signin')
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
