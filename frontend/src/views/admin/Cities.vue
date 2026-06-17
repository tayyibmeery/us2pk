<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Cities'" />
    <ComponentCard title="Cities">
      <!-- Add button -->
      <div class="flex justify-end mb-4">
        <button @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-700">
          Add City
        </button>
      </div>

      <!-- City table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="city in cities" :key="city.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ city.city_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.city_code }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="city.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ city.status ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="openEditModal(city)" class="text-brand-600 hover:text-brand-900 mr-3">
                  Edit
                </button>
                <button @click="deleteCity(city.id)" class="text-red-600 hover:text-red-900">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading indicator -->
      <div v-if="loading" class="text-center py-4 text-gray-500">Loading cities...</div>

      <!-- Pagination -->
      <div v-if="pagination"
        class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
        <div class="flex justify-between flex-1 sm:hidden">
          <button @click="prevPage" :disabled="!pagination.prev_page_url"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50">
            Previous
          </button>
          <button @click="nextPage" :disabled="!pagination.next_page_url"
            class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50">
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ pagination.from }}</span> to
              <span class="font-medium">{{ pagination.to }}</span> of
              <span class="font-medium">{{ pagination.total }}</span> results
            </p>
          </div>
          <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
            <button @click="prevPage" :disabled="!pagination.prev_page_url"
              class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50">
              Previous
            </button>
            <span
              class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
              Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>
            <button @click="nextPage" :disabled="!pagination.next_page_url"
              class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50">
              Next
            </button>
          </nav>
        </div>
      </div>
    </ComponentCard>

    <!-- City Form Modal -->
    <CityFormModal :isOpen="modalOpen" :initialData="editingCity" @close="modalOpen = false" @save="handleSave" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ComponentCard from '@/components/common/ComponentCard.vue'
import CityFormModal from '@/components/admin/CityFormModal.vue'
import { useCityStore } from '@/stores/cityStore'
import type { City } from '@/types'

const cityStore = useCityStore()
const { items: cities, pagination, loading } = storeToRefs(cityStore)

const modalOpen = ref(false)
const editingCity = ref<City | null>(null)

// Load first page on mount
onMounted(() => {
  cityStore.fetchItems(1)
})

// Pagination helpers
const prevPage = () => {
  if (pagination.value?.prev_page_url) {
    const url = new URL(pagination.value.prev_page_url)
    const page = url.searchParams.get('page')
    if (page) cityStore.fetchItems(parseInt(page))
  }
}

const nextPage = () => {
  if (pagination.value?.next_page_url) {
    const url = new URL(pagination.value.next_page_url)
    const page = url.searchParams.get('page')
    if (page) cityStore.fetchItems(parseInt(page))
  }
}

// Modal controls
const openCreateModal = () => {
  editingCity.value = null
  modalOpen.value = true
}
const openEditModal = (city: City) => {
  editingCity.value = city
  modalOpen.value = true
}

// Save handler (create or update)
const handleSave = async (data: Partial<City>) => {
  try {
    if (editingCity.value?.id) {
      await cityStore.update(editingCity.value.id, data)
    } else {
      await cityStore.create(data)
    }
    modalOpen.value = false
    // Refresh the current page
    cityStore.fetchItems(pagination.value?.current_page || 1)
  } catch (error) {
    console.error('Save failed:', error)
    // Optionally display error from store
    alert(cityStore.error || 'Save failed')
  }
}

// Delete
const deleteCity = async (id: number) => {
  if (!confirm('Are you sure you want to delete this city?')) return
  try {
    await cityStore.delete(id)
    // Refresh the current page
    cityStore.fetchItems(pagination.value?.current_page || 1)
  } catch (error) {
    console.error('Delete failed:', error)
    alert(cityStore.error || 'Delete failed')
  }
}
</script>
