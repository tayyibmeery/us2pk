<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Shipment' : 'Add Shipment'"
    :subtitle="formData.id ? 'Update the shipment details below.' : 'Fill in the details to add a new shipment.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" :loading="saving" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- User - Searchable Dropdown -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            User <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <input type="text" v-model="userSearch" @input="onUserSearch" @focus="onUserSearchFocus"
              placeholder="Search by name, email or pcode..."
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              :disabled="usersLoading" autocomplete="off" />
            <div v-if="showUserDropdown && filteredUsers.length > 0"
              class="absolute z-50 mt-1 w-full max-h-60 overflow-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 shadow-lg">
              <div v-for="user in filteredUsers" :key="user.id" @click="selectUser(user)"
                class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex flex-col"
                :class="{ 'bg-brand-50 dark:bg-brand-900/20': formData.user_id === user.id }">
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }} | PCODE:
                  {{ user.pcode || 'N/A' }}</span>
              </div>
            </div>
            <div v-else-if="showUserDropdown && !usersLoading && filteredUsers.length === 0 && userSearch.length > 0"
              class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 shadow-lg p-4 text-center text-gray-500 dark:text-gray-400">
              No users found
            </div>
          </div>
          <p v-if="usersLoading" class="text-xs text-gray-400 mt-1">
            <span class="inline-block animate-spin mr-1">⟳</span> Loading users...
          </p>
          <p v-if="formData.user_id && selectedUser" class="text-xs text-green-600 dark:text-green-400 mt-1">
            ✓ Selected: {{ selectedUser.name }} ({{ selectedUser.email }})
          </p>
        </div>

        <!-- shipment_code (read-only) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Shipment Code
          </label>
          <input v-model="formData.shipment_code" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Description & Comments -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Description
          </label>
          <textarea v-model="formData.description" rows="2" placeholder="Brief description of the shipment"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Comments
          </label>
          <textarea v-model="formData.comments" rows="2" placeholder="Internal comments"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Weight & Unit -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Weight <span class="text-red-500">*</span>
          </label>
          <input v-model.number="formData.weight" type="number" step="0.01" placeholder="e.g. 1.5"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required />
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Weight Unit <span class="text-red-500">*</span>
          </label>
          <select v-model="formData.weight_unit"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            required>
            <option value="ounce">Ounce</option>
            <option value="gram">Gram</option>
            <option value="kg">Kilogram</option>
            <option value="pound">Pound</option>
          </select>
        </div>

        <!-- Seller Tracker ID -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Seller Tracker ID
          </label>
          <input v-model="formData.seller_tracker_id" type="text" placeholder="e.g. 1Z9999999999999999"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Site (dynamic) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Site
          </label>
          <select v-model="formData.site_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="sitesLoading">
            <option value="">Select site</option>
            <option v-for="site in sites" :key="site.id" :value="site.id">
              {{ site.name }}
            </option>
          </select>
          <p v-if="sitesLoading" class="text-xs text-gray-400 mt-1">Loading sites...</p>
        </div>

        <!-- Purchase Date (hidden - auto-set to today) -->
        <input type="hidden" ref="purchaseDateInput" v-model="formData.purchase_date" />

        <!-- Status - Only show when editing -->
        <div v-if="isEdit">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.shipment_status_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="statusesLoading" required>
            <option value="">Select status</option>
            <option v-for="status in statuses" :key="status.id" :value="status.id">
              {{ status.name }}
            </option>
          </select>
          <p v-if="statusesLoading" class="text-xs text-gray-400 mt-1">Loading statuses...</p>
        </div>

        <!-- Hidden status field for creation - auto-set based on bought_by -->
        <input v-if="!isEdit" type="hidden" v-model="formData.shipment_status_id" />

        <!-- Expected Delivery Date (flatpickr) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Expected Delivery Date
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.expected_delivery_date = el }"
              v-model="formData.expected_delivery_date" type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Select date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Bought By (formerly Paid By) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Bought By <span class="text-red-500">*</span>
          </label>
          <select v-model="formData.bought_by" @change="onBoughtByChange"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            required>
            <option value="By Company">By Company</option>
            <option value="By Customer">By Customer</option>
          </select>
        </div>

        <!-- Item Value & Company Charges -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Item Value (PKR) <span class="text-red-500">*</span>
          </label>
          <input v-model.number="formData.item_value_pkr" type="number" step="0.01" placeholder="0.00" :class="[
            'h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800',
            isItemValueDisabled
              ? 'bg-gray-100 text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400'
              : 'bg-transparent text-gray-800 dark:bg-gray-900 dark:text-white/90'
          ]" :disabled="isItemValueDisabled" required @input="calculateTotalAndDue" />
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Company Charges (PKR) <span class="text-red-500">*</span>
          </label>
          <input v-model.number="formData.company_charges" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required @input="calculateTotalAndDue" />
        </div>

        <!-- Total Amount Payable (auto) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Total Amount Payable (PKR)
          </label>
          <input :value="totalAmount" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Received Amount - NOW READ-ONLY ON EDIT -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Received Amount (PKR)
          </label>
          <input v-model.number="formData.received_amount" :readonly="isEdit" type="number" step="0.01"
            placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          <p v-if="isEdit" class="text-xs text-gray-400 mt-1">Can't update it from here. Update it from payments.</p>
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Payment Method
          </label>
          <select v-model="formData.payment_method_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="paymentMethodsLoading">
            <option value="">Select method</option>
            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
              {{ method.name }}
            </option>
          </select>
          <p v-if="paymentMethodsLoading" class="text-xs text-gray-400 mt-1">Loading payment methods...</p>
        </div>

        <!-- Receivable COD (auto) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Receivable COD (PKR)
          </label>
          <input :value="receivableCod" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Local Courier (dynamic) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Local Courier
          </label>
          <select v-model="formData.local_courier_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="localCouriersLoading">
            <option value="">Select courier</option>
            <option v-for="courier in localCouriers" :key="courier.id" :value="courier.id">
              {{ courier.name }}
            </option>
          </select>
          <p v-if="localCouriersLoading" class="text-xs text-gray-400 mt-1">Loading couriers...</p>
        </div>

        <!-- Delivery Charges -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Delivery Charges (PKR)
          </label>
          <input v-model.number="formData.delivery_charges" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Image Upload Section -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Product Images
          </label>

          <div v-if="existingImages.length" class="flex flex-wrap gap-2 mb-2">
            <div v-for="img in existingImages" :key="img.id" class="relative w-20 h-20 group">
              <img :src="getImageUrl(img.image_path)"
                class="w-full h-full object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
              <button type="button" @click="removeExistingImage(img.id)"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition opacity-0 group-hover:opacity-100 focus:opacity-100"
                title="Remove image">
                ×
              </button>
            </div>
          </div>

          <div v-if="newImagePreviews.length" class="flex flex-wrap gap-2 mb-2">
            <div v-for="(preview, index) in newImagePreviews" :key="index" class="relative w-20 h-20 group">
              <img :src="preview"
                class="w-full h-full object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
              <button type="button" @click="removeNewImage(index)"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition">
                ×
              </button>
            </div>
          </div>

          <input type="file" multiple accept="image/*" @change="handleNewImageUpload"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          <p v-if="!existingImages.length && !newImagePreviews.length" class="text-xs text-gray-400 mt-1">
            No images uploaded yet.
          </p>
          <p v-else class="text-xs text-gray-400 mt-1">
            {{ existingImages.length + newImagePreviews.length }} image(s) (click × to remove)
          </p>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'
