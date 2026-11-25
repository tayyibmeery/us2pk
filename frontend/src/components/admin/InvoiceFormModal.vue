<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Invoice' : 'Create Invoice'"
    :subtitle="formData.id ? 'Update invoice details.' : 'Select a customer and their COD shipments to invoice.'"
    :saveLabel="formData.id ? 'Update Invoice' : 'Create Invoice'" :loading="saving" @close="close" @save="handleSave">
    <template #fields>
      <div v-if="loadingInitial" class="py-12 flex flex-col items-center">
        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-brand-500 border-r-transparent">
        </div>
        <p class="mt-3 text-sm text-gray-500">Loading customers...</p>
      </div>

      <div v-else class="space-y-6">

        <!-- ── 1. CUSTOMER ─────────────────────────────── -->
        <div v-if="!formData.id">
          <h4
            class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
            1. Select Customer
          </h4>

          <div class="relative">
            <input v-model="userSearch" @focus="showUserDropdown = true" @blur="closeUserDropdown" type="text"
              placeholder="Search by name or email..."
              class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800"
              :disabled="!!selectedUser" />

            <button v-if="selectedUser" @click="clearUser"
              class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-red-600 hover:underline">
              Change
            </button>

            <div v-if="showUserDropdown && !selectedUser && filteredUsers.length"
              class="absolute z-50 mt-1 w-full max-h-72 overflow-y-auto rounded-lg border bg-white shadow-xl dark:bg-gray-800 dark:border-gray-700">
              <div v-for="u in filteredUsers" :key="u.id" @mousedown.prevent="selectUser(u)"
                class="cursor-pointer px-4 py-3 border-b last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700">
                <div class="flex justify-between items-start">
                  <div>
                    <div class="font-medium text-sm text-gray-900 dark:text-white">{{ u.name }}</div>
                    <div class="text-xs text-gray-500">{{ u.email }}</div>
                  </div>
                  <div class="text-right">
                    <div class="text-xs font-semibold text-emerald-600">
                      {{ u.unbilled_cod_count }} unbilled
                    </div>
                    <div class="text-xs text-gray-500 font-mono">
                      {{ formatCurrency(u.total_pending_cod) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <p v-if="selectedUser" class="text-xs text-emerald-600 mt-2 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Customer: {{ selectedUser.name }} ({{ selectedUser.email }})
          </p>
        </div>

        <!-- ── 2. SHIPMENTS ─────────────────────────────── -->
        <div v-if="selectedUser || formData.id">
          <h4
            class="text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 flex items-center justify-between">
            <span>2. Select Shipments to Invoice</span>
            <span v-if="!formData.id" class="text-xs font-normal text-gray-500">
              {{ selectedShipmentIds.length }} selected
            </span>
          </h4>

          <div v-if="loadingShipments" class="py-8 text-center text-sm text-gray-500">
            Loading shipments...
          </div>

          <div v-else-if="!shipments.length" class="py-6 text-center text-sm text-gray-500">
            No COD shipments found for this customer.
          </div>

          <div v-else class="space-y-2 max-h-72 overflow-y-auto pr-1">
            <label v-for="s in shipments" :key="s.id" :class="[
              'flex items-start gap-3 rounded-lg border p-3 cursor-pointer transition',
              s.already_invoiced
                ? 'bg-gray-100 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 cursor-not-allowed opacity-60'
                : selectedShipmentIds.includes(s.id)
                  ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20'
                  : 'border-gray-200 dark:border-gray-700 hover:border-brand-300'
            ]">
              <input type="checkbox" :value="s.id" :checked="selectedShipmentIds.includes(s.id)"
                :disabled="s.already_invoiced" @change="toggleShipment(s.id)" class="mt-1 accent-brand-600" />
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-medium text-sm text-gray-900 dark:text-white truncate">
                    {{ s.shipment_code }}
                  </div>
                  <div class="text-sm font-mono font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                    {{ formatCurrency(s.receivable_cod) }}
                  </div>
                </div>
                <div class="text-xs text-gray-500 truncate">
                  {{ s.description || 'No description' }}
                  <span v-if="s.weight"> · {{ s.weight }} KG</span>
                </div>
                <div v-if="s.already_invoiced" class="text-xs text-amber-600 mt-1">
                  Already invoiced → {{ s.invoice_no }}
                </div>
              </div>
            </label>
          </div>
        </div>

        <!-- ── 3. FINANCIALS ─────────────────────────────── -->
        <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
          <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">3. Invoice Details</h4>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Invoice Date <span class="text-red-500">*</span>
              </label>
              <input :ref="el => { if (el) datePickerRefs.invoice_date = el }" v-model="formData.date" type="text"
                readonly placeholder="Select date"
                class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm cursor-pointer focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
              <select v-model="formData.status"
                class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Output Tax (PKR)</label>
              <input v-model.number="formData.output_tax" type="number" step="0.01" min="0"
                class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm font-mono dark:border-gray-700 dark:bg-gray-800" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Total (auto)</label>
              <input :value="formatCurrency(computedTotal)" readonly
                class="w-full h-11 rounded-lg border border-gray-200 bg-gray-100 px-4 text-sm font-mono font-bold text-emerald-700 dark:bg-gray-800/50 dark:border-gray-700" />
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between text-sm">
            <span class="text-gray-500">Subtotal (COD)</span>
            <span class="font-mono">{{ formatCurrency(computedSubtotal) }}</span>
          </div>
          <div class="flex justify-between text-sm mt-1">
            <span class="text-gray-500">Output Tax</span>
            <span class="font-mono">{{ formatCurrency(formData.output_tax || 0) }}</span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes (Optional)</label>
          <textarea v-model="formData.notes" rows="2"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800"
            placeholder="Notes to appear on the invoice..."></textarea>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { Invoice } from '@/stores/invoiceStore'
import { useInvoiceStore } from '@/stores/invoiceStore'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const props = defineProps<{ isOpen: boolean; initialData?: Invoice | null }>()
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<Invoice> & { shipment_ids?: number[] }): void
}>()

