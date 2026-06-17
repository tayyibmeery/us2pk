<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Sub Categories'" />
    <ComponentCard title="Sub Categories">
      <div class="flex justify-end mb-4">
        <button @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700">
          Add Sub Category
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Category</th>
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
      <Pagination :pagination="pagination" @page-change="fetchItems" />
    </ComponentCard>
    <SubCategoryFormModal :isOpen="modalOpen" :initialData="editingItem" @close="modalOpen = false"
      @save="handleSave" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import SubCategoryFormModal from '@/components/admin/SubCategoryFormModal.vue'
import Pagination from '@/components/common/Pagination.vue'
import api from '@/services/api'
import type { SubCategory, PaginatedResponse } from '@/types'

const items = ref<SubCategory[]>([])
const pagination = ref<PaginatedResponse<SubCategory> | null>(null)
const modalOpen = ref(false)
const editingItem = ref<SubCategory | null>(null)
const currentPage = ref(1)

const fetchItems = async (page = 1) => {
  const res = await api.get<PaginatedResponse<SubCategory>>(`/admin/sub-categories?page=${page}`)
  items.value = res.data.data
  pagination.value = res.data
}

const openCreateModal = () => { editingItem.value = null; modalOpen.value = true }
const openEditModal = (item: SubCategory) => { editingItem.value = item; modalOpen.value = true }

const handleSave = async (data: Partial<SubCategory>) => {
  if (editingItem.value?.id) {
    await api.put(`/admin/sub-categories/${editingItem.value.id}`, data)
  } else {
    await api.post('/admin/sub-categories', data)
  }
  modalOpen.value = false
  fetchItems(currentPage.value)
}

const deleteItem = async (id: number) => {
  if (!confirm('Are you sure?')) return
  await api.delete(`/admin/sub-categories/${id}`)
  fetchItems(currentPage.value)
}

onMounted(() => fetchItems())
</script>
