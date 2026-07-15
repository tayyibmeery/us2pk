<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Invoice' : 'Create Invoice'"
    :subtitle="formData.id ? 'Update the invoice details below.' : 'Fill in the details to create a new invoice.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" :loading="saving" @close="close" @save="handleSave">
    <template #fields>
      <div v-if="loadingShipments" class="py-8 text-center">
        <div
          class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-brand-500 border-r-transparent">
        </div>
        <p class="mt-3 text-sm text-gray-500">Loading shipments...</p>
      </div>
      <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Invoice No -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Invoice No
          </label>
          <input v-model="formData.invoice_no" type="text" placeholder="Auto-generated" :disabled="true"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/60 dark:placeholder:text-white/30 cursor-not-allowed" />
          <p class="text-xs text-gray-500 mt-1">Auto-generated when creating</p>
        </div>

        <!-- Date with Flatpickr -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Invoice Date <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.invoice_date = el }" v-model="formData.date" type="text"
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

        <!-- Shipment Selection -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Shipment <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <input v-model="shipmentSearch" @input="filterShipments" @focus="showShipmentDropdown = true"
              @blur="closeDropdownWithDelay" type="text" placeholder="Search by code or description..."
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            <div v-if="showShipmentDropdown && filteredShipments.length > 0"
              class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
              <div v-for="shipment in filteredShipments" :key="shipment.id"
                @mousedown.prevent="selectShipment(shipment)"
                class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex items-center justify-between">
                  <div>
                    <span class="font-medium text-sm">{{ shipment.shipment_code }}</span>
                    <span class="text-xs text-gray-500 ml-2">#{{ shipment.id }}</span>
                  </div>
                  <span class="text-xs text-gray-400">{{ shipment.description || 'No description' }}</span>
                </div>
                <div class="text-xs text-gray-400 mt-0.5">
                  Customer: {{ shipment.user?.name || 'N/A' }} | COD: {{ formatCurrency(shipment.receivable_cod || 0) }}
                </div>
              </div>
            </div>
            <div v-if="showShipmentDropdown && filteredShipments.length === 0 && shipmentSearch"
              class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-4 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">
              No shipments found
            </div>
          </div>
          <p v-if="selectedShipment" class="text-xs text-green-600 mt-1">
            ✓ Selected: {{ selectedShipment.shipment_code }} - {{ selectedShipment.description || 'No description' }}
          </p>
        </div>

        <!-- Amount Due = Receivable COD from shipment -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Amount Due <span class="text-red-500">*</span>
          </label>
          <input v-model.number="formData.amount_due" type="number" step="0.01" placeholder="0.00" min="0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required />
          <p v-if="selectedShipment" class="text-xs text-gray-400 mt-1">
            Receivable COD: {{ formatCurrency(selectedShipment.receivable_cod || 0) }}
          </p>
        </div>

        <!-- COD = Amount Due (same as receivable cod) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            COD
          </label>
          <input v-model.number="formData.cod" type="number" step="0.01" placeholder="0.00" min="0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          <p class="text-xs text-gray-400 mt-1">Same as Amount Due</p>
        </div>

        <!-- COD Date -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            COD Date
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.cod_date = el }" v-model="formData.cod_date" type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Select COD date" readonly />
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

        <!-- Output Tax - REMOVED (no extra charges) -->
        <input type="hidden" v-model="formData.output_tax" />

        <!-- Status -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Payment Method -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Payment Method
          </label>
          <select v-model="formData.payment_method"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Select</option>
            <option value="Cash">Cash</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Credit Card">Credit Card</option>
            <option value="JazzCash">JazzCash</option>
            <option value="EasyPaisa">EasyPaisa</option>
          </select>
        </div>

        <!-- Notes -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Notes
          </label>
          <textarea v-model="formData.notes" rows="2" placeholder="Additional notes..."
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Summary -->
        <div v-if="formData.amount_due > 0"
          class="sm:col-span-2 mt-2 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
          <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Summary</h4>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
            <div>
              <span class="text-gray-500">Amount Due:</span>
              <span
                class="font-medium ml-2 text-gray-800 dark:text-white/90">{{ formatCurrency(formData.amount_due || 0) }}</span>
            </div>
            <div>
              <span class="text-gray-500">COD:</span>
              <span
                class="font-medium ml-2 text-blue-600 dark:text-blue-400">{{ formatCurrency(formData.cod || 0) }}</span>
            </div>
            <div>
              <span class="text-gray-500 font-semibold">Total:</span>
              <span
                class="font-bold ml-2 text-emerald-600 dark:text-emerald-400">{{ formatCurrency(formData.amount_due || 0) }}</span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { Invoice } from '@/stores/invoiceStore'
import { useShipmentStore } from '@/stores/shipmentStore'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const props = defineProps<{
  isOpen: boolean
  initialData?: Invoice | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<Invoice>): void
}>()

