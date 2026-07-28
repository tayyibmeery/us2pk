<!-- frontend/src/views/user/profile/UserProfile.vue -->
<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">My Profile</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">View and manage your personal information</p>
      </div>
      <div class="flex gap-3">
        <router-link to="/user/settings"
          class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
          Security
        </router-link>
        <button @click="openEditModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition shadow-sm hover:shadow-md">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
          </svg>
          Edit Profile
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="w-8 h-8 border-4 border-gray-200 dark:border-gray-700 border-t-brand-500 rounded-full animate-spin" />
    </div>

    <div v-else
      class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <!-- Cover -->
      <div class="h-20 bg-gradient-to-r from-brand-500 to-brand-600 dark:from-brand-600 dark:to-brand-700" />

      <div class="px-6 pb-6">
        <!-- Avatar + Name -->
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12">
          <div class="relative group shrink-0">
            <div
              class="w-24 h-24 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 shadow-md bg-gray-100 dark:bg-gray-700">
              <img :src="userAvatar" alt="Profile" class="w-full h-full object-cover" @error="handleImageError" />
            </div>
            <label for="avatar-upload"
              class="absolute bottom-0 right-0 flex items-center justify-center w-8 h-8 bg-brand-500 rounded-full cursor-pointer hover:bg-brand-600 transition shadow-sm border-2 border-white dark:border-gray-800"
              title="Change photo">
              <svg v-if="!uploading" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <svg v-else class="w-3.5 h-3.5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
              </svg>
            </label>
            <input id="avatar-upload" type="file" accept="image/*" class="hidden" :disabled="uploading"
              @change="handleAvatarUpload" />
          </div>

          <div class="pb-1">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ user?.name || 'N/A' }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ user?.email || 'N/A' }}</p>
          </div>
        </div>

        <!-- Badges -->
        <div class="flex flex-wrap items-center gap-2 mt-5">
          <span
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 capitalize">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" />
            {{ user?.role || 'User' }}
          </span>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium capitalize"
            :class="statusBadgeClass">
            <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass" />
            {{ user?.status || 'Active' }}
          </span>
          <span v-if="user?.city?.city_name"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ user.city.city_name }}
          </span>
        </div>

        <div v-if="uploadError" class="mt-4 text-sm text-red-500 bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg">
          {{ uploadError }}
        </div>

        <!-- Personal Information -->
        <div class="mt-8">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Personal
            Information</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <InfoField label="Full Name" :value="user?.name" />
            <InfoField label="Email Address" :value="user?.email" locked />
            <InfoField label="Phone Number" :value="user?.phone" />
            <InfoField label="Postal Code" :value="user?.pcode" locked />
            <InfoField label="City" :value="user?.city?.city_name" locked />
            <InfoField label="Account Type" :value="user?.role" locked class-value="capitalize" />
            <InfoField label="Address" :value="user?.address" class="sm:col-span-2" />
            <InfoField v-if="user?.bio" label="Bio" :value="user?.bio" class="sm:col-span-2" />
            <InfoField label="Member Since" :value="formatDate(user?.created_at)" locked />
          </div>
          <p class="mt-4 text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Fields marked with a lock icon are managed by our team and can't be edited here.
          </p>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <FormModal :isOpen="showEditModal" title="Edit Profile" subtitle="Update your editable personal information"
      :loading="savingProfile" save-label="Save Changes" @close="showEditModal = false" @save="submitProfile">
      <template #fields>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
            <input v-model="editForm.name" type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Email <span class="text-xs text-gray-400">(read-only)</span>
            </label>
            <input :value="user?.email" type="email" disabled
              class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
            <input v-model="editForm.phone" type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
            <input v-model="editForm.bio" type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
          </div>
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
            <textarea v-model="editForm.address" rows="2"
              class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
          </div>
        </div>
      </template>
    </FormModal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineComponent, h } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useUserProfileStore } from '@/stores/userProfileStore'
import { useToast } from '@/composables/useToast'
import FormModal from '@/components/common/FormModal.vue'

const InfoField = defineComponent({
  props: { label: String, value: [String, Number], locked: Boolean, classValue: String },
  setup(props) {
    return () => h('div', { class: 'flex flex-col p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-100 dark:border-gray-600/30' }, [
      h('span', { class: 'flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider' }, [
        props.label,
        props.locked ? h('svg', { class: 'w-3 h-3', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' })
        ]) : null,
      ]),
      h('span', { class: `font-medium text-gray-800 dark:text-white ${props.classValue || ''}` }, props.value || 'N/A'),
    ])
  }
})

const authStore = useAuthStore()
const profileStore = useUserProfileStore()
const toast = useToast()

const loading = ref(false)
const uploading = ref(false)
const uploadError = ref('')
const savingProfile = ref(false)
const showEditModal = ref(false)

const user = computed(() => authStore.user)

const editForm = ref({
  name: '',
  phone: '',
  bio: '',
  address: '',
})

const statusBadgeClass = computed(() => {
  const s = user.value?.status
  if (s === 'approved' || s === 'verified') return 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400'
  return 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400'
})
const statusDotClass = computed(() => {
  const s = user.value?.status
  return (s === 'approved' || s === 'verified') ? 'bg-green-500' : 'bg-amber-500'
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

const formatDate = (date: string | undefined) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' })
}

const handleAvatarUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return
  uploading.value = true
  uploadError.value = ''
  try {
    await authStore.updateAvatar(input.files[0])
    toast.success('Avatar updated successfully')
    await authStore.fetchUser()
  } catch (error: any) {
    uploadError.value = error.response?.data?.message || 'Failed to upload avatar'
    toast.error(uploadError.value)
  } finally {
    uploading.value = false
    input.value = ''
  }
}

const openEditModal = () => {
  editForm.value = {
    name: user.value?.name || '',
    phone: user.value?.phone || '',
    bio: user.value?.bio || '',
    address: user.value?.address || '',
  }
  showEditModal.value = true
}

const submitProfile = async () => {
  savingProfile.value = true
  try {
    // city_id is required server-side but not user-editable; send the current value unchanged.
    await profileStore.updateProfile({ ...editForm.value, city_id: user.value?.city_id })
    await authStore.fetchUser()
    toast.success('Profile updated successfully')
    showEditModal.value = false
  } catch (error: any) {
    toast.error(error.response?.data?.message || profileStore.error || 'Failed to update profile')
  } finally {
    savingProfile.value = false
  }
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