import FormModal from '@/components/common/FormModal.vue'
import api from '@/services/api'
import { usePaymentMethodStore } from '@/stores/paymentMethodStore'
import { useSiteStore } from '@/stores/siteStore'
import { useShipmentStatusStore } from '@/stores/shipmentStatusStore'
import { useLocalCourierStore } from '@/stores/localCourierStore'
import type { Shipment } from '@/types'
import { useShipmentStore } from '@/stores/shipmentStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: Shipment | null
}>()

const shipmentStore = useShipmentStore()
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'saved'): void
}>()

const saving = ref(false)

const existingImages = ref<{ id: number; image_path: string }[]>([])
const imagesToDelete = ref<number[]>([])
const newImages = ref<File[]>([])
const newImagePreviews = ref<string[]>([])

const getImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  return `${baseUrl}/storage/${path}`
}

const paymentMethodStore = usePaymentMethodStore()
const siteStore = useSiteStore()
const shipmentStatusStore = useShipmentStatusStore()
const localCourierStore = useLocalCourierStore()

// User search related refs
const userSearch = ref('')
const showUserDropdown = ref(false)
const allUsers = ref<any[]>([])
const filteredUsers = ref<any[]>([])
const userSearchTimeout = ref<number | null>(null)
const usersLoading = ref(false)

