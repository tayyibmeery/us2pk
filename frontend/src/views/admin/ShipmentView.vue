<template>
  <PageBreadcrumb :pageTitle="pageTitle" />

  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <!-- Loading & Error States -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
      <p class="mt-2 text-gray-500 dark:text-gray-400">Loading shipment details…</p>
    </div>
    <div v-else-if="!shipment" class="text-center py-12 text-gray-400">Shipment not found.</div>

    <div v-else>
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90 flex items-center gap-3">
            {{ shipment.shipment_code }}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" :class="{
              'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': isCleared,
              'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300': !isCleared,
            }">
              {{ isCleared ? 'Cleared' : 'Pending' }}
            </span>
          </h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Created {{ formatDate(shipment.created_at) }} • Updated {{ formatDate(shipment.updated_at) }}
          </p>
        </div>
        <div class="flex gap-2">
          <button @click="openEditModal"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </button>
          <button @click="goBack"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
          </button>
        </div>
      </div>

      <!-- Detail Cards Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-6">
          <!-- Shipment Details -->
          <div
            class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 p-5 shadow-sm">
            <h3
              class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              Shipment Details
            </h3>
            <dl class="grid grid-cols-1 gap-3 text-sm">
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">User</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.user?.name || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.user?.email || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="text-gray-800 dark:text-white/90">
                  <span
                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                    {{ shipment.shipment_status?.name || '—' }}
                  </span>
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Description</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.description || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Weight</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.weight || 0).toFixed(2) }}
                  {{ shipment.weight_unit || 'kg' }}
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Weight (KGs)</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.weight_kgs || 0).toFixed(2) }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Seller Tracker ID</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ (shipment.seller_tracker_id) || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Purchase Date</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ formatDate(shipment.purchase_date) || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Site</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.site?.name || '—' }}</dd>
              </div>
            </dl>
          </div>

          <!-- Dates -->
          <div
            class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 p-5 shadow-sm">
            <h3
              class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Dates
            </h3>
            <dl class="grid grid-cols-1 gap-3 text-sm">
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <!-- <dt class="font-medium text-gray-500 dark:text-gray-400">Arrival Date</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ formatDate(shipment.arrival_date) || '—' }}</dd> -->
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Expected Delivery</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ formatDate(shipment.expected_delivery_date) || '—' }}
                </dd>
              </div>
              <div class="flex justify-between py-1">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Date Delivered</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ formatDate(shipment.date_delivered) || '—' }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
          <!-- Financials -->
          <div
            class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 p-5 shadow-sm">
            <h3
              class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 3v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
              Financials
            </h3>
            <dl class="grid grid-cols-1 gap-3 text-sm">
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Item Value</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.item_value_pkr || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Company Charges</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.company_charges || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Total Payable</dt>
                <dd class="font-bold text-gray-800 dark:text-white/90">{{ Number(shipment.total || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Received Amount</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.received_amount || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Receivable COD</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.receivable_cod || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <!-- <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Amount Due</dt>
                <dd class="font-semibold"
                  :class="Number(shipment.amount_due || 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                  {{ Number(shipment.amount_due || 0).toFixed(2) }} PKR
                </dd>
              </div> -->
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Paid By</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.bought_by || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Payment Method</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.payment_method?.name || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Delivery Charges</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ Number(shipment.delivery_charges || 0).toFixed(2) }} PKR
                </dd>
              </div>
            </dl>
          </div>

          <!-- Courier & Comments -->
          <div
            class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 p-5 shadow-sm">
            <h3
              class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Courier & Comments
            </h3>
            <dl class="grid grid-cols-1 gap-3 text-sm">
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Local Courier</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.local_courier?.name || '—' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Consolidation</dt>
                <dd class="text-gray-800 dark:text-white/90">
                  <router-link v-if="shipment.consolidation_id"
                    :to="`/admin/consolidations/${shipment.consolidation_id}`" class="text-brand-500 hover:underline">
                    {{ shipment.consolidation?.consol_id || 'View' }}
                  </router-link>
                  <span v-else>—</span>
                </dd>
              </div>
              <div class="flex justify-between py-1">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Comments</dt>
                <dd class="text-gray-800 dark:text-white/90">{{ shipment.comments || '—' }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Images -->
      <div v-if="shipmentImages.length" class="mt-6">
        <h3
          class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Images
        </h3>
        <div class="flex flex-wrap gap-3">
          <div v-for="img in shipmentImages" :key="img.id"
            class="w-24 h-24 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
            <img :src="getImageUrl(img.image_path)" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>

      <!-- Payment History -->
      <ShipmentPayments v-if="shipment.id" :shipmentId="shipment.id" :totalPayable="Number(shipment.total || 0)"
        :totalReceived="Number(shipment.received_amount || 0)" :amountDue="Number(shipment.amount_due || 0)"
        :receivableCod="Number(shipment.receivable_cod || 0)" @paymentChanged="refreshShipmentData" />
    </div>

    <!-- Edit Modal -->
    <ShipmentFormModal v-if="showEditModal" :isOpen="showEditModal" :initialData="shipment" @close="closeEditModal"
      @save="onSaved" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ShipmentPayments from '@/components/shipments/ShipmentPayments.vue'
import ShipmentFormModal from '@/components/admin/ShipmentFormModal.vue'


const router = useRouter()
const route = useRoute()
const loading = ref(false)
const shipment = ref<any>(null)
const pageTitle = ref('Shipment Details')
const showEditModal = ref(false)

const shipmentImages = computed(() => shipment.value?.images || [])
const isCleared = computed(() => Number(shipment.value?.amount_due || 0) <= 0)

function getImageUrl(path: string) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_API_URL || 'https://us2pk.com'
  return `${baseUrl}/storage/${path}`
}

// function formatDate(date: string | null | undefined) {
//   if (!date) return '—';
//   return new Date(date).toLocaleDateString('en-GB');
// }
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

function openEditModal() {
  showEditModal.value = true
}

function closeEditModal() {
  showEditModal.value = false
}

function onSaved() {
  closeEditModal()
  refreshShipmentData()
}

onMounted(fetchShipment)
</script>
