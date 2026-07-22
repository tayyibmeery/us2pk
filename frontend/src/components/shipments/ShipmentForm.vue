<template>
  <div class="space-y-6" v-if="!error">
    <PageBreadcrumb :pageTitle="pageTitle" />
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <form @submit.prevent="submitForm">
        <!-- Two columns layout -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <!-- Left Column -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold">Shipment Details</h3>

            <!-- User -->
            <div>
              <label class="block text-sm font-medium">User</label>
              <select v-model="form.user_id" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
                <option value="">Select User</option>
                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
              </select>
            </div>

            <!-- Shipment Code (read-only) -->
            <div>
              <label class="block text-sm font-medium">Shipment Code</label>
              <input v-model="form.shipment_code" type="text" readonly
                class="w-full rounded-lg border p-2 bg-gray-100 dark:bg-gray-700 cursor-not-allowed" />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium">Description</label>
              <textarea v-model="form.description" rows="2"
                class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
            </div>

            <!-- Weight & Unit -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium">Weight</label>
                <input v-model.number="form.weight" type="number" step="0.01"
                  class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
              </div>
              <div>
                <label class="block text-sm font-medium">Unit</label>
                <select v-model="form.weight_unit" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
                  <option value="ounce">Ounce</option>
                  <option value="gram">Gram</option>
                  <option value="kg">Kilogram</option>
                  <option value="pound">Pound</option>
                </select>
              </div>
            </div>

            <!-- Seller Tracker ID -->
            <div>
              <label class="block text-sm font-medium">Seller Tracker ID</label>
              <input v-model="form.seller_tracker_id" type="text"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>

            <!-- Purchase Date (flatpickr) -->
            <div>
              <label class="block text-sm font-medium">Purchase Date</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.purchase_date = el }" v-model="form.purchase_date"
                  type="text" class="w-full rounded-lg border p-2 pr-10 dark:bg-gray-800" placeholder="Select date"
                  readonly />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                  <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                      fill="currentColor" />
                  </svg>
                </span>
              </div>
            </div>

            <!-- Status (dynamic) -->
            <div>
              <label class="block text-sm font-medium">Status</label>
              <select v-model="form.shipment_status_id" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
                <option value="">Select Status</option>
                <option v-for="status in statuses" :key="status.id" :value="status.id">
                  {{ status.name }}
                </option>
              </select>
            </div>

            <!-- Arrival Date (flatpickr) -->
            <div>
              <label class="block text-sm font-medium">Arrival Date</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.arrival_date = el }" v-model="form.arrival_date" type="text"
                  class="w-full rounded-lg border p-2 pr-10 dark:bg-gray-800" placeholder="Select date" readonly />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                  <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                      fill="currentColor" />
                  </svg>
                </span>
              </div>
            </div>

            <!-- Expected Delivery Date (flatpickr) -->
            <div>
              <label class="block text-sm font-medium">Expected Delivery</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.expected_delivery_date = el }"
                  v-model="form.expected_delivery_date" type="text"
                  class="w-full rounded-lg border p-2 pr-10 dark:bg-gray-800" placeholder="Select date" readonly />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                  <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                      fill="currentColor" />
                  </svg>
                </span>
              </div>
            </div>

            <!-- Date Delivered (flatpickr) -->
            <div>
              <label class="block text-sm font-medium">Date Delivered</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.date_delivered = el }" v-model="form.date_delivered"
                  type="text" class="w-full rounded-lg border p-2 pr-10 dark:bg-gray-800" placeholder="Select date"
                  readonly />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                  <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                      fill="currentColor" />
                  </svg>
                </span>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold">Financials & Courier</h3>

            <!-- Item Value & Company Charges -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium">Item Value (PKR)</label>
                <input v-model.number="form.item_value_pkr" type="number" step="0.01"
                  class="w-full rounded-lg border p-2 dark:bg-gray-800" required @input="calculateTotals" />
              </div>
              <div>
                <label class="block text-sm font-medium">Company Charges (PKR)</label>
                <input v-model.number="form.company_charges" type="number" step="0.01"
                  class="w-full rounded-lg border p-2 dark:bg-gray-800" required @input="calculateTotals" />
              </div>
            </div>

            <!-- Total (auto) -->
            <div>
              <label class="block text-sm font-medium">Total Amount (PKR)</label>
              <input :value="totalAmount" type="text" readonly
                class="w-full rounded-lg border p-2 bg-gray-100 dark:bg-gray-700 cursor-not-allowed" />
            </div>

            Received Amount
            <div>
              <label class="block text-sm font-medium">Received Amount (PKR)</label>
              <input v-model.number="form.received_amount" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" @input="calculateTotals" />
            </div>

            <!-- Receivable COD (auto) -->
            <div>
              <label class="block text-sm font-medium">Receivable COD (PKR)</label>
              <input :value="receivableCod" type="text" readonly
                class="w-full rounded-lg border p-2 bg-gray-100 dark:bg-gray-700 cursor-not-allowed" />
            </div>

            <!-- Paid By -->
            <div>
              <label class="block text-sm font-medium">Paid By</label>
              <select v-model="form.bought_by" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
                <option value="By Company">By Company</option>
                <option value="By Customer">By Customer</option>
              </select>
            </div>

            <!-- Payment Method (dynamic) -->
            <div>
              <label class="block text-sm font-medium">Payment Method</label>
              <select v-model="form.payment_method_id" class="w-full rounded-lg border p-2 dark:bg-gray-800">
                <option value="">Select Method</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                  {{ method.name }}
                </option>
              </select>
            </div>

            <!-- Local Courier (dynamic) -->
            <div>
              <label class="block text-sm font-medium">Local Courier</label>
              <select v-model="form.local_courier_id" class="w-full rounded-lg border p-2 dark:bg-gray-800">
                <option value="">Select Courier</option>
                <option v-for="courier in localCouriers" :key="courier.id" :value="courier.id">
                  {{ courier.name }}
                </option>
              </select>
            </div>

            <!-- Delivery Charges -->
            <div>
              <label class="block text-sm font-medium">Delivery Charges (PKR)</label>
              <input v-model.number="form.delivery_charges" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>

            <!-- Site (dynamic) -->
            <div>
              <label class="block text-sm font-medium">Site</label>
              <select v-model="form.site_id" class="w-full rounded-lg border p-2 dark:bg-gray-800">
                <option value="">Select Site</option>
                <option v-for="site in sites" :key="site.id" :value="site.id">
                  {{ site.name }}
                </option>
              </select>
            </div>

            <!-- Comments -->
            <div>
              <label class="block text-sm font-medium">Comments</label>
              <textarea v-model="form.comments" rows="2"
                class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
            </div>
          </div>
        </div>

        <!-- Images Upload -->
        <div class="mt-6">
          <h3 class="text-lg font-semibold">Images</h3>
          <div class="flex flex-wrap gap-3 mb-3">
            <div v-for="(img, idx) in existingImages" :key="idx" class="relative w-20 h-20 group">
              <img :src="img.preview || getImageUrl(img.path)" class="w-full h-full object-cover rounded-lg border" />
              <button type="button" @click="removeImage(idx)"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                ×
              </button>
            </div>
          </div>
          <input type="file" multiple accept="image/*" @change="handleFileUpload"
            class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>

        <!-- Payment History (only when editing existing shipment) -->
        <ShipmentPayments v-if="isEdit && formDataLoaded && form.id" :shipmentId="form.id"
          @paymentChanged="refreshShipmentData" />

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end gap-4">
          <router-link to="/admin/shipments"
            class="rounded-lg border px-6 py-2 text-sm font-medium hover:bg-gray-50">Cancel</router-link>
          <button type="submit" :disabled="submitting"
            class="rounded-lg bg-brand-500 px-6 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50">
            {{ submitting ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
          </button>
        </div>
      </form>
    </div>
  </div>
  <div v-else class="p-6 text-center text-red-500">
    <p>Something went wrong. Please check the console.</p>
    <pre class="text-left mt-4 bg-gray-100 p-4 rounded">{{ error }}</pre>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onBeforeUnmount, nextTick, onErrorCaptured, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ShipmentPayments from '@/components/shipments/ShipmentPayments.vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'
import { usePaymentMethodStore } from '@/stores/paymentMethodStore'
import { useSiteStore } from '@/stores/siteStore'
import { useShipmentStatusStore } from '@/stores/shipmentStatusStore'
import { useLocalCourierStore } from '@/stores/localCourierStore'

const router = useRouter()
const route = useRoute()
const isEdit = computed(() => !!route.params.id)
const pageTitle = ref(isEdit.value ? 'Edit Shipment' : 'New Shipment')

// ── Pinia Stores ────────────────────────────────────────────
const paymentMethodStore = usePaymentMethodStore()
const siteStore = useSiteStore()
const shipmentStatusStore = useShipmentStatusStore()
const localCourierStore = useLocalCourierStore()

const paymentMethods = computed(() => paymentMethodStore.items)
const sites = computed(() => siteStore.items)
const statuses = computed(() => shipmentStatusStore.items)
const localCouriers = computed(() => localCourierStore.items)

// ── Error tracking ──────────────────────────────────────────
const error = ref<string | null>(null)
onErrorCaptured((err) => {
  console.error('❌ ShipmentForm error:', err)
  error.value = err.message || 'Unknown error'
  return false
})

// ── Component state ─────────────────────────────────────────
const loading = ref(false)
const submitting = ref(false)
const users = ref<any[]>([])
const formDataLoaded = ref(false)

const form = reactive({
  id: undefined as number | undefined,
  user_id: null as number | null,
  shipment_code: '',
  description: '',
  weight: 0,
  weight_unit: 'kg',
  weight_kgs: null as number | null,
  seller_tracker_id: '',
  purchase_date: '',
  shipment_status_id: null as number | null,
  arrival_date: '',
  expected_delivery_date: '',
  date_delivered: '',
  item_value_pkr: 0,
  company_charges: 0,
  received_amount: 0,
  bought_by: 'By Customer',
  payment_method_id: null as number | null,
  receivable_cod: 0,
  local_courier_id: null as number | null,
  delivery_charges: 0,
  site_id: null as number | null,
  comments: '',
})

// Image handling
const existingImages = ref<{ id?: number; path: string; preview?: string; file?: File }[]>([])
const imagesToDelete = ref<number[]>([])

// ── Computed totals ──────────────────────────────────────
const totalAmount = computed(() => {
  return (Number(form.item_value_pkr) || 0) + (Number(form.company_charges) || 0)
})
const receivableCod = computed(() => {
  const total = totalAmount.value
  const received = Number(form.received_amount) || 0
  return Math.max(0, total - received)
})

function calculateTotals() {
  // triggers computed
}

function getImageUrl(path: string) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  return `${baseUrl}/storage/${path}`
}

// ── Date picker refs ────────────────────────────────────
const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

function initDatePickers() {
  const dateFields = ['purchase_date', 'arrival_date', 'expected_delivery_date', 'date_delivered']
  dateFields.forEach(field => {
    const el = datePickerRefs.value[field]
    if (el) {
      if (flatpickrInstances[field]) flatpickrInstances[field].destroy()
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          (form as any)[field] = dateStr
        },
      })
      const val = (form as any)[field]
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
        flatpickrInstances[field].setDate(clean)
      }
    }
  })
}

