<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit User' : 'Add User'"
    :subtitle="formData.id ? 'Update the user details below.' : 'Fill in the details to add a new user.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Name -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Full Name
          </label>
          <input v-model="formData.name" type="text" placeholder="e.g. John Doe"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Email -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Email
          </label>
          <input v-model="formData.email" type="email" placeholder="user@example.com"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Phone -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Phone
          </label>
          <input v-model="formData.phone" type="text" placeholder="+92-300-1234567"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- City -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            City
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

        <!-- Address -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Address
          </label>
          <textarea v-model="formData.address" rows="2" placeholder="Street, area, etc."
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- PCode -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            PCode
          </label>
          <input v-model="formData.pcode" type="text" placeholder="e.g. LHE123"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Source -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Source
          </label>
          <input v-model="formData.source" type="text" placeholder="e.g. Facebook"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
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

const formData = ref<Partial<User>>({
  name: '',
  email: '',
  phone: '',
  address: '',
  city_id: undefined,
  pcode: '',
  source: '',
  status: 'pending',
  role: 'user',
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = {
        name: '',
        email: '',
        phone: '',
        address: '',
        city_id: undefined,
        pcode: '',
        source: '',
        status: 'pending',
        role: 'user',
      }
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
      formData.value = {
        name: '',
        email: '',
        phone: '',
        address: '',
        city_id: undefined,
        pcode: '',
        source: '',
        status: 'pending',
        role: 'user',
      }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