// Other refs
const paymentMethods = ref<any[]>([])
const paymentMethodsLoading = ref(false)
const sites = ref<any[]>([])
const sitesLoading = ref(false)
const statuses = ref<any[]>([])
const statusesLoading = ref(false)
const localCouriers = ref<any[]>([])
const localCouriersLoading = ref(false)

const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

const defaultForm = (): Partial<Shipment> => ({
  user_id: undefined,
  shipment_code: '',
  description: '',
  weight: 0,
  weight_unit: 'kg',
  seller_tracker_id: '',
  site_id: null,
  purchase_date: new Date().toISOString().split('T')[0],
  shipment_status_id: null,
  expected_delivery_date: '',
  item_value_pkr: 0,
  company_charges: 0,
  received_amount: 0,
  bought_by: 'By Customer',
  payment_method_id: null,
  receivable_cod: 0,
  local_courier_id: null,
  delivery_charges: 0,
  comments: '',
})

const formData = ref<Partial<Shipment>>(defaultForm())

// Computed
const totalAmount = computed(() => {
  const item = parseFloat(formData.value.item_value_pkr as any) || 0
  const charges = parseFloat(formData.value.company_charges as any) || 0
  return item + charges
})

const receivableCod = computed(() => {
  const total = totalAmount.value
  const received = parseFloat(formData.value.received_amount as any) || 0
  return Math.max(0, total - received)
})

const isItemValueDisabled = computed(() => formData.value.bought_by === 'By Customer')

const selectedUser = computed(() => {
  return allUsers.value.find(u => u.id === formData.value.user_id)
})

const isEdit = computed(() => !!props.initialData?.id)

// Handle Bought By change - auto-set status
const onBoughtByChange = () => {
  if (!isEdit.value) {
    // Only auto-set status when creating new shipment
    const paidBy = formData.value.bought_by
    let statusName = ''

    if (paidBy === 'By Company') {
      statusName = 'Bought by Company'
    } else if (paidBy === 'By Customer') {
      statusName = 'Bought by Customer'
    }

    // Find the status ID by name
    const status = statuses.value.find(s => s.name === statusName)
    if (status) {
      formData.value.shipment_status_id = status.id
    }
  }

  // Handle item value disabled state
  if (formData.value.bought_by === 'By Customer') {
    formData.value.item_value_pkr = 0
  }
}

// Watch for statuses loading and auto-set status
watch([() => statuses.value, () => formData.value.bought_by, isEdit], () => {
  if (!isEdit.value && statuses.value.length > 0 && formData.value.bought_by) {
    const paidBy = formData.value.bought_by
    let statusName = ''

    if (paidBy === 'By Company') {
      statusName = 'Bought by Company'
    } else if (paidBy === 'By Customer') {
      statusName = 'Bought by Customer'
    }

    const status = statuses.value.find(s => s.name === statusName)
    if (status && !formData.value.shipment_status_id) {
      formData.value.shipment_status_id = status.id
    }
  }
})

const calculateTotalAndDue = () => { /* triggers computed */ }

const resetForm = () => {
  formData.value = defaultForm()
  existingImages.value = []
  imagesToDelete.value = []
  newImages.value = []
  newImagePreviews.value = []
  userSearch.value = ''
  showUserDropdown.value = false
}

// User search functions
const onUserSearch = () => {
  if (userSearchTimeout.value) {
    clearTimeout(userSearchTimeout.value)
  }
  userSearchTimeout.value = setTimeout(() => {
    searchUsers(userSearch.value)
  }, 300) as unknown as number
}

const onUserSearchFocus = () => {
  if (allUsers.value.length === 0) {
    fetchAllUsers()
  } else {
    showUserDropdown.value = true
    filterUsers(userSearch.value)
  }
}

const searchUsers = async (query: string) => {
  if (!query || query.length < 1) {
    if (allUsers.value.length === 0) {
      await fetchAllUsers()
    } else {
      filterUsers('')
    }
    return
  }

  try {
    usersLoading.value = true
    const response = await api.get('/admin/users/search', {
      params: {
        search: query,
        per_page: 20
      }
    })
    filteredUsers.value = response.data.data || response.data || []
    showUserDropdown.value = true
  } catch (error) {
    console.error('Failed to search users:', error)
    // Fallback to client-side filtering
    filterUsers(query)
  } finally {
    usersLoading.value = false
  }
}

