<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Sub Category' : 'Add Sub Category'"
    :subtitle="formData.id ? 'Update the sub category details below.' : 'Fill in the details to add a new sub category.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5">
        <!-- Category dropdown -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Category
          </label>
          <select v-model="formData.category_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="categoryStore.loading">
            <option value="" disabled>Select a category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <p v-if="categoryStore.loading" class="text-xs text-gray-400 mt-1">Loading categories...</p>
        </div>

        <!-- Name -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Sub Category Name
          </label>
          <input v-model="formData.name" type="text" placeholder="e.g. Smartphones"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Description -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Description
          </label>
          <textarea v-model="formData.description" rows="3" placeholder="Brief description"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Status -->
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
import { ref, watch, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import FormModal from '@/components/common/FormModal.vue'
import { useCategoryStore } from '@/stores/categoryStore'
import type { SubCategory } from '@/types'

const props = defineProps<{
  isOpen: boolean
  initialData?: SubCategory | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<SubCategory>): void
}>()

const categoryStore = useCategoryStore()
const { items: categories } = storeToRefs(categoryStore)

const formData = ref<Partial<SubCategory>>({
  name: '',
  description: '',
  category_id: undefined,
  status: 'Active',
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = { name: '', description: '', category_id: undefined, status: 'Active' }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      if (!categoryStore.items.length) categoryStore.fetchAll()
    } else {
      formData.value = { name: '', description: '', category_id: undefined, status: 'Active' }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
