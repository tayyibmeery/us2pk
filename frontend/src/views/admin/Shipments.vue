<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="pageTitle" />
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">All Shipments</h2>
        <button @click="openCreateModal" class="bg-brand-500 text-white px-4 py-2 rounded-lg hover:bg-brand-600">
          + New Shipment
        </button>
      </div>
      <ShipmentTable :shipments="shipments" :currentPage="currentPage" :totalPages="totalPages" :canManage="true"
        @edit="openEditModal" @delete="deleteShipment" @page-change="loadShipments" />
    </div>
    <ShipmentForm :isOpen="showModal" :shipment="editingShipment" @close="showModal = false" @saved="onSaved" />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ShipmentTable from '@/components/shipments/ShipmentTable.vue'
import ShipmentForm from '@/components/shipments/ShipmentForm.vue'
import api from '@/services/api'

const pageTitle = ref('Shipments')
const shipments = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const showModal = ref(false)
const editingShipment = ref(null)

const loadShipments = async (page = 1) => {
  try {
    const res = await api.get('/admin/shipments', { params: { page } })
    shipments.value = res.data.data
    currentPage.value = res.data.current_page
    totalPages.value = res.data.last_page
  } catch (e) {
    console.error(e)
    // Optionally show a toast/alert
  }
}

const openCreateModal = () => {
  editingShipment.value = null
  showModal.value = true
}

const openEditModal = (shipment: any) => {
  editingShipment.value = { ...shipment }
  showModal.value = true
}

const deleteShipment = async (id: number) => {
  if (!confirm('Are you sure?')) return
  try {
    await api.delete(`/admin/shipments/${id}`)
    await loadShipments(currentPage.value)
  } catch (e) {
    alert('Delete failed')
  }
}

const onSaved = () => {
  loadShipments(currentPage.value)
}

onMounted(() => loadShipments(1))
</script>