const filterUsers = (query: string) => {
  if (!query || query.trim() === '') {
    filteredUsers.value = allUsers.value
  } else {
    const searchTerm = query.toLowerCase().trim()
    filteredUsers.value = allUsers.value.filter(user =>
      user.name?.toLowerCase().includes(searchTerm) ||
      user.email?.toLowerCase().includes(searchTerm) ||
      user.pcode?.toLowerCase().includes(searchTerm)
    )
  }
  showUserDropdown.value = true
}

const fetchAllUsers = async () => {
  if (allUsers.value.length > 0) {
    filteredUsers.value = allUsers.value
    showUserDropdown.value = true
    return
  }

  usersLoading.value = true
  try {
    const response = await api.get('/admin/users', {
      params: { per_page: 1000 }
    })
    allUsers.value = response.data.data || response.data || []
    filteredUsers.value = allUsers.value
    showUserDropdown.value = true
  } catch (error) {
    console.error('Failed to fetch users:', error)
  } finally {
    usersLoading.value = false
  }
}

const selectUser = (user: any) => {
  formData.value.user_id = user.id
  userSearch.value = `${user.name} (${user.email})`
  showUserDropdown.value = false

  // Trigger shipment code generation
  generateShipmentCode(user.id)
}

const generateShipmentCode = async (userId: number) => {
  try {
    const res = await api.get('/admin/shipments/generate-shipment-code', {
      params: { user_id: userId }
    })
    formData.value.shipment_code = res.data.shipment_code
  } catch (e) {
    console.error('Failed to generate shipment code', e)
  }
}

// Close dropdown when clicking outside
const closeUserDropdown = (event: Event) => {
  const target = event.target as HTMLElement
  const dropdown = target.closest('.relative')
  if (!dropdown) {
    showUserDropdown.value = false
  }
}

const fetchLookupData = async () => {
  paymentMethodsLoading.value = true
  try {
    await paymentMethodStore.fetchItems(1, { per_page: 100 })
    paymentMethods.value = paymentMethodStore.items
  } catch (e) {
    console.error('Failed to load payment methods', e)
  } finally {
    paymentMethodsLoading.value = false
  }

  sitesLoading.value = true
  try {
    await siteStore.fetchItems(1, { per_page: 100 })
    sites.value = siteStore.items
  } catch (e) {
    console.error('Failed to load sites', e)
  } finally {
    sitesLoading.value = false
  }

  statusesLoading.value = true
  try {
    await shipmentStatusStore.fetchItems(1, { per_page: 100 })
    statuses.value = shipmentStatusStore.items
    // Auto-set status after loading
    if (!isEdit.value && formData.value.bought_by) {
      const paidBy = formData.value.bought_by
      let statusName = paidBy === 'By Company' ? 'Bought by Company' : 'Bought by Customer'
      const status = statuses.value.find(s => s.name === statusName)
      if (status) {
        formData.value.shipment_status_id = status.id
      }
    }
  } catch (e) {
    console.error('Failed to load statuses', e)
  } finally {
    statusesLoading.value = false
  }

  localCouriersLoading.value = true
  try {
    await localCourierStore.fetchItems(1, { per_page: 100 })
    localCouriers.value = localCourierStore.items
  } catch (e) {
    console.error('Failed to load local couriers', e)
  } finally {
    localCouriersLoading.value = false
  }
}

const initDatePickers = () => {
  const dateFields = ['purchase_date', 'expected_delivery_date']
  dateFields.forEach(field => {
    const el = datePickerRefs.value[field]
    if (el) {
      if (flatpickrInstances[field]) flatpickrInstances[field].destroy()
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          (formData.value as any)[field] = dateStr
        },
      })
      const val = (formData.value as any)[field]
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
        flatpickrInstances[field].setDate(clean)
      }
    }
  })
}

watch(() => props.isOpen, async (open) => {
  if (open) {
    await nextTick()
    initDatePickers()
    await fetchAllUsers()
    await fetchLookupData()
  } else {
    Object.values(flatpickrInstances).forEach((inst: any) => inst.destroy())
    showUserDropdown.value = false
  }
})

