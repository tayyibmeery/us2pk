<template>
  <Modal v-if="isOpen" @close="close">
    <template #body>
      <div
        class="no-scrollbar relative w-full max-w-[800px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-8">
        <!-- Close button -->
        <button @click="close"
          class="transition-color absolute right-4 top-4 z-999 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="" />
          </svg>
        </button>

        <div class="px-2 pr-12">
          <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
            Edit Profile
          </h4>
          <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
            Update your personal information and address details.
          </p>
        </div>

        <form @submit.prevent="save" class="flex flex-col">
          <div class="custom-scrollbar max-h-[65vh] overflow-y-auto px-2">
            <!-- Personal Information -->
            <div class="mb-6">
              <h5
                class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2">
                Personal Information
              </h5>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Full Name
                  </label>
                  <input v-model="form.name" type="text"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Email Address
                  </label>
                  <input v-model="form.email" type="email"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Phone
                  </label>
                  <input v-model="form.phone" type="tel"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                  <!-- Placeholder for alignment, but we can put a dummy or just let it be -->
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 opacity-0">
                    .
                  </label>
                  <!-- Nothing here, but we keep the grid balanced -->
                </div>
                <div class="col-span-2">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Bio
                  </label>
                  <textarea v-model="form.bio" rows="2"
                    class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                </div>
              </div>
            </div>

            <!-- Address Details -->
            <div>
              <h5
                class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2">
                Address Details
              </h5>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Country
                  </label>
                  <input v-model="form.country" type="text"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    City
                  </label>
                  <select v-model="form.city_id"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="" disabled>Select a city</option>
                    <option v-for="city in cities" :key="city.id" :value="city.id">
                      {{ city.city_name }}
                    </option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Full Address
                  </label>
                  <textarea v-model="form.address" rows="2"
                    class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Postal Code
                  </label>
                  <input v-model="form.postal_code" type="text"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    TAX ID
                  </label>
                  <input v-model="form.tax_id" type="text"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row items-center gap-3 px-2 mt-6 sm:justify-end">
            <button type="button" @click="close"
              class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
              Close
            </button>
            <button type="submit" :disabled="saving"
              class="flex w-full justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 sm:w-auto">
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

const props = defineProps<{
  isOpen: boolean
  userData: any
}>()

const emit = defineEmits(['close', 'saved'])

const authStore = useAuthStore()
const cities = ref<any[]>([])
const saving = ref(false)

const form = ref({
  name: '',
  email: '',
  phone: '',
  bio: '',
  country: '',
  city_id: null as number | null,
  address: '',
  postal_code: '',
  tax_id: '',
})

watch(
  () => props.userData,
  (newVal) => {
    if (newVal) {
      form.value = {
        name: newVal.name || '',
        email: newVal.email || '',
        phone: newVal.phone || '',
        bio: newVal.bio || '',
        country: newVal.country || '',
        city_id: newVal.city_id || null,
        address: newVal.address || '',
        postal_code: newVal.postal_code || '',
        tax_id: newVal.tax_id || '',
      }
    }
  },
  { immediate: true }
)

onMounted(async () => {
  try {
    const res = await api.get('/cities')
    cities.value = res.data
  } catch (err) {
    console.error('Failed to load cities', err)
  }
})

const close = () => emit('close')

const save = async () => {
  saving.value = true
  try {
    await api.put('/user/profile', form.value)
    await authStore.fetchUser()
    emit('saved')
    close()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Update failed')
  } finally {
    saving.value = false
  }
}
</script>
