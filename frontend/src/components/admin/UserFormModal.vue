<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit User' : 'Add User'"
    :subtitle="formData.id ? 'Update the user details below.' : 'Fill in the details to add a new user.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="space-y-5">
        <!-- Name & Email -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Full Name <span class="text-error-500">*</span>
            </label>
            <input v-model="formData.name" type="text" placeholder="e.g. John Doe"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Email <span class="text-error-500">*</span>
            </label>
            <input v-model="formData.email" type="email" placeholder="user@example.com"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
        </div>

        <!-- Password & Confirm Password -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Password <span v-if="!formData.id" class="text-error-500">*</span>
            </label>
            <div class="relative">
              <input v-model="formData.password" :type="showPassword ? 'text' : 'password'"
                :placeholder="formData.id ? 'Leave blank to keep current' : 'Min 8 characters'"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                :aria-label="showPassword ? 'Hide password' : 'Show password'">
                <!-- Eye open -->
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <!-- Eye closed -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </div>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Confirm Password <span v-if="!formData.id" class="text-error-500">*</span>
            </label>
            <div class="relative">
              <input v-model="formData.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Confirm password"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'">
                <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Phone & City -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Phone <span class="text-error-500">*</span>
            </label>
            <input v-model="formData.phone" type="tel" placeholder="0300-XXXXXXX" maxlength="11" inputmode="numeric"
              @input="formData.phone = (formData.phone || '').replace(/\D/g, '').slice(0, 11)"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              City <span class="text-error-500">*</span>
            </label>
            <select v-model="formData.city_id"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
              :disabled="cityStore.loading">
              <option value="" disabled>Select a city</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ city.city_name }}
              </option>
            </select>
            <p v-if="cityStore.loading" class="text-xs text-gray-400 mt-1">Loading cities...</p>
          </div>
        </div>

        <!-- Source & PCode (PCode only shown when editing) -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              How did you hear? <span class="text-error-500">*</span>
            </label>
            <select v-model="formData.source"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
              <option value="">Select Source</option>
              <option v-for="src in sources" :key="src" :value="src">{{ src }}</option>
            </select>
          </div>
          <div v-if="formData.id">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              PCode <span class="text-xs text-gray-400">(system-generated)</span>
            </label>
            <input :value="formData.pcode" type="text" disabled readonly
              class="h-11 w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
          </div>
        </div>

        <!-- Address -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Full Address <span class="text-error-500">*</span>
          </label>
          <textarea v-model="formData.address" rows="2" placeholder="House #, Street, Area, City"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Status -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="approved">Approved</option>
          </select>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import FormModal from '@/components/common/FormModal.vue'
import { useCityStore } from '@/stores/cityStore'
import type { User } from '@/types'

const props = defineProps<{
  isOpen: boolean
  initialData?: User | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<User>): void
}>()

const cityStore = useCityStore()
const { items: cities } = storeToRefs(cityStore)

const sources = ['Facebook', 'Google Search', 'WhatsApp', 'Friend Referral', 'Newspapers', 'Twitter/X', 'Other']

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const getEmptyForm = (): Partial<User> & { password?: string; password_confirmation?: string } => ({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  address: '',
  city_id: undefined,
  pcode: '',
  source: '',
  status: 'pending',
})

const formData = ref<Partial<User> & { password?: string; password_confirmation?: string }>(getEmptyForm())

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal, password: '', password_confirmation: '' }
    } else {
      formData.value = getEmptyForm()
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      if (!cityStore.items.length) cityStore.fetchPublicCities()
    } else {
      formData.value = getEmptyForm()
      showPassword.value = false
      showConfirmPassword.value = false
    }
  }
)

const close = () => emit('close')

const save = () => {
  const payload = { ...formData.value }

  if (formData.value.id && !payload.password) {
    delete payload.password
    delete payload.password_confirmation
  }

  delete payload.role

  if (!formData.value.id) {
    delete payload.pcode
  }

  emit('save', payload)
}
</script>
