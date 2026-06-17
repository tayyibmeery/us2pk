<template>
  <Modal :isOpen="isOpen" @close="close">
    <template #body>
      <div
        class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-8">
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
            {{ formData.id ? 'Edit Sub Sub Category' : 'Add Sub Sub Category' }}
          </h4>
          <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
            {{ formData.id ? 'Update the sub sub category details below.' : 'Fill in the details to add a new sub sub category.' }}
          </p>
        </div>

        <form @submit.prevent="save" class="flex flex-col">
          <div class="custom-scrollbar max-h-[65vh] overflow-y-auto px-2">
            <div class="grid grid-cols-1 gap-5">
              <!-- Category -->
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Category
                </label>
                <select v-model="formData.category_id" @change="onCategoryChange"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                  :disabled="categoryStore.loading">
                  <option value="" disabled>Select a category</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>
                <p v-if="categoryStore.loading" class="text-xs text-gray-400 mt-1">Loading categories...</p>
              </div>

              <!-- Sub Category (filtered) -->
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Sub Category
                </label>
                <select v-model="formData.sub_category_id"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                  :disabled="subCategoryStore.loading || !formData.category_id">
                  <option value="" disabled>Select a sub category</option>
                  <option v-for="sub in filteredSubCategories" :key="sub.id" :value="sub.id">
                    {{ sub.name }}
                  </option>
                </select>
                <p v-if="subCategoryStore.loading" class="text-xs text-gray-400 mt-1">Loading sub categories...</p>
              </div>

              <!-- Name -->
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Sub Sub Category Name
                </label>
                <input v-model="formData.name" type="text" placeholder="e.g. iPhone 15"
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
          </div>

          <div class="flex flex-col sm:flex-row items-center gap-3 px-2 mt-6 sm:justify-end">
            <button type="button" @click="close"
              class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
              Cancel
            </button>
            <button type="submit"
              class="flex w-full justify-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
              {{ formData.id ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import Modal from '@/components/Modal.vue'
import { useCategoryStore } from '@/stores/categoryStore'
import { useSubCategoryStore } from '@/stores/subCategoryStore'
import type { SubSubCategory } from '@/types'

const props = defineProps<{
  isOpen: boolean
  initialData?: SubSubCategory | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<SubSubCategory>): void
}>()

const categoryStore = useCategoryStore()
const subCategoryStore = useSubCategoryStore()
const { items: categories } = storeToRefs(categoryStore)
const { items: subCategories } = storeToRefs(subCategoryStore)

const formData = ref<Partial<SubSubCategory>>({
  name: '',
  description: '',
  category_id: undefined,
  sub_category_id: undefined,
  status: 'Active',
})

const filteredSubCategories = computed(() => {
  if (!formData.value.category_id) return []
  return subCategories.value.filter(sub => sub.category_id === formData.value.category_id)
})

const onCategoryChange = () => {
  formData.value.sub_category_id = undefined
}

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = { name: '', description: '', category_id: undefined, sub_category_id: undefined, status: 'Active' }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      if (!categoryStore.items.length) categoryStore.fetchAll()
      if (!subCategoryStore.items.length) subCategoryStore.fetchAll()
    } else {
      formData.value = { name: '', description: '', category_id: undefined, sub_category_id: undefined, status: 'Active' }
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