const shipmentStore = useShipmentStore()
const shipments = ref<any[]>([])
const loadingShipments = ref(false)
const saving = ref(false)
const shipmentSearch = ref('')
const showShipmentDropdown = ref(false)
const selectedShipment = ref<any>(null)

// Date picker refs
const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value || 0)
}

const defaultFormData = (): Partial<Invoice> => ({
  invoice_no: '',
  shipment_id: null,
  date: new Date().toISOString().split('T')[0],
  amount_due: 0,
  cod: 0,
  cod_date: null,
  output_tax: 0,
  status: 'pending',
  payment_method: null,
  notes: null,
})

const formData = ref<Partial<Invoice>>(defaultFormData())

// Initialize date pickers
const initDatePickers = () => {
  const dateFields = ['invoice_date', 'cod_date']
  dateFields.forEach(field => {
    const el = datePickerRefs.value[field]
    if (el) {
      if (flatpickrInstances[field]) flatpickrInstances[field].destroy()
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          if (field === 'invoice_date') {
            formData.value.date = dateStr
          } else if (field === 'cod_date') {
            formData.value.cod_date = dateStr
          }
        },
      })
      const val = field === 'invoice_date' ? formData.value.date : formData.value.cod_date
      if (val) {
        flatpickrInstances[field].setDate(val)
      }
    }
  })
}

// Filtered shipments
const filteredShipments = computed(() => {
  if (!shipmentSearch.value.trim()) {
    return shipments.value.slice(0, 50)
  }
  const search = shipmentSearch.value.toLowerCase()
  return shipments.value.filter((s: any) => {
    return s.shipment_code?.toLowerCase().includes(search) ||
      s.id?.toString().includes(search) ||
      s.description?.toLowerCase().includes(search) ||
      s.user?.name?.toLowerCase().includes(search)
  }).slice(0, 50)
})

const filterShipments = () => {
  showShipmentDropdown.value = true
}

const selectShipment = (shipment: any) => {
  selectedShipment.value = shipment
  formData.value.shipment_id = shipment.id

  // ✅ Amount Due = Receivable COD (only this, no extra charges)
  if (shipment.receivable_cod) {
    formData.value.amount_due = shipment.receivable_cod
    formData.value.cod = shipment.receivable_cod // COD same as amount due
  }

  // ✅ Output Tax = 0 (no extra charges)
  formData.value.output_tax = 0

  // Set date from shipment
  if (shipment.created_at) {
    const date = new Date(shipment.created_at)
    formData.value.date = date.toISOString().split('T')[0]
  }

  // Set COD date (7 days after invoice date)
  if (formData.value.date) {
    const codDate = new Date(formData.value.date)
    codDate.setDate(codDate.getDate() + 7)
    formData.value.cod_date = codDate.toISOString().split('T')[0]
  }

  shipmentSearch.value = `${shipment.shipment_code} - ${shipment.description || 'No description'}`
  showShipmentDropdown.value = false
}

const closeDropdownWithDelay = () => {
  setTimeout(() => {
    showShipmentDropdown.value = false
  }, 200)
}

const fetchShipments = async () => {
  loadingShipments.value = true
  try {
    await shipmentStore.fetchItems(1, { per_page: 1000 })
    shipments.value = shipmentStore.items
  } catch (error) {
    console.error('Failed to fetch shipments:', error)
  } finally {
    loadingShipments.value = false
  }
}

watch(
  () => props.isOpen,
  async (open) => {
    if (open) {
      await fetchShipments()
      await nextTick()
      initDatePickers()

      if (props.initialData) {
        formData.value = { ...props.initialData }
        if (props.initialData.shipment_id) {
          const shipment = shipments.value.find((s: any) => s.id === props.initialData.shipment_id)
          if (shipment) {
            selectedShipment.value = shipment
            shipmentSearch.value = `${shipment.shipment_code} - ${shipment.description || 'No description'}`
          }
        }
      } else {
        formData.value = defaultFormData()
        selectedShipment.value = null
        shipmentSearch.value = ''
        // Set default date
        const today = new Date()
        formData.value.date = today.toISOString().split('T')[0]
      }
    }
  },
  { immediate: true }
)

const close = () => {
  saving.value = false
  formData.value = defaultFormData()
  selectedShipment.value = null
  shipmentSearch.value = ''
  showShipmentDropdown.value = false
  emit('close')
}

const handleSave = () => {
  if (!formData.value.shipment_id) {
    alert('Please select a shipment')
    return
  }
  if (!formData.value.date) {
    alert('Please select a date')
    return
  }
  if (!formData.value.amount_due || formData.value.amount_due <= 0) {
    alert('Please enter a valid amount due')
    return
  }

  // Ensure COD = Amount Due
  formData.value.cod = formData.value.amount_due
  formData.value.output_tax = 0

  saving.value = true
  emit('save', formData.value)
}

onBeforeUnmount(() => {
  Object.values(flatpickrInstances).forEach(inst => {
    if (inst) inst.destroy()
  })
})
</script>
