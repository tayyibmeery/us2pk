<!-- src/components/admin/SubSubCategoryFormModal.vue -->
<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- overlay & modal container same as above -->
    <div class="...">
      <div class="...">
        <div class="...">
          <h3>{{ formData.id ? 'Edit Sub Sub Category' : 'Add Sub Sub Category' }}</h3>
          <div class="mt-4 space-y-4">
            <!-- Category -->
            <div>
              <label>Category</label>
              <select v-model="formData.category_id" @change="onCategoryChange">
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <!-- Sub Category (filtered) -->
            <div>
              <label>Sub Category</label>
              <select v-model="formData.sub_category_id">
                <option v-for="sub in filteredSubCategories" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
              </select>
            </div>
            <!-- Name -->
            <div>
              <label>Sub Sub Category Name</label>
              <input v-model="formData.name" type="text" />
            </div>
            <!-- Description -->
            <div>
              <label>Description</label>
              <textarea v-model="formData.description" rows="2"></textarea>
            </div>
            <!-- Status -->
            <div>
              <label>Status</label>
              <select v-model="formData.status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
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
import { ref, watch, computed } from 'vue'
import type { SubSubCategory, Category, SubCategory } from '@/types'
import api from '@/services/api'

const props = defineProps<{
  isOpen: boolean
  initialData?: SubSubCategory | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<SubSubCategory>): void
}>()

const categories = ref<Category[]>([])
const subCategories = ref<SubCategory[]>([])
const formData = ref<Partial<SubSubCategory>>({
  name: '',
  description: '',
  category_id: undefined,
  sub_category_id: undefined,
  status: 'Active'
})

const filteredSubCategories = computed(() => {
  if (!formData.value.category_id) return []
  return subCategories.value.filter(sub => sub.category_id === formData.value.category_id)
})

const fetchCategories = async () => {
  const res = await api.get('/admin/categories?per_page=1000')
  categories.value = res.data.data
}
const fetchSubCategories = async () => {
  const res = await api.get('/admin/sub-categories?per_page=1000')
  subCategories.value = res.data.data
}

const onCategoryChange = () => {
  formData.value.sub_category_id = undefined
}

watch(() => props.initialData, (newVal) => {
  if (newVal) {
    formData.value = { ...newVal }
  } else {
    formData.value = { name: '', description: '', category_id: undefined, sub_category_id: undefined, status: 'Active' }
  }
}, { immediate: true })

watch(() => props.isOpen, (open) => {
  if (open) {
    fetchCategories()
    fetchSubCategories()
  } else {
    formData.value = { name: '', description: '', category_id: undefined, sub_category_id: undefined, status: 'Active' }
  }
})

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
