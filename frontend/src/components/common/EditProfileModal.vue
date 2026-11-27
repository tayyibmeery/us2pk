<!-- frontend/src/components/common/EditProfileModal.vue -->
<template>
  <Modal :isOpen="isOpen" @close="close" size="xl">
    <template #body>
      <div
        class="no-scrollbar relative w-full max-w-[800px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-8">
        <button @click="close"
          class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="currentColor" />
          </svg>
        </button>

        <div class="px-2 pr-14">
          <h4 class="mb-1 text-2xl font-semibold text-gray-800 dark:text-white/90">
            {{ isAdmin ? 'Edit Admin Profile' : 'Edit Profile' }}
          </h4>
          <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Locked fields are managed by our team and can't be changed here.
          </p>
        </div>

        <form @submit.prevent="submit" class="flex flex-col">
          <div class="custom-scrollbar h-[480px] overflow-y-auto p-2">

            <!-- Editable section -->
            <h5 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Editable
            </h5>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2 mb-6">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full Name</label>
                <input v-model="form.name" type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
                <input v-model="form.phone" type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Bio</label>
                <input v-model="form.bio" type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Country</label>
                <input v-model="form.country" type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Postal Code</label>
                <input v-model="form.postal_code" type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div v-if="isAdmin">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tax ID</label>
                <input v-model="form.tax_id" type="text"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div class="lg:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full Address</label>
                <textarea v-model="form.address" rows="2"
                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
            </div>

            <!-- Locked section -->
            <h5
              class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Locked
            </h5>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
              <LockedField label="Email" :value="form.email" />
              <LockedField label="City" :value="getCityName" />
              <LockedField label="PCode" :value="form.pcode" />
              <LockedField label="Status" :value="form.status" class-value="capitalize" />
              <LockedField label="Role" :value="form.role" class-value="capitalize" />
            </div>

            <!-- Change Password -->
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
              <button @click.prevent="openPasswordModal" type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

  <ChangePasswordModal :isOpen="showPasswordModal" @close="showPasswordModal = false" />
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed, defineComponent, h } from 'vue'
import Modal from '@/components/Modal.vue'
import ChangePasswordModal from '@/components/common/ChangePasswordModal.vue'
import { useAuthStore } from '@/stores/authStore'
import { useCityStore } from '@/stores/cityStore'
import { useToast } from '@/composables/useToast'

const LockedField = defineComponent({
  props: { label: String, value: [String, Number], classValue: String },
  setup(props) {
    return () => h('div', [
      h('label', { class: 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400' }, [
        props.label,
        h('span', { class: 'ml-1.5 text-xs text-gray-400' }, '(read-only)'),
      ]),
      h('div', {
        class: `h-11 w-full flex items-center rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-500 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 ${props.classValue || ''}`
      }, props.value || 'N/A'),
    ])
  }
})

const props = defineProps<{ isOpen: boolean; isAdmin?: boolean }>()
const emit = defineEmits(['close', 'saved'])

const authStore = useAuthStore()
const cityStore = useCityStore()
const toast = useToast()
const loading = ref(false)
const showPasswordModal = ref(false)
const cities = ref([])

const form = reactive({
  name: '', email: '', phone: '', bio: '', country: '', postal_code: '',
  tax_id: '', address: '', city_id: null as number | null, pcode: '', status: '', role: '',
})

const getCityName = computed(() => {
  if (!form.city_id) return 'N/A'
  const city = cities.value.find((c: any) => c.id === form.city_id)
  return city ? (city as any).city_name : 'N/A'
})

const loadCities = async () => {
  try {
    await cityStore.fetchPublicCities()
    cities.value = cityStore.items
  } catch {
    cities.value = []
  }
}

const populateForm = () => {
  const u = authStore.user
  if (!u) return
  form.name = u.name || ''
  form.email = u.email || ''
  form.phone = u.phone || ''
  form.bio = u.bio || ''
  form.country = u.country || ''
  form.postal_code = u.postal_code || ''
  form.tax_id = u.tax_id || ''
  form.address = u.address || ''
  form.city_id = u.city_id || null
  form.pcode = u.pcode || ''
  form.status = u.status || ''
  form.role = u.role || ''
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
    const payload: any = {
      name: form.name,
      phone: form.phone,
      bio: form.bio,
      country: form.country,
      postal_code: form.postal_code,
      address: form.address,
      city_id: form.city_id,
    }
    if (props.isAdmin) payload.tax_id = form.tax_id

    await authStore.updateProfile(payload)
    toast.success('Profile updated successfully')
    emit('saved')
    close()
  } catch (err: any) {
    toast.error(err.response?.data?.message || 'Update failed')
  } finally {
    loading.value = false
  }
}
</script>
