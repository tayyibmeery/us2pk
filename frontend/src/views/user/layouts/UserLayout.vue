<template>
  <div class="min-h-screen xl:flex">
    <UserSidebar />
    <Backdrop />
    <div class="flex-1 transition-all duration-300 ease-in-out"
      :class="[isExpanded || isHovered ? 'lg:ml-[290px]' : 'lg:ml-[90px]']">
      <UserNavbar />
      <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import UserSidebar from '@/components/user/UserSidebar.vue'
import UserNavbar from '@/components/user/UserNavbar.vue'
import Backdrop from '@/components/layout/Backdrop.vue'
import { useSidebar } from '@/composables/useSidebar'

const { isExpanded, isHovered } = useSidebar()
</script>
