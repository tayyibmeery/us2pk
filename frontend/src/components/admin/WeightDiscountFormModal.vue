<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Weight Discount' : 'Add Weight Discount'"
    :subtitle="formData.id ? 'Update the discount details below.' : 'Fill in the details to add a new weight discount.'"
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
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Discount Percent (%)
          </label>
          <input v-model.number="formData.discount_percent" type="number" step="0.01" min="0" max="100"
            placeholder="e.g. 10.5"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { WeightDiscount } from '@/stores/weightDiscountStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: WeightDiscount | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<WeightDiscount>): void
}>()

const formData = ref<Partial<WeightDiscount>>({
  warehouse: '',
  discount_percent: 0,
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = { warehouse: '', discount_percent: 0 }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = { warehouse: '', discount_percent: 0 }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
