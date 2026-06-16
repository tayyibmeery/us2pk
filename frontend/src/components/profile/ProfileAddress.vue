<template>
  <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
    <div>
      <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Address</h4>
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Country</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">
            {{ user?.country || 'Pakistan' }}
          </p>
        </div>
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">City/State</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">
            {{ user?.city?.city_name || 'Not set' }}, {{ user?.state || '' }}
          </p>
        </div>
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Full Address</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user?.address }}</p>
        </div>
        <div>
          <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">PCode</p>
          <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ user?.pcode }}</p>
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
