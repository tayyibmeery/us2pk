<template>
  <div class="space-y-6" v-if="!error">
    <PageBreadcrumb :pageTitle="pageTitle" />
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <form @submit.prevent="submitForm">
        <!-- Two columns -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <!-- Left: Details -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold">Consolidation Details</h3>
            <div>
              <label class="block text-sm font-medium">Warehouse</label>
              <select v-model="form.warehouse_id" class="w-full rounded-lg border p-2 dark:bg-gray-800">
                <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium">International Courier</label>
              <select v-model="form.international_courier_id" class="w-full rounded-lg border p-2 dark:bg-gray-800">
                <option v-for="c in couriers" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium">AWB Number</label>
              <input v-model="form.awb_number" class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>



            <!-- Date Departed -->
            <div>
              <label class="block text-sm font-medium">Date Departed</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.date_departed = el }" v-model="form.date_departed"
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

            <!-- Date Reached -->
            <div>
              <label class="block text-sm font-medium">Date Reached</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.date_reached = el }" v-model="form.date_reached" type="text"
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
          </div>

          <!-- Right: Costs (unchanged) -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold">Costs</h3>
            <div>
              <label class="block text-sm font-medium">Warehouse Charges</label>
              <input v-model.number="form.ware_house_charges" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>
            <div>
              <label class="block text-sm font-medium">Customs Duty</label>
              <input v-model.number="form.customs_duty" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>
            <div>
              <label class="block text-sm font-medium">Sales Tax</label>
              <input v-model.number="form.sales_tax" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>
            <div>
              <label class="block text-sm font-medium">Income Tax</label>
              <input v-model.number="form.income_tax" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>
            <div>
              <label class="block text-sm font-medium">CAA Charges</label>
              <input v-model.number="form.caa_charges" type="number" step="0.01"
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
            </div>
          </div>
        </div>

        <!-- Shipments (unchanged) -->
        <div class="mt-6">
          <h3 class="text-lg font-semibold">Shipments</h3>
          <div class="flex items-center gap-4 mt-2">
            <div class="relative flex-1">
              <input v-model="shipmentCodeQuery" @input="searchShipments" placeholder="Type shipment code to add..."
                class="w-full rounded-lg border p-2 dark:bg-gray-800" />
              <div v-if="showSuggestions && suggestions.length"
                class="absolute z-10 w-full bg-white border rounded-lg shadow-lg dark:bg-gray-800">
                <div v-for="s in suggestions" :key="s.id" @click="addShipment(s)"
                  class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                  {{ s.shipment_code }}
                </div>
              </div>
            </div>
            <span class="text-sm text-gray-500">Total KGs: {{ totalKg.toFixed(2) }}</span>
          </div>

          <!-- Shipment table -->
          <div class="overflow-x-auto mt-4">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="px-3 py-2 text-left">Ship-Code</th>
                  <th class="px-3 py-2 text-left">Customer</th>
                  <th class="px-3 py-2 text-left">City</th>
                  <th class="px-3 py-2 text-right">KGs</th>
                  <th class="px-3 py-2 text-right">US2PK Charges</th>
                  <th class="px-3 py-2 text-right">Amount Due</th>
                  <!-- <th class="px-3 py-2 text-right">COD</th> -->
                  <th class="px-3 py-2 text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(s, index) in selectedShipments" :key="s.id" class="border-b">
                  <td class="px-3 py-2 font-mono">{{ s.shipment_code ?? 'N/A' }}</td>
                  <td class="px-3 py-2">{{ s.user?.name ?? 'Unknown' }}</td>
                  <td class="px-3 py-2">{{ s.user?.city?.city_name ?? 'N/A' }}</td>
                  <td class="px-3 py-2 text-right">{{ (s.weight_kgs ?? 0).toFixed(2) }}</td>
                  <td class="px-3 py-2 text-right">{{ (s.company_charges ?? 0).toFixed(2) }}</td>
                  <td class="px-3 py-2 text-right">{{ (s.amount_due ?? 0).toFixed(2) }}</td>
                  <!-- <td class="px-3 py-2 text-right">{{ (s.receivable_cod ?? 0).toFixed(2) }}</td> -->
                  <td class="px-3 py-2 text-center">
                    <button type="button" @click="removeShipment(index)"
                      class="text-red-500 hover:underline">Remove</button>
                  </td>
                </tr>
                <tr v-if="!selectedShipments.length">
                  <td colspan="8" class="py-4 text-center text-gray-400">No shipments added yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Submit -->
        <div class="mt-6 flex justify-end gap-4">
          <router-link to="/admin/consolidations"
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
import { useWarehouseStore } from '@/stores/warehouseStore'
import { useInternationalCourierStore } from '@/stores/internationalCourierStore'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const router = useRouter()
const route = useRoute()
const isEdit = computed(() => !!route.params.id)
const pageTitle = ref(isEdit.value ? 'Edit Consolidation' : 'New Consolidation')

// ── Pinia Stores ────────────────────────────────────────────
const warehouseStore = useWarehouseStore()
const courierStore = useInternationalCourierStore()

const warehouses = computed(() => warehouseStore.items)
const couriers = computed(() => courierStore.items)

// ── Error tracking ──────────────────────────────────────────
const error = ref<string | null>(null)
onErrorCaptured((err) => {
  console.error('❌ ConsolidationForm error:', err)
  error.value = err.message || 'Unknown error'
  return false
})

// ── Component state ─────────────────────────────────────────
const loading = ref(false)
const submitting = ref(false)
const shipmentCodeQuery = ref('')
const suggestions = ref<any[]>([])
const showSuggestions = ref(false)

