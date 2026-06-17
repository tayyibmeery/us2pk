<!-- src/components/admin/CityFormModal.vue -->
<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="close"></div>
      </div>
      <div
        class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
              <h3 class="text-lg font-medium leading-6 text-gray-900">
                {{ formData.id ? 'Edit City' : 'Add City' }}
              </h3>
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">City Name</label>
                  <input v-model="formData.city_name" type="text"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                    placeholder="e.g. Lahore" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">City Code</label>
                  <input v-model="formData.city_code" type="text"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"
                    placeholder="e.g. LHE" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <select v-model="formData.status"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    <option :value="true">Active</option>
                    <option :value="false">Inactive</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="save"
            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-brand-600 border border-transparent rounded-md shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:ml-3 sm:w-auto sm:text-sm">
            {{ formData.id ? 'Update' : 'Create' }}
          </button>
          <button @click="close"
            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
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
  status: true
})

watch(() => props.initialData, (newVal) => {
  if (newVal) {
    formData.value = { ...newVal }
  } else {
    formData.value = { city_name: '', city_code: '', status: true }
  }
}, { immediate: true })

watch(() => props.isOpen, (open) => {
  if (!open) {
    // reset form when closed
    formData.value = { city_name: '', city_code: '', status: true }
  }
})

const close = () => emit('close')
const save = () => {
  emit('save', formData.value)
}
</script>
