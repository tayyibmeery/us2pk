<!-- src/components/admin/SubCategoryFormModal.vue -->
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
                {{ formData.id ? 'Edit Sub Category' : 'Add Sub Category' }}
              </h3>
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Category</label>
                  <select v-model="formData.category_id"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Sub Category Name</label>
                  <input v-model="formData.name" type="text"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea v-model="formData.description" rows="2"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <select v-model="formData.status"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="save" class="...">Save</button>
          <button @click="close" class="...">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import type { SubCategory, Category } from '@/types'
import api from '@/services/api'

const props = defineProps<{
  isOpen: boolean
  initialData?: SubCategory | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<SubCategory>): void
}>()

const categories = ref<Category[]>([])
const formData = ref<Partial<SubCategory>>({
  name: '',
  description: '',
  category_id: undefined,
  status: 'Active'
})

const fetchCategories = async () => {
  try {
    const res = await api.get('/admin/categories?per_page=1000')
    categories.value = res.data.data
  } catch (e) { console.error(e) }
}

watch(() => props.initialData, (newVal) => {
  if (newVal) {
    formData.value = { ...newVal }
  } else {
    formData.value = { name: '', description: '', category_id: undefined, status: 'Active' }
  }
}, { immediate: true })

watch(() => props.isOpen, (open) => {
  if (open) fetchCategories()
  else {
    formData.value = { name: '', description: '', category_id: undefined, status: 'Active' }
  }
})

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