function updateDatePickers() {
  const dateFields = ['purchase_date', 'arrival_date', 'expected_delivery_date', 'date_delivered']
  dateFields.forEach(field => {
    const inst = flatpickrInstances[field]
    const val = (form as any)[field]
    if (inst && val) {
      const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
      inst.setDate(clean)
    }
  })
}

// ── Image handlers ──────────────────────────────────────
function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement
  if (!target.files) return
  Array.from(target.files).forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      existingImages.value.push({
        file: file,
        preview: e.target?.result as string,
        path: '',
      })
    }
    reader.readAsDataURL(file)
  })
  target.value = ''
}

function removeImage(index: number) {
  const img = existingImages.value[index]
  if (img.id) imagesToDelete.value.push(img.id)
  existingImages.value.splice(index, 1)
}

// ── Load lookup data ────────────────────────────────────
async function fetchLookupData() {
  try {
    await Promise.all([
      paymentMethodStore.fetchItems(1, { per_page: 100 }),
      siteStore.fetchItems(1, { per_page: 100 }),
      shipmentStatusStore.fetchItems(1, { per_page: 100 }),
      localCourierStore.fetchItems(1, { per_page: 100 }),
    ])
  } catch (err) {
    console.error('Failed to load dropdowns', err)
  }
}

