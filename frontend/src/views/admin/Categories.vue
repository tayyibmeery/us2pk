<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Categories'" />
    <ComponentCard title="Categories">
      <div class="flex justify-end mb-4">
        <button @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700">
          Add Category
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.description }}</td>
              <td>
                <span :class="item.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ item.status }}
                </span>
              </td>
              <td>
                <button @click="openEditModal(item)" class="text-brand-600 hover:text-brand-900 mr-3">Edit</button>
                <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination v-if="pagination" :pagination="pagination" @page-change="fetchItems" />
    </ComponentCard>
    <CategoryFormModal :isOpen="modalOpen" :initialData="editingItem" @close="modalOpen = false" @save="handleSave" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import CategoryFormModal from '@/components/admin/CategoryFormModal.vue'
import Pagination from '@/components/common/Pagination.vue'
import { useCategoryStore } from '@/stores/categoryStore'
import type { Category } from '@/types'

const store = useCategoryStore()
const { items, pagination, loading } = storeToRefs(store)

const modalOpen = ref(false)
const editingItem = ref<Category | null>(null)

const fetchItems = (page = 1) => store.fetchItems(page)

const openCreateModal = () => {
  editingItem.value = null
  modalOpen.value = true
}
const openEditModal = (item: Category) => {
  editingItem.value = item
  modalOpen.value = true
}

const handleSave = async (data: Partial<Category>) => {
  try {
    if (editingItem.value?.id) {
      await store.update(editingItem.value.id, data)
    } else {
      await store.create(data)
    }
    modalOpen.value = false
    // Refresh current page
    store.fetchItems(pagination.value?.current_page || 1)
  } catch (error) {
    console.error('Save failed:', error)
    alert(store.error || 'Save failed')
  }
}

const deleteItem = async (id: number) => {
  if (!confirm('Are you sure?')) return
  try {
    await store.delete(id)
    store.fetchItems(pagination.value?.current_page || 1)
  } catch (error) {
    console.error('Delete failed:', error)
    alert(store.error || 'Delete failed')
  }
}

onMounted(() => fetchItems())
</script>
