<template>
  <Modal :isOpen="isOpen" @close="close">
    <template #body>
      <div
        class="no-scrollbar relative w-full max-w-[800px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-8">
        <button @click="close"
          class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="" />
          </svg>
        </button>
        <div class="px-2 pr-14">
          <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Edit Profile</h4>
          <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
            Update your personal details. Fields marked with <span class="text-gray-400">(read‑only)</span> cannot be
            changed.
          </p>
        </div>
        <form @submit.prevent="submit" class="flex flex-col">
          <div class="custom-scrollbar h-[480px] overflow-y-auto p-2">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
              <!-- Name -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Full Name
                </label>
                <input v-model="form.name" type="text"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <!-- Email (non-editable) -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Email <span class="text-xs text-gray-400">(read‑only)</span>
                </label>
                <input v-model="form.email" type="email" disabled
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs cursor-not-allowed dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700" />
              </div>
              <!-- Phone -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Phone
                </label>
                <input v-model="form.phone" type="text"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <!-- Bio -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Bio
                </label>
                <input v-model="form.bio" type="text"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <!-- Country -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Country
                </label>
                <input v-model="form.country" type="text"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <!-- Postal Code -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Postal Code
                </label>
                <input v-model="form.postal_code" type="text"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <!-- Tax ID -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Tax ID
                </label>
                <input v-model="form.tax_id" type="text"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <!-- Address -->
              <div class="lg:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Full Address
                </label>
                <textarea v-model="form.address" rows="2"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
              </div>
              <!-- City (dropdown) -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  City
                </label>
                <select v-model="form.city_id"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                  <option value="">Select City</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">
                    {{ city.city_name }} ({{ city.city_code }})
                  </option>
                </select>
              </div>
              <!-- PCode (non-editable) -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  PCode <span class="text-xs text-gray-400">(read‑only)</span>
                </label>
                <input v-model="form.pcode" type="text" disabled
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs cursor-not-allowed dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700" />
              </div>
              <!-- Status (non-editable) -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Status <span class="text-xs text-gray-400">(read‑only)</span>
                </label>
                <input v-model="form.status" type="text" disabled
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs cursor-not-allowed dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700" />
              </div>
              <!-- Role (non-editable) -->
              <div class="lg:col-span-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Role <span class="text-xs text-gray-400">(read‑only)</span>
                </label>
                <input v-model="form.role" type="text" disabled
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs cursor-not-allowed dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700" />
              </div>
            </div>

            <!-- Change Password Button -->
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
              <button @click.prevent="openPasswordModal" type="button"
                class="flex items-center gap-2 text-sm font-medium text-brand-500 hover:text-brand-600 dark:hover:text-brand-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Change Password
              </button>
            </div>
          </div>

          <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
            <button @click="close" type="button"
              class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
              Cancel
            </button>
            <button type="submit" :disabled="loading"
              class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 sm:w-auto">
              {{ loading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </template>
  </Modal>

  <!-- Change Password Modal -->
  <ChangePasswordModal :isOpen="showPasswordModal" @close="showPasswordModal = false" />
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import Modal from '@/components/Modal.vue'          // ✅ correct import
import ChangePasswordModal from './ChangePasswordModal.vue'
import { useAuthStore } from '@/stores/authStore'
import { useCityStore } from '@/stores/cityStore'


const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits(['close', 'saved'])

const authStore = useAuthStore()
const cityStore = useCityStore()
const loading = ref(false)
const showPasswordModal = ref(false)
const cities = ref([])

const form = reactive({
  name: '',
  email: '',
  phone: '',
  bio: '',
  country: '',
  postal_code: '',
  tax_id: '',
  address: '',
  city_id: null as number | null,
  pcode: '',
  status: '',
  role: '',
})

const loadCities = async () => {
  if (cityStore.items.length === 0) {
    await cityStore.fetchItems(1, { per_page: 999 })
  }
  cities.value = cityStore.items
}

const populateForm = () => {
  const user = authStore.user
  if (user) {
    form.name = user.name || ''
    form.email = user.email || ''
    form.phone = user.phone || ''
    form.bio = user.bio || ''
    form.country = user.country || ''
    form.postal_code = user.postal_code || ''
    form.tax_id = user.tax_id || ''
    form.address = user.address || ''
    form.city_id = user.city_id || null
    form.pcode = user.pcode || ''
    form.status = user.status || ''
    form.role = user.role || ''
  }
}

watch(() => props.isOpen, async (open) => {
  if (open) {
    await loadCities()
    populateForm()
  }
}, { immediate: true })

const close = () => emit('close')
const openPasswordModal = () => (showPasswordModal.value = true)

const submit = async () => {
  loading.value = true
  try {
    const payload = {
      name: form.name,
      phone: form.phone,
      bio: form.bio,
      country: form.country,
      postal_code: form.postal_code,
      tax_id: form.tax_id,
      address: form.address,
      city_id: form.city_id,
    }
    await authStore.updateProfile(payload)
    emit('saved')
    close()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Update failed')
  } finally {
    loading.value = false
  }
}
</script>
