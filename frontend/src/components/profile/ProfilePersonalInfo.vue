<template>
  <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
    <!-- Personal Info -->
    <div class="mb-8">
      <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Personal Information</h4>
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Full Name</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user?.name }}</p>
        </div>
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Email</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user?.email }}</p>
        </div>
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Phone</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user?.phone }}</p>
        </div>
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Bio</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user?.bio || 'Not provided' }}</p>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

defineEmits(['edit'])
</script>
