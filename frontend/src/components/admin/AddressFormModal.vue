<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Address' : 'Add Address'"
    :subtitle="formData.id ? 'Update the address details below.' : 'Fill in the details to add a new address.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Warehouse
          </label>
          <input v-model="formData.warehouse" type="text" placeholder="e.g. USA"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Address
          </label>
          <textarea v-model="formData.address" rows="3" placeholder="Enter full address"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { Address } from '@/stores/addressStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: Address | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<Address>): void
}>()

const formData = ref<Partial<Address>>({
  warehouse: '',
  address: '',
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = { warehouse: '', address: '' }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = { warehouse: '', address: '' }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
