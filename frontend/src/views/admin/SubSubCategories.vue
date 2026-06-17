<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Sub Sub Categories'" />
    <ComponentCard title="Sub Sub Categories">
      <div class="flex justify-end mb-4">
        <button @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700">
          Add Sub Sub Category
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Category</th>
              <th>Sub Category</th>
              <th>Description</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.category?.name || 'N/A' }}</td>
              <td>{{ item.sub_category?.name || 'N/A' }}</td>
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
    <SubSubCategoryFormModal :isOpen="modalOpen" :initialData="editingItem" @close="modalOpen = false"
      @save="handleSave" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import SubSubCategoryFormModal from '@/components/admin/SubSubCategoryFormModal.vue'
import Pagination from '@/components/common/Pagination.vue'
import { useSubSubCategoryStore } from '@/stores/subSubCategoryStore'
import type { SubSubCategory } from '@/types'

const store = useSubSubCategoryStore()
const { items, pagination } = storeToRefs(store)

const modalOpen = ref(false)
const editingItem = ref<SubSubCategory | null>(null)

const fetchItems = (page = 1) => store.fetchItems(page)

const openCreateModal = () => {
  editingItem.value = null
  modalOpen.value = true
}
const openEditModal = (item: SubSubCategory) => {
  editingItem.value = item
  modalOpen.value = true
}

const handleSave = async (data: Partial<SubSubCategory>) => {
  try {
    if (editingItem.value?.id) {
      await store.update(editingItem.value.id, data)
    } else {
      await store.create(data)
    }
    modalOpen.value = false
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