async function fetchUsers() {
  try {
    const res = await api.get('/admin/users?per_page=1000')
    users.value = res.data.data || res.data
  } catch (err) {
    console.error('Failed to load users', err)
  }
}

// ── Load shipment for edit ─────────────────────────────
async function loadShipment() {
  if (!isEdit.value) {
    // Generate shipment code for new
    if (form.user_id) generateShipmentCode()
    return
  }
  loading.value = true
  try {
    const res = await api.get(`/admin/shipments/${route.params.id}`)
    const data = res.data
    // Populate form (skip nested objects)
    Object.keys(form).forEach(key => {
      if (key in data && typeof data[key] !== 'object') {
        (form as any)[key] = data[key] ?? null
      }
    })
    // Load images
    existingImages.value = (data.images || []).map((img: any) => ({
      id: img.id,
      path: img.image_path,
      preview: '',
    }))
    formDataLoaded.value = true
    await nextTick()
    updateDatePickers()
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

// ── Generate shipment code ─────────────────────────────
async function generateShipmentCode() {
  if (!form.user_id) return
  try {
    const res = await api.get('/admin/shipments/generate-shipment-code', {
      params: { user_id: form.user_id }
    })
    form.shipment_code = res.data.shipment_code
  } catch (err) {
    console.error('Failed to generate code', err)
  }
}

// ── Submit form ──────────────────────────────────────────
async function submitForm() {
  if (!form.user_id) {
    alert('Please select a user.')
    return
  }
  submitting.value = true
  try {
    const payload: any = { ...form }
    // Remove id for new
    if (!isEdit.value) delete payload.id
      // Convert numeric strings
      ;['weight', 'item_value_pkr', 'company_charges', 'received_amount', 'receivable_cod', 'delivery_charges'].forEach(f => {
        payload[f] = Number(payload[f]) || 0
      })

    // Prepare images
    const imageFiles = existingImages.value.filter(img => img.file).map(img => img.file!)
    const imageIds = existingImages.value.filter(img => img.id).map(img => img.id!)

    let response
    if (isEdit.value) {
      response = await api.put(`/admin/shipments/${form.id}`, payload, {
        params: { keep_images: imageIds }
      })
    } else {
      response = await api.post('/admin/shipments', payload)
    }

    // Upload new images if any
    if (imageFiles.length) {
      const fd = new FormData()
      imageFiles.forEach(file => fd.append('images[]', file))
      const shipmentId = response.data.id || form.id
      await api.post(`/admin/shipments/${shipmentId}/images`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    // Delete images marked for removal
    if (imagesToDelete.value.length && isEdit.value) {
      await Promise.all(imagesToDelete.value.map(id => api.delete(`/admin/shipment-images/${id}`)))
    }

    router.push('/admin/shipments')
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to save shipment')
  } finally {
    submitting.value = false
  }
}

// ── Refresh after payment changes ──────────────────────
function refreshShipmentData() {
  if (isEdit.value) loadShipment()
}

// ── Watch for user changes to generate code ─────────────
watch(() => form.user_id, (newVal) => {
  if (newVal && !isEdit.value) generateShipmentCode()
})

// ── Lifecycle ────────────────────────────────────────────
onMounted(async () => {
  await fetchLookupData()
  await fetchUsers()
  await loadShipment()
  await nextTick()
  initDatePickers()
})

onBeforeUnmount(() => {
  Object.values(flatpickrInstances).forEach(inst => {
    if (inst) inst.destroy()
  })
})
</script>
