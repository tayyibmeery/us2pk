<template>
  <aside :class="[
    'fixed mt-16 flex flex-col lg:mt-0 top-0 left-0 h-screen bg-white dark:bg-gray-900 text-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 ease-in-out z-99999',
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
      'flex items-center h-16 lg:h-20 px-5 shrink-0',
      !isExpanded && !isHovered ? 'lg:justify-center lg:px-0' : 'justify-start',
    ]">
      <router-link to="/user/dashboard" class="flex items-center">
        <img v-if="isExpanded || isHovered || isMobileOpen" class="dark:hidden" src="/images/logo/logo.png" alt="Logo"
          width="140" height="36" />
        <img v-if="isExpanded || isHovered || isMobileOpen" class="hidden dark:block" src="/images/logo/logo-dark.svg"
          alt="Logo" width="140" height="36" />
        <img v-else src="/images/logo/logo-icon.png" alt="Logo" width="30" height="30" />
      </router-link>
    </div>

    <!-- User Card -->
    <div class="px-3 pb-4">
      <div :class="[
        'flex items-center rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] transition-all',
        !isExpanded && !isHovered ? 'lg:justify-center lg:p-2' : 'p-2.5',
      ]">
        <div class="relative shrink-0">
          <div class="w-9 h-9 rounded-full overflow-hidden ring-2 ring-white dark:ring-gray-900 shadow-sm">
            <img :src="userAvatar" alt="User avatar" class="object-cover w-full h-full" @error="handleImageError" />
          </div>
          <span
            class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-900" />
        </div>
        <div v-if="isExpanded || isHovered || isMobileOpen" class="ml-2.5 min-w-0">
          <p class="text-sm font-semibold text-gray-800 dark:text-white truncate leading-tight">
            {{ user?.name || 'User' }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user?.email || '' }}</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto no-scrollbar px-3">
      <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex" class="mb-5">
        <h2 v-if="isExpanded || isHovered || isMobileOpen"
          class="mb-2 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
          {{ menuGroup.title }}
        </h2>
        <div v-else class="mb-2 flex justify-center">
          <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600" />
        </div>

        <ul class="flex flex-col gap-0.5">
          <li v-for="item in menuGroup.items" :key="item.name">
            <router-link :to="item.path" :title="!isExpanded && !isHovered && !isMobileOpen ? item.name : undefined"
              :class="[
                'group relative flex items-center rounded-lg px-2.5 py-2.5 text-sm transition-all duration-150',
                isActive(item.path)
                  ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-medium'
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.04] hover:text-gray-800 dark:hover:text-gray-200',
                !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
              ]">
              <!-- active accent bar -->
              <span v-if="isActive(item.path)"
                class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-[3px] rounded-r-full bg-brand-500" />
              <span :class="[
                'shrink-0 transition-colors',
                isActive(item.path) ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300',
              ]">
                <component :is="item.icon" class="w-5 h-5" />
              </span>
              <span v-if="isExpanded || isHovered || isMobileOpen" class="ml-3 truncate">{{ item.name }}</span>
              <span v-if="item.badge && (isExpanded || isHovered || isMobileOpen)"
                class="ml-auto inline-flex items-center rounded-full bg-brand-500 px-1.5 py-0.5 text-[10px] font-semibold text-white">
                {{ item.badge }}
              </span>
            </router-link>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Footer - Sign Out -->
    <div class="shrink-0 px-3 pb-4 pt-3 border-t border-gray-100 dark:border-gray-800">
      <button @click="confirmLogout" :title="!isExpanded && !isHovered && !isMobileOpen ? 'Sign Out' : undefined"
        :class="[
          'group flex items-center w-full rounded-lg px-2.5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-600 dark:hover:text-red-400 transition-all duration-150',
          !isExpanded && !isHovered ? 'lg:justify-center' : 'lg:justify-start',
        ]">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span v-if="isExpanded || isHovered || isMobileOpen" class="ml-3">Sign Out</span>
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useSidebar } from '@/composables/useSidebar'
import { useToast } from '@/composables/useToast'
import {
  GridIcon,
  BoxCubeIcon,
  UserCircleIcon,
  SettingsIcon,
} from '@/icons'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()
const { isExpanded, isMobileOpen, isHovered } = useSidebar()

const user = computed(() => authStore.user)

const menuGroups = [
  {
    title: 'Main',
    items: [
      { icon: GridIcon, name: 'Dashboard', path: '/user/dashboard' },
      { icon: BoxCubeIcon, name: 'My Shipments', path: '/user/my-shipments' },
    ],
  },
  {
    title: 'Account',
    items: [
      { icon: UserCircleIcon, name: 'Profile', path: '/user/profile' },
      { icon: SettingsIcon, name: 'Security', path: '/user/settings' },
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

const confirmLogout = async () => {
  try {
    await authStore.logout()
    router.push('/signin')
  } catch {
    toast.error('Failed to sign out, please try again')
  }
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
