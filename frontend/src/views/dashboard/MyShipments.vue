<template>

    <PageBreadcrumb :pageTitle="pageTitle" />
    <div class="space-y-6">
      <h2 class="text-xl font-semibold">My Shipments</h2>
      <ShipmentTable :shipments="shipments" :currentPage="currentPage" :totalPages="totalPages" :canManage="false"
        @page-change="loadShipments" />
    </div>
  
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ShipmentTable from '@/components/shipments/ShipmentTable.vue'
import api from '@/services/api'

const pageTitle = ref('My Shipments')
const shipments = ref([])
const currentPage = ref(1)
const totalPages = ref(1)

const loadShipments = async (page = 1) => {
  try {
    const res = await api.get('/user/shipments', { params: { page } })
    shipments.value = res.data.data
    currentPage.value = res.data.current_page
    totalPages.value = res.data.last_page
  } catch (e) {
    console.error(e)
  }
}

onMounted(() => loadShipments(1))
</script>
