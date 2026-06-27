<template>
  <div>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <!-- Show loading while user data is being fetched -->
    <div v-if="!user" class="flex justify-center py-10">
      <div class="text-gray-500">Loading profile...</div>
    </div>

    <!-- Full profile (only when user is loaded) -->
    <template v-else>
      <div class="space-y-6">
        <ProfileHeader @avatar-updated="refreshUser" />
        <ProfilePersonalInfo />
        <ProfileAddress />
      </div>

      <!-- Edit button -->
      <div class="flex justify-center mt-8">
        <button @click="openEditModal"
          class="flex items-center gap-2 px-6 py-3 text-sm font-medium text-white transition-colors rounded-lg shadow-md bg-brand-500 hover:bg-brand-600 dark:bg-brand-600 dark:hover:bg-brand-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
          </svg>
          Edit Profile
        </button>
      </div>

      <!-- EditProfileModal – placed here so it's inside v-else -->
      <EditProfileModal :isOpen="isEditModalOpen" @close="isEditModalOpen = false" @saved="refreshUser" />
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ProfileHeader from '@/components/Profile/ProfileHeader.vue'
import ProfilePersonalInfo from '@/components/Profile/ProfilePersonalInfo.vue'
import ProfileAddress from '@/components/Profile/ProfileAddress.vue'
import EditProfileModal from '@/components/Profile/EditProfileModal.vue'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const currentPageTitle = computed(() => 'My Profile')
const isEditModalOpen = ref(false)

const openEditModal = () => {
  isEditModalOpen.value = true
}

const refreshUser = async () => {
  await authStore.fetchUser()
}
</script>
