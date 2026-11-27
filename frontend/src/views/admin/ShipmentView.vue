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
            <ShipmentStatusBadge :status="shipment.shipment_status?.name" />
          </h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Created {{ formatDate(shipment.created_at) }} • Updated {{ formatDate(shipment.updated_at) }}
          </p>
        </div>

        <div class="flex flex-wrap gap-2">
          <!-- ✅ View existing invoice -->
          <router-link v-if="existingInvoice" :to="`/admin/invoices`"
            class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-300 dark:hover:bg-emerald-900/30 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Invoice {{ existingInvoice.invoice_no }}
          </router-link>

          <!-- ✅ Generate invoice button -->
          <button v-else-if="canGenerateInvoice" @click="generateInvoice" :disabled="generatingInvoice"
            class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
            <svg v-if="!generatingInvoice" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span v-else
              class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            {{ generatingInvoice ? 'Creating…' : 'Generate Invoice' }}
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

      <!-- ✅ Damaged warning banner -->
      <div v-if="isDamaged"
        class="mb-6 rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4 flex items-start gap-3">
        <svg class="h-6 w-6 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
          stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        <div>
          <p class="text-sm font-semibold text-red-700 dark:text-red-400">Shipment Damaged — Returned</p>
          <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
            Inventory has been written off (PKR {{ Number(shipment.item_value_pkr || 0).toFixed(2) }}) and the payment
            of PKR {{ Number(shipment.total || 0).toFixed(2) }} has been fully refunded to the customer. No revenue
            was recognised.
          </p>
        </div>
      </div>

      <!-- Payments (hidden form on this page) -->
      <div class="mb-6">
        <ShipmentPayments v-if="shipment.id && !isDamaged" :shipmentId="shipment.id"
          :totalPayable="Number(shipment.total || 0)" :totalReceived="Number(shipment.received_amount || 0)"
          :amountDue="Number(shipment.amount_due || 0)" :receivableCod="Number(shipment.receivable_cod || 0)"
          :isDelivered="!!shipment.date_delivered" :hideForm="true" @paymentChanged="refreshShipmentData" />
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
                  <ShipmentStatusBadge :status="shipment.shipment_status?.name" />
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Description</dt>
                <dd class="text-gray-800 dark:text-white/90 max-w-[200px]">
                  <TruncateTooltip :text="shipment.description || '—'" :limit="20" />
                </dd>
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
              <span v-if="isDamaged"
                class="ml-auto inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Written Off
              </span>
            </h3>
            <dl class="grid grid-cols-1 gap-3 text-sm">
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Item Value</dt>
                <dd
                  :class="isDamaged ? 'text-gray-400 line-through dark:text-gray-500' : 'text-gray-800 dark:text-white/90'">
                  {{ Number(shipment.item_value_pkr || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Company Charges</dt>
                <dd
                  :class="isDamaged ? 'text-gray-400 line-through dark:text-gray-500' : 'text-gray-800 dark:text-white/90'">
                  {{ Number(shipment.company_charges || 0).toFixed(2) }} PKR
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Total Payable</dt>
                <dd
                  :class="isDamaged ? 'font-bold text-red-600 dark:text-red-400' : 'font-bold text-gray-800 dark:text-white/90'">
                  <template v-if="isDamaged">PKR 0.00 <span class="text-xs font-normal">(Refunded)</span></template>
                  <template v-else>{{ Number(shipment.total || 0).toFixed(2) }} PKR</template>
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Received Amount</dt>
                <dd :class="isDamaged ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white/90'">
                  <template v-if="isDamaged">Refunded</template>
                  <template v-else>{{ Number(shipment.received_amount || 0).toFixed(2) }} PKR</template>
                </dd>
              </div>
              <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700/50">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Receivable COD</dt>
                <dd :class="isDamaged ? 'text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-white/90'">
                  {{ Number(shipment.receivable_cod || 0).toFixed(2) }} PKR
                </dd>
              </div>
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
                <dd class="text-gray-800 dark:text-white/90 max-w-[200px]">
                  <button v-if="hasComments" @click="openCommentsModal"
                    class="text-brand-500 hover:text-brand-600 hover:underline flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    {{ commentCount }} comment{{ commentCount > 1 ? 's' : '' }}
                  </button>
                  <span v-else>—</span>
                </dd>
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
            class="w-24 h-24 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm cursor-pointer hover:opacity-80 transition"
            @click="openImageViewer(img)">
            <img :src="getImageUrl(img.image_path)" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <ImageModal v-if="showImageModal" :isOpen="showImageModal" :imageUrl="selectedImageUrl" @close="closeImageViewer" />
    <CommentsModal v-if="showCommentsModal" :isOpen="showCommentsModal" :comments="shipment?.comments"
      @close="closeCommentsModal" />
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
import TruncateTooltip from '@/components/common/TruncateTooltip.vue'
import ImageModal from '@/components/common/ImageModal.vue'
import CommentsModal from '@/components/common/CommentsModal.vue'
import ShipmentStatusBadge from '@/components/user/ShipmentStatusBadge.vue'
import { useInvoiceStore } from '@/stores/invoiceStore'
import { useToastStore } from '@/stores/toastStore'

const router = useRouter()
const route = useRoute()

const invoiceStore = useInvoiceStore()
const toastStore = useToastStore()

const loading = ref(false)
const generatingInvoice = ref(false)
const shipment = ref<any>(null)
const pageTitle = ref('Shipment Details')
const showEditModal = ref(false)
const showImageModal = ref(false)
const showCommentsModal = ref(false)
const selectedImageUrl = ref('')

/* ── Computed ─────────────────────────────────────────── */

const shipmentImages = computed(() => shipment.value?.images || [])

const isDamaged = computed(
  () => shipment.value?.shipment_status?.name === 'Damaged - Returned'
)

/**
 * Existing invoice for this shipment (loaded via the `invoices` pivot relation).
 * Returns the first invoice or null.
 */
const existingInvoice = computed(() => {
  const list = shipment.value?.invoices
  if (Array.isArray(list) && list.length > 0) return list[0]
  return null
})

/**
 * Show the "Generate Invoice" button only when:
 *  - the shipment has COD to invoice
 *  - it hasn't been invoiced yet
 *  - it isn't marked as damaged/refunded
 */
const canGenerateInvoice = computed(() => {
  if (!shipment.value) return false
  if (existingInvoice.value) return false
  if (isDamaged.value) return false
  return Number(shipment.value.receivable_cod || 0) > 0
})

const hasComments = computed(() => {
  if (!shipment.value?.comments) return false
  if (Array.isArray(shipment.value.comments)) {
    return shipment.value.comments.length > 0
  }
  return String(shipment.value.comments).trim().length > 0
})

const commentCount = computed(() => {
  if (!shipment.value?.comments) return 0
  if (Array.isArray(shipment.value.comments)) {
    return shipment.value.comments.length
  }
  return String(shipment.value.comments).split(/\n+/).filter((c: string) => c.trim()).length
})

/* ── Helpers ──────────────────────────────────────────── */

function getImageUrl(path: string) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com'
  return `${baseUrl}/storage/${path}`
}

function formatDate(date: string | null | undefined) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-GB')
}

function openImageViewer(img: any) {
  selectedImageUrl.value = getImageUrl(img.image_path)
  showImageModal.value = true
}

function closeImageViewer() {
  showImageModal.value = false
  selectedImageUrl.value = ''
}

function openCommentsModal() {
  showCommentsModal.value = true
}

function closeCommentsModal() {
  showCommentsModal.value = false
}

/* ── Data fetch ───────────────────────────────────────── */

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

/* ── ✅ Generate invoice ─────────────────────────────── */

async function generateInvoice() {
  if (!shipment.value || generatingInvoice.value) return

  generatingInvoice.value = true
  try {
    const invoice = await invoiceStore.createFromShipment(shipment.value.id, {
      date: new Date().toISOString().split('T')[0],
    })

    toastStore.success(`Invoice ${invoice.invoice_no} created successfully`)

    // Refresh so the header switches to "View Invoice"
    await fetchShipment()
  } catch (err: any) {
    const message =
      err.response?.data?.message || 'Failed to generate invoice. Please try again.'
    toastStore.error(message)
    console.error('Generate invoice failed:', err)
  } finally {
    generatingInvoice.value = false
  }
}

/* ── Edit modal (unused in header, kept for future) ───── */

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