const invoiceStore = useInvoiceStore()

const loadingInitial = ref(false)
const loadingShipments = ref(false)
const saving = ref(false)

const users = ref<any[]>([])
const userSearch = ref('')
const showUserDropdown = ref(false)
const selectedUser = ref<any>(null)

const shipments = ref<any[]>([])
const selectedShipmentIds = ref<number[]>([])

const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

const formData = ref<Partial<Invoice>>({})

const formatCurrency = (v: number) =>
  new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(v || 0)

const filteredUsers = computed(() => {
  if (!userSearch.value.trim()) return users.value.slice(0, 50)
  const s = userSearch.value.toLowerCase()
  return users.value
    .filter(
      (u) =>
        u.name?.toLowerCase().includes(s) ||
        u.email?.toLowerCase().includes(s)
    )
    .slice(0, 50)
})

const computedSubtotal = computed(() =>
  shipments.value
    .filter((s) => selectedShipmentIds.value.includes(s.id))
    .reduce((sum, s) => sum + (Number(s.receivable_cod) || 0), 0)
)

const computedTotal = computed(
  () => computedSubtotal.value + (Number(formData.value.output_tax) || 0)
)

const defaultFormData = (): Partial<Invoice> => ({
  invoice_no: '',
  user_id: null,
  date: new Date().toISOString().split('T')[0],
  amount_due: 0,
  cod: 0,
  output_tax: 0,
  status: 'pending',
  notes: null,
})

const initDatePicker = () => {
  const el = datePickerRefs.value.invoice_date
  if (!el) return
  if (flatpickrInstances.invoice) flatpickrInstances.invoice.destroy()
  flatpickrInstances.invoice = flatpickr(el, {
    dateFormat: 'Y-m-d',
    allowInput: true,
    onChange: (_d: any, str: string) => {
      formData.value.date = str
    },
  })
  if (formData.value.date) flatpickrInstances.invoice.setDate(formData.value.date)
}

const selectUser = async (user: any) => {
  selectedUser.value = user
  formData.value.user_id = user.id
  userSearch.value = `${user.name} (${user.email})`
  showUserDropdown.value = false
  await loadUserShipments(user.id)
}

const clearUser = () => {
  selectedUser.value = null
  formData.value.user_id = null
  userSearch.value = ''
  shipments.value = []
  selectedShipmentIds.value = []
  formData.value.amount_due = 0
  formData.value.cod = 0
}

const closeUserDropdown = () =>
  setTimeout(() => {
    showUserDropdown.value = false
  }, 200)

const toggleShipment = (id: number) => {
  const i = selectedShipmentIds.value.indexOf(id)
  if (i === -1) selectedShipmentIds.value.push(id)
  else selectedShipmentIds.value.splice(i, 1)
  syncAmounts()
}

const syncAmounts = () => {
  const subtotal = shipments.value
    .filter((s) => selectedShipmentIds.value.includes(s.id))
    .reduce((sum, s) => sum + (Number(s.receivable_cod) || 0), 0)
  formData.value.amount_due = subtotal
  formData.value.cod = subtotal
}

const loadUserShipments = async (userId: number) => {
  loadingShipments.value = true
  try {
    shipments.value = await invoiceStore.fetchUserCodShipments(userId)
    // auto-select all unbilled
    selectedShipmentIds.value = shipments.value
      .filter((s) => !s.already_invoiced)
      .map((s) => s.id)
    syncAmounts()
  } finally {
    loadingShipments.value = false
  }
}

watch(
  () => props.isOpen,
  async (open) => {
    if (!open) return
    loadingInitial.value = true
    formData.value = defaultFormData()
    selectedUser.value = null
    userSearch.value = ''
    shipments.value = []
    selectedShipmentIds.value = []

    try {
      users.value = await invoiceStore.fetchUsersWithCod()
      await nextTick()
      initDatePicker()

      if (props.initialData) {
        // Edit mode — only metadata, shipments are locked
        formData.value = { ...props.initialData }
        selectedUser.value = props.initialData.user ?? null
        if (selectedUser.value) {
          userSearch.value = `${selectedUser.value.name} (${selectedUser.value.email})`
        }
      }
    } finally {
      loadingInitial.value = false
    }
  },
  { immediate: true }
)

const close = () => {
  saving.value = false
  emit('close')
}

const handleSave = () => {
  if (saving.value) return

  if (!formData.value.id) {
    if (!formData.value.user_id) return alert('Please select a customer')
    if (!selectedShipmentIds.value.length) return alert('Please select at least one shipment')
  }
  if (!formData.value.date) return alert('Please select a date')

  saving.value = true

  if (formData.value.id) {
    // Edit: just metadata
    emit('save', { ...formData.value })
  } else {
    emit('save', {
      ...formData.value,
      amount_due: computedSubtotal.value,
      cod: computedSubtotal.value,
      shipment_ids: [...selectedShipmentIds.value],
    })
  }
}

onBeforeUnmount(() => {
  Object.values(flatpickrInstances).forEach((i: any) => i?.destroy())
})
</script>
