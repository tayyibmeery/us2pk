<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Invoice' : 'Add Invoice'"
    :subtitle="formData.id ? 'Update the invoice details below.' : 'Fill in the details to add a new invoice.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" :loading="saving" @close="close" @save="handleSave">
    <template #fields>
      <div v-if="loadingShipments" class="py-4 text-center">
        <div
          class="inline-block h-6 w-6 animate-spin rounded-full border-4 border-solid border-blue-500 border-r-transparent">
        </div>
        <p class="mt-2 text-sm text-gray-500">Loading shipments...</p>
      </div>
      <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Invoice No (Auto-generated) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Invoice No
          </label>
          <input v-model="formData.invoice_no" type="text" placeholder="Auto-generated" :disabled="true"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/60 dark:placeholder:text-white/30 cursor-not-allowed" />
          <p class="text-xs text-gray-500 mt-1">Auto-generated when creating</p>
        </div>

        <!-- Shipment Selection with Search -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Shipment
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
                    <span class="font-medium text-sm">{{ shipment.pcode }}</span>
                    <span class="text-xs text-gray-500 ml-2">#{{ shipment.id }}</span>
                  </div>
                  <span class="text-xs text-gray-400">{{ shipment.description || 'No description' }}</span>
                </div>
                <div class="text-xs text-gray-400 mt-0.5">
                  Customer: {{ shipment.user?.name || 'N/A' }}
                </div>
              </div>
            </div>
            <div v-if="showShipmentDropdown && filteredShipments.length === 0 && shipmentSearch"
              class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white p-4 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800">
              No shipments found
            </div>
          </div>
          <p v-if="selectedShipment" class="text-xs text-green-600 mt-1">
            ✓ Selected: {{ selectedShipment.pcode }} - {{ selectedShipment.description || 'No description' }}
          </p>
          <p v-else class="text-xs text-gray-500 mt-1">Please select a shipment</p>
        </div>

        <!-- Date -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Date
          </label>
          <input v-model="formData.date" type="date"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          <p v-if="selectedShipment && selectedShipment.created_at" class="text-xs text-gray-400 mt-1">
            From shipment: {{ formatDate(selectedShipment.created_at) }}
          </p>
        </div>

        <!-- Amount Due (Total Payable from shipment) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Amount Due
          </label>
          <input v-model.number="formData.amount_due" type="number" step="0.01" placeholder="0.00" min="0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          <p v-if="selectedShipment && selectedShipment.total_payable" class="text-xs text-gray-400 mt-1">
            From shipment (Total Payable): {{ formatCurrency(selectedShipment.total_payable) }}
          </p>
        </div>

        <!-- COD (Receivable COD from shipment) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            COD
          </label>
          <input v-model.number="formData.cod" type="number" step="0.01" placeholder="0.00" min="0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          <p v-if="selectedShipment && selectedShipment.receivable_cod" class="text-xs text-gray-400 mt-1">
            From shipment (Receivable COD): {{ formatCurrency(selectedShipment.receivable_cod) }}
          </p>
        </div>

        <!-- COD Date -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            COD Date
          </label>
          <input v-model="formData.cod_date" type="date" :min="formData.date"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          <p class="text-xs text-gray-500 mt-1">Must be after invoice date</p>
        </div>

        <!-- Output Tax -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Output Tax
          </label>
          <input v-model.number="formData.output_tax" type="number" step="0.01" placeholder="0.00" min="0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          <p v-if="selectedShipment && selectedShipment.tax" class="text-xs text-gray-400 mt-1">
            From shipment: {{ formatCurrency(selectedShipment.tax) }}
          </p>
        </div>

        <!-- Shipment Financial Summary -->
        <div v-if="selectedShipment" class="col-span-2 mt-2">
          <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
            <h4 class="text-sm font-medium mb-2 text-green-700 dark:text-green-300">Shipment Financial Summary</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
              <div>
                <span class="text-gray-500">Item Value:</span>
                <span
                  class="font-medium ml-2">{{ selectedShipment.item_value ? formatCurrency(selectedShipment.item_value) : 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Company Charges:</span>
                <span
                  class="font-medium ml-2">{{ selectedShipment.company_charges ? formatCurrency(selectedShipment.company_charges) : 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Total Payable:</span>
                <span
                  class="font-medium ml-2 text-green-600">{{ selectedShipment.total_payable ? formatCurrency(selectedShipment.total_payable) : 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Received Amount:</span>
                <span
                  class="font-medium ml-2 text-blue-600">{{ selectedShipment.received_amount ? formatCurrency(selectedShipment.received_amount) : 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Receivable COD:</span>
                <span
                  class="font-medium ml-2 text-orange-600">{{ selectedShipment.receivable_cod ? formatCurrency(selectedShipment.receivable_cod) : 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Delivery Charges:</span>
                <span
                  class="font-medium ml-2">{{ selectedShipment.delivery_charges ? formatCurrency(selectedShipment.delivery_charges) : 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Payment Method:</span>
                <span class="font-medium ml-2">{{ selectedShipment.payment_method || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Paid By:</span>
                <span class="font-medium ml-2">{{ selectedShipment.bought_by || 'N/A' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Section -->
      <div v-if="!loadingShipments && (formData.amount_due > 0 || formData.cod > 0 || formData.output_tax > 0)"
        class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <h4 class="text-sm font-medium mb-2">Invoice Summary</h4>
        <div class="grid grid-cols-3 gap-4 text-sm">
          <div>
            <span class="text-gray-500">Amount Due:</span>
            <span class="font-medium ml-2">{{ formatCurrency(formData.amount_due || 0) }}</span>
          </div>
          <div>
            <span class="text-gray-500">COD:</span>
            <span class="font-medium ml-2">{{ formatCurrency(formData.cod || 0) }}</span>
          </div>
          <div>
            <span class="text-gray-500">Tax:</span>
            <span class="font-medium ml-2">{{ formatCurrency(formData.output_tax || 0) }}</span>
          </div>
          <div class="col-span-3 border-t pt-2">
            <span class="text-gray-500">Total:</span>
            <span
              class="font-bold ml-2">{{ formatCurrency((formData.amount_due || 0) + (formData.output_tax || 0)) }}</span>
          </div>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { Invoice } from '@/stores/invoiceStore'
import { useShipmentStore } from '@/stores/shipmentStore'

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

const defaultFormData = (): Partial<Invoice> => ({
  invoice_no: '',
  shipment_id: null,
  date: new Date().toISOString().split('T')[0],
  amount_due: 0,
  cod: 0,
  cod_date: null,
  output_tax: 0,
})

const formData = ref<Partial<Invoice>>(defaultFormData())

// Filtered shipments based on search
const filteredShipments = computed(() => {
  if (!shipmentSearch.value.trim()) {
    return shipments.value.slice(0, 50)
  }
  const search = shipmentSearch.value.toLowerCase()
  return shipments.value.filter((s: any) => {
    return s.pcode?.toLowerCase().includes(search) ||
      s.id?.toString().includes(search) ||
      s.description?.toLowerCase().includes(search) ||
      s.user?.name?.toLowerCase().includes(search)
  }).slice(0, 50)
})

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

// Generate a unique invoice number
const generateInvoiceNumber = (): string => {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  const prefix = `INV-${year}${month}${day}-`

  const existingCount = shipments.value.filter((s: any) => s.invoice_no?.startsWith(prefix)).length || 0
  const nextNumber = existingCount + 1

  return `${prefix}${String(nextNumber).padStart(4, '0')}`
}

const filterShipments = () => {
  showShipmentDropdown.value = true
}

const selectShipment = (shipment: any) => {
  selectedShipment.value = shipment
  formData.value.shipment_id = shipment.id

  // Auto-fill ALL form fields from shipment data
  // Date - from shipment created_at or purchase_date
  if (shipment.created_at) {
    const date = new Date(shipment.created_at)
    formData.value.date = date.toISOString().split('T')[0]
  } else if (shipment.purchase_date) {
    const date = new Date(shipment.purchase_date)
    formData.value.date = date.toISOString().split('T')[0]
  }

  // Amount Due - from total_payable
  if (shipment.total_payable) {
    formData.value.amount_due = shipment.total_payable
  }

  // COD - from receivable_cod
  if (shipment.receivable_cod) {
    formData.value.cod = shipment.receivable_cod
  }

  // COD Date - set to 7 days after shipment date if not set
  if (formData.value.date) {
    const codDate = new Date(formData.value.date)
    codDate.setDate(codDate.getDate() + 7)
    formData.value.cod_date = codDate.toISOString().split('T')[0]
  }

  // Output Tax - calculate as percentage of amount_due (e.g., 16% GST)
  if (formData.value.amount_due && formData.value.amount_due > 0) {
    // If tax is stored in shipment, use it, otherwise calculate
    if (shipment.tax) {
      formData.value.output_tax = shipment.tax
    } else {
      // Example: 16% tax
      formData.value.output_tax = formData.value.amount_due * 0.16
    }
  }

  // Update search input display
  shipmentSearch.value = `${shipment.pcode} - ${shipment.description || 'No description'}`
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

    if (!props.initialData && props.isOpen) {
      formData.value.invoice_no = generateInvoiceNumber()
    }
  } catch (error) {
    console.error('Failed to fetch shipments:', error)
    shipments.value = []
  } finally {
    loadingShipments.value = false
  }
}

watch(
  () => props.isOpen,
  async (open) => {
    if (open) {
      await fetchShipments()
      if (props.initialData) {
        formData.value = { ...props.initialData }
        if (props.initialData.shipment_id) {
          const shipment = shipments.value.find((s: any) => s.id === props.initialData.shipment_id)
          if (shipment) {
            selectedShipment.value = shipment
            shipmentSearch.value = `${shipment.pcode} - ${shipment.description || 'No description'}`
          }
        }
      } else {
        formData.value = defaultFormData()
        formData.value.invoice_no = generateInvoiceNumber()
        selectedShipment.value = null
        shipmentSearch.value = ''
      }
    }
  },
  { immediate: true }
)

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal && props.isOpen) {
      formData.value = { ...newVal }
      if (newVal.shipment_id) {
        const shipment = shipments.value.find((s: any) => s.id === newVal.shipment_id)
        if (shipment) {
          selectedShipment.value = shipment
          shipmentSearch.value = `${shipment.pcode} - ${shipment.description || 'No description'}`
        }
      }
    }
  }
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

  if (!formData.value.invoice_no) {
    formData.value.invoice_no = generateInvoiceNumber()
  }

  saving.value = true
  emit('save', formData.value)
}
</script>