watch(() => props.initialData, (newVal) => {
  if (newVal) {
    const cleaned: Partial<Shipment> = {}
    for (const key of Object.keys(newVal)) {
      const val = (newVal as any)[key]
      if (val !== null && val !== undefined && typeof val !== 'object') {
        (cleaned as any)[key] = val
      } else if (val === null) {
        (cleaned as any)[key] = null
      }
    }

    if (!cleaned.purchase_date) {
      cleaned.purchase_date = new Date().toISOString().split('T')[0]
    }
    formData.value = cleaned

    // Set user search text if user exists
    if (newVal.user) {
      userSearch.value = `${newVal.user.name} (${newVal.user.email})`
      // Add user to allUsers if not already there
      if (!allUsers.value.find(u => u.id === newVal.user?.id)) {
        allUsers.value.push(newVal.user)
      }
    }

    if (formData.value.bought_by === 'By Customer') {
      formData.value.item_value_pkr = 0
    }

    existingImages.value = Array.isArray(newVal.images)
      ? newVal.images.map((img: any) => ({ id: img.id, image_path: img.image_path }))
      : []

    newImages.value = []
    newImagePreviews.value = []
    imagesToDelete.value = []
  } else {
    resetForm()
  }
}, { immediate: true })

const removeExistingImage = (imageId: number) => {
  imagesToDelete.value.push(imageId)
  existingImages.value = existingImages.value.filter(img => img.id !== imageId)
}

const handleNewImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    Array.from(target.files).forEach(file => {
      newImages.value.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        if (e.target?.result) newImagePreviews.value.push(e.target.result as string)
      }
      reader.readAsDataURL(file)
    })
  }
  target.value = ''
}

const removeNewImage = (index: number) => {
  newImages.value.splice(index, 1)
  newImagePreviews.value.splice(index, 1)
}

const close = () => {
  if (saving.value) return
  emit('close')
}

const save = async () => {
  if (saving.value) return

  // Validate required fields
  if (!formData.value.user_id) {
    alert('Please select a user')
    return
  }

  const cleanData: Partial<Shipment> = {
    user_id: formData.value.user_id,
    shipment_code: formData.value.shipment_code,
    description: formData.value.description,
    comments: formData.value.comments,
    weight: formData.value.weight,
    weight_unit: formData.value.weight_unit,
    seller_tracker_id: formData.value.seller_tracker_id,
    site_id: formData.value.site_id,
    purchase_date: formData.value.purchase_date,
    shipment_status_id: formData.value.shipment_status_id,
    expected_delivery_date: formData.value.expected_delivery_date,
    payment_method_id: formData.value.payment_method_id,
    bought_by: formData.value.bought_by,
    item_value_pkr: formData.value.item_value_pkr,
    company_charges: formData.value.company_charges,
    received_amount: formData.value.received_amount,
    local_courier_id: formData.value.local_courier_id,
    delivery_charges: formData.value.delivery_charges,
  }

  const requiredFields = ['user_id', 'weight', 'weight_unit', 'item_value_pkr', 'company_charges', 'bought_by']
  const missing = requiredFields.filter(field => {
    const val = cleanData[field as keyof typeof cleanData]
    return val === undefined || val === null || val === ''
  })

  if (missing.length) {
    alert(`Please fill in the following required fields: ${missing.join(', ')}`)
    return
  }

  saving.value = true

  try {
    let result: any

    if (props.initialData?.id) {
      result = await shipmentStore.update(
        props.initialData.id,
        cleanData,
        newImages.value,
        imagesToDelete.value,
      )
    } else {
      result = await shipmentStore.create(cleanData, newImages.value)
    }

    existingImages.value = Array.isArray(result?.images)
      ? result.images.map((img: any) => ({ id: img.id, image_path: img.image_path }))
      : []

    newImages.value = []
    newImagePreviews.value = []
    imagesToDelete.value = []

    emit('saved')
    emit('close')

  } catch (error: any) {
    const msg = error?.response?.data?.message || error?.message || String(error)
    console.error('Save failed:', msg)
    alert('Save failed: ' + msg)
  } finally {
    saving.value = false
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', closeUserDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeUserDropdown)
  if (userSearchTimeout.value) {
    clearTimeout(userSearchTimeout.value)
  }
})
</script>
