<template>
  <PageBreadcrumb :pageTitle="pageTitle" />
  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <div v-if="loading" class="text-center py-10 text-gray-400">Loading...</div>
    <div v-else-if="!shipment" class="text-center py-10 text-gray-400">Shipment not found.</div>
    <div v-else>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ shipment.shipment_code }}</h2>
        <div class="flex gap-2">
          <router-link :to="`/admin/shipments/${shipment.id}/edit`"
            class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Edit</router-link>
          <button @click="goBack"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back</button>
        </div>
      </div>

      <!-- Details Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-4">
          <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Shipment Details</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div>
                <dt class="font-medium inline">User:</dt>
                <dd class="inline">{{ shipment.user?.name || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Email:</dt>
                <dd class="inline">{{ shipment.user?.email || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Status:</dt>
                <dd class="inline">{{ shipment.shipment_status?.name || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Description:</dt>
                <dd class="inline">{{ shipment.description || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Weight:</dt>
                <dd class="inline">{{ Number(shipment.weight || 0).toFixed(2) }} {{ shipment.weight_unit || 'kg' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Weight (KGs):</dt>
                <dd class="inline">{{ Number(shipment.weight_kgs || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Seller Tracker ID:</dt>
                <dd class="inline">{{ shipment.seller_tracker_id || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Purchase Date:</dt>
                <dd class="inline">{{ shipment.purchase_date || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Site:</dt>
                <dd class="inline">{{ shipment.site?.name || '—' }}</dd>
              </div>
            </dl>
          </div>

          <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Dates</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div>
                <dt class="font-medium inline">Arrival Date:</dt>
                <dd class="inline">{{ shipment.arrival_date || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Expected Delivery:</dt>
                <dd class="inline">{{ shipment.expected_delivery_date || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Date Delivered:</dt>
                <dd class="inline">{{ shipment.date_delivered || '—' }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
          <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Financials</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div>
                <dt class="font-medium inline">Item Value (PKR):</dt>
                <dd class="inline">{{ Number(shipment.item_value_pkr || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Company Charges (PKR):</dt>
                <dd class="inline">{{ Number(shipment.company_charges || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Total (PKR):</dt>
                <dd class="inline font-bold">{{ Number(shipment.total || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Received Amount (PKR):</dt>
                <dd class="inline">{{ Number(shipment.received_amount || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Receivable COD (PKR):</dt>
                <dd class="inline">{{ Number(shipment.receivable_cod || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Amount Due (PKR):</dt>
                <dd class="inline">{{ Number(shipment.amount_due || 0).toFixed(2) }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Paid By:</dt>
                <dd class="inline">{{ shipment.paid_by || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Payment Method:</dt>
                <dd class="inline">{{ shipment.payment_method?.name || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Delivery Charges (PKR):</dt>
                <dd class="inline">{{ Number(shipment.delivery_charges || 0).toFixed(2) }}</dd>
              </div>
            </dl>
          </div>

          <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Courier & Comments</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div>
                <dt class="font-medium inline">Local Courier:</dt>
                <dd class="inline">{{ shipment.local_courier?.name || '—' }}</dd>
              </div>
              <div>
                <dt class="font-medium inline">Consolidation:</dt>
                <dd class="inline">
                  <router-link v-if="shipment.consolidation_id"
                    :to="`/admin/consolidations/${shipment.consolidation_id}`" class="text-brand-500 hover:underline">
                    {{ shipment.consolidation?.consol_id || 'View' }}
                  </router-link>
                  <span v-else>—</span>
                </dd>
              </div>
              <div>
                <dt class="font-medium inline">Comments:</dt>
                <dd class="inline">{{ shipment.comments || '—' }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Images -->
      <div v-if="shipmentImages.length" class="mt-6">
        <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Images</h3>
        <div class="flex flex-wrap gap-3">
          <div v-for="img in shipmentImages" :key="img.id" class="w-24 h-24">
            <img :src="getImageUrl(img.image_path)" class="w-full h-full object-cover rounded-lg border" />
          </div>
        </div>
      </div>

      <!-- Payment History -->
      <ShipmentPayments v-if="shipment.id" :shipmentId="shipment.id" @paymentChanged="refreshShipmentData" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ShipmentPayments from '@/components/shipments/ShipmentPayments.vue'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const shipment = ref<any>(null)
const pageTitle = ref('Shipment Details')

const shipmentImages = computed(() => shipment.value?.images || [])

function getImageUrl(path: string) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  return `${baseUrl}/storage/${path}`
}

async function fetchShipment() {
  loading.value = true
  try {
    const res = await api.get(`/admin/shipments/${route.params.id}`)
    shipment.value = res.data
    pageTitle.value = `${shipment.value.shipment_code} - Details`
  } catch (err) {
    console.error(err)
    shipment.value = null
  } finally {
    loading.value = false
  }
}

function refreshShipmentData() {
  fetchShipment()
}

function goBack() {
  router.push('/admin/shipments')
}

onMounted(fetchShipment)
</script>
