<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Category' : 'Add Category'"
    :subtitle="formData.id ? 'Update the category details below.' : 'Fill in the details to add a new category.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Category Name
          </label>
          <input v-model="formData.name" type="text" placeholder="e.g. Electronics"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Description
          </label>
          <textarea v-model="formData.description" rows="3" placeholder="Brief description of the category"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { Category } from '@/types'

const props = defineProps<{
  isOpen: boolean
  initialData?: Category | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<Category>): void
}>()

const formData = ref<Partial<Category>>({
  name: '',
  description: '',
  status: 'Active',
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = { name: '', description: '', status: 'Active' }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = { name: '', description: '', status: 'Active' }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