const form = reactive({
  awb_number: '',
  warehouse_id: null,

  international_courier_id: null,
  date_departed: '',
  date_reached: '',
  ware_house_charges: 0,
  customs_duty: 0,
  sales_tax: 0,
  income_tax: 0,
  caa_charges: 0,
  shipment_ids: [] as number[],
})

const selectedShipments = ref<any[]>([])

// ── Date picker refs ──────────────────────────────────────
const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

// ── Total KGs ────────────────────────────────────────────────
const totalKg = computed(() => {
  return selectedShipments.value.reduce((sum, s) => {
    const kg = Number(s.weight_kgs) || 0
    return sum + kg
  }, 0)
})

// ── Helper to coerce numeric fields ──────────────────────
function coerceNumericShipment(shipment: any): any {
  const numericFields = ['weight_kgs', 'company_charges', 'amount_due', 'receivable_cod']
  numericFields.forEach(field => {
    if (shipment[field] !== undefined && shipment[field] !== null) {
      shipment[field] = Number(shipment[field]) || 0
    } else {
      shipment[field] = 0
    }
  })
  return shipment
}

// ── Shipment search & add ──────────────────────────────────
async function searchShipments() {
  if (shipmentCodeQuery.value.length < 2) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }
  try {
    const res = await api.get('/admin/consolidations/shipmentsJson', {
      params: { q: shipmentCodeQuery.value }
    })
    const selectedIds = selectedShipments.value.map(s => s.id)
    suggestions.value = res.data.filter((s: any) => !selectedIds.includes(s.id))
    showSuggestions.value = true
  } catch (err) {
    console.error('Search failed:', err)
  }
}

function addShipment(s: any) {
  api.get('/admin/consolidations/shipment', {
    params: { shipment_tracker_id: s.shipment_code }
  })
    .then(res => {
      const shipment = coerceNumericShipment(res.data)
      selectedShipments.value.push(shipment)
      shipmentCodeQuery.value = ''
      suggestions.value = []
      showSuggestions.value = false
      form.shipment_ids = selectedShipments.value.map(s => s.id)
    })
    .catch(err => alert('Failed to fetch shipment details: ' + err.message))
}

function removeShipment(index: number) {
  selectedShipments.value.splice(index, 1)
  form.shipment_ids = selectedShipments.value.map(s => s.id)
}

// ── Load existing consolidation (edit mode) ──────────────
async function loadConsolidation() {
  if (!isEdit.value) return
  loading.value = true
  try {
    const res = await api.get(`/admin/consolidations/${route.params.id}`)
    const data = res.data
    Object.assign(form, {
      awb_number: data.awb_number,
      warehouse_id: data.warehouse_id,

      international_courier_id: data.international_courier_id,
      date_departed: data.date_departed || '',
      date_reached: data.date_reached || '',
      ware_house_charges: Number(data.ware_house_charges) || 0,
      customs_duty: Number(data.customs_duty) || 0,
      sales_tax: Number(data.sales_tax) || 0,
      income_tax: Number(data.income_tax) || 0,
      caa_charges: Number(data.caa_charges) || 0,
      shipment_ids: data.shipments?.map((s: any) => s.id) || [],
    })
    selectedShipments.value = (data.shipments || []).map((s: any) =>
      coerceNumericShipment({ ...s })
    )
    // Update date pickers after data loads
    await nextTick()
    updateDatePickers()
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

// ── Submit form ─────────────────────────────────────────────
async function submitForm() {
  if (!selectedShipments.value.length) {
    alert('Please add at least one shipment.')
    return
  }
  submitting.value = true
  try {
    const payload = {
      ...form,
      shipment_ids: selectedShipments.value.map(s => s.id)
    }
    if (isEdit.value) {
      await api.put(`/admin/consolidations/${route.params.id}`, payload)
    } else {
      await api.post('/admin/consolidations', payload)
    }
    router.push('/admin/consolidations')
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to save consolidation')
  } finally {
    submitting.value = false
  }
}

// ── Date picker initialization ──────────────────────────────
function initDatePickers() {
  const dateFields = [ 'date_departed', 'date_reached']
  dateFields.forEach(field => {
    const el = datePickerRefs.value[field]
    if (el) {
      if (flatpickrInstances[field]) {
        flatpickrInstances[field].destroy()
      }
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          (form as any)[field] = dateStr
        },
      })
      // Set initial value if form has it
      const val = (form as any)[field]
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
        flatpickrInstances[field].setDate(clean)
      }
    }
  })
}

function updateDatePickers() {
  const dateFields = [ 'date_departed', 'date_reached']
  dateFields.forEach(field => {
    const inst = flatpickrInstances[field]
    const val = (form as any)[field]
    if (inst && val) {
      const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
      inst.setDate(clean)
    }
  })
}

// ── Lifecycle ──────────────────────────────────────────────
onMounted(async () => {
  try {
    await Promise.all([
      warehouseStore.fetchItems(1, { per_page: 1000 }),
      courierStore.fetchItems(1, { per_page: 1000 })
    ])
  } catch (err) {
    console.error('Failed to load dropdown data:', err)
    error.value = 'Could not load required data.'
  }
  if (isEdit.value) {
    await loadConsolidation()
  }
  // Initialize date pickers after DOM is ready
  await nextTick()
  initDatePickers()
})

onBeforeUnmount(() => {
  Object.values(flatpickrInstances).forEach(inst => {
    if (inst) inst.destroy()
  })
})

// Watch for route changes (if editing different ID)
watch(() => route.params.id, async (newId) => {
  if (newId) {
    await loadConsolidation()
    await nextTick()
    updateDatePickers()
  }
})
</script>
