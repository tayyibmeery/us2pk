<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit City' : 'Add City'"
    :subtitle="formData.id ? 'Update the city details below.' : 'Fill in the details to add a new city.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            City Name
          </label>
          <input v-model="formData.city_name" type="text" placeholder="e.g. Lahore"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            City Code
          </label>
          <input v-model="formData.city_code" type="text" placeholder="e.g. LHE"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { City } from '@/types'

const props = defineProps<{
  isOpen: boolean
  initialData?: City | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<City>): void
}>()

const formData = ref<Partial<City>>({
  city_name: '',
  city_code: '',
  status: true,
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal, status: !!newVal.status }
    } else {
      formData.value = { city_name: '', city_code: '', status: true }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = { city_name: '', city_code: '', status: true }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
