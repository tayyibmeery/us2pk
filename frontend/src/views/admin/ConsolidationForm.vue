<template>
  <div class="space-y-6" v-if="!error">
    <PageBreadcrumb :pageTitle="pageTitle" />

    <form @submit.prevent="submitForm" class="space-y-6">
      <!-- Page heading -->
      <div class="flex items-center gap-4">
        <div
          class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{ isEdit ? 'Edit consolidation' : 'New consolidation' }}
          </h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Group shipments into one international consignment and track what it costs.
          </p>
        </div>
      </div>

      <!-- Top row: details + costs -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

        <!-- Shipment details -->
        <section
          class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03] lg:col-span-3">
          <header class="border-b border-gray-100 px-6 py-4 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Shipment details</h3>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Where it ships from, who carries it and when.</p>
          </header>

          <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Warehouse</label>
              <select v-model="form.warehouse_id" class="field">
                <option :value="null">Select a warehouse</option>
                <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">International
                courier</label>
              <select v-model="form.international_courier_id" class="field">
                <option :value="null">Select a courier</option>
                <option v-for="c in couriers" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">AWB number</label>
              <div class="relative">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                  <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                  </svg>
                </span>
                <input v-model="form.awb_number" placeholder="e.g. 4654654" class="field pl-10 font-mono" />
              </div>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date departed</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.date_departed = el }" v-model="form.date_departed"
                  type="text" class="field cursor-pointer pr-10" placeholder="Select date" readonly />
                <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"><svg
                    class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                  </svg></span>
              </div>
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date reached</label>
              <div class="relative">
                <input :ref="el => { if (el) datePickerRefs.date_reached = el }" v-model="form.date_reached" type="text"
                  class="field cursor-pointer pr-10" placeholder="Select date" readonly />
                <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400"><svg
                    class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                  </svg></span>
              </div>
            </div>
          </div>
        </section>

        <!-- Costs -->
        <section
          class="flex flex-col rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03] lg:col-span-2">
          <header class="border-b border-gray-100 px-6 py-4 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Costs and taxes</h3>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Expenses incurred for this consolidation.</p>
          </header>

          <div class="flex-1 space-y-5 p-6">
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Other charges</label>
              <div class="relative">
                <span
                  class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-400">PKR</span>
                <input v-model.number="form.other_charges" type="number" step="0.01" min="0" placeholder="0.00"
                  class="field pl-12 text-right tabular-nums" />
              </div>
              <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Warehouse, sales tax, income tax and CAA.</p>
            </div>

            <div>
              <label class="mb-1.5 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Customs duty
                <span
                  class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:bg-amber-500/15 dark:text-amber-400">Add
                  later</span>
              </label>
              <div class="relative">
                <span
                  class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-400">PKR</span>
                <input v-model.number="form.customs_duty" type="number" step="0.01" min="0" placeholder="0.00"
                  class="field pl-12 text-right tabular-nums" />
              </div>
              <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Update this once the consolidation arrives at
                your office.</p>
            </div>
          </div>

          <div class="flex items-baseline justify-between rounded-b-2xl bg-gray-50 px-6 py-5 dark:bg-gray-800/40">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total cost</span>
            <span class="text-2xl font-semibold tabular-nums text-gray-900 dark:text-white">
              <span class="mr-1 text-sm font-medium text-gray-400">PKR</span>{{ totalCost.toLocaleString() }}
            </span>
          </div>
        </section>
      </div>

      <!-- Shipments: full width -->
      <section class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
        <header
          class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-6 py-4 dark:border-gray-800">
          <div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Shipments</h3>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Search by shipment code to add them to this
              consolidation.</p>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <span
              class="rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
              {{ selectedShipments.length }} shipments
            </span>
            <span
              class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
              {{ totalKg.toFixed(2) }} KG
            </span>
          </div>
        </header>

        <div class="p-6">
          <!-- Search -->
          <div ref="searchBox" class="relative mb-5 max-w-xl">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
              <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
              </svg>
            </span>
            <input v-model="shipmentCodeQuery" @input="onSearchInput" @focus="showSuggestions = suggestions.length > 0"
              placeholder="Type at least 2 characters of a shipment code" class="field pl-10 pr-10"
              autocomplete="off" />
            <span v-if="searching" class="absolute right-3.5 top-1/2 -translate-y-1/2">
              <svg class="h-4 w-4 animate-spin text-brand-500" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
              </svg>
            </span>

            <div v-if="showSuggestions"
              class="absolute z-20 mt-1.5 max-h-64 w-full overflow-auto rounded-xl border border-gray-200 bg-white py-1 shadow-xl dark:border-gray-700 dark:bg-gray-800">
              <button v-for="s in suggestions" :key="s.id" type="button" @click="addShipment(s)"
                class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700/60">
                <span class="font-mono font-medium text-gray-800 dark:text-gray-100">{{ s.shipment_code }}</span>
                <span class="text-xs font-medium text-brand-500">Add</span>
              </button>
              <p v-if="!suggestions.length" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                No matching shipments, or they're already added.
              </p>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full min-w-[820px] text-sm">
              <thead class="bg-gray-50 text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
                <tr>
                  <th class="px-5 py-3.5 text-left font-medium">Shipment code</th>
                  <th class="px-5 py-3.5 text-left font-medium">Customer</th>
                  <th class="px-5 py-3.5 text-left font-medium">City</th>
                  <th class="px-5 py-3.5 text-right font-medium">KGs</th>
                  <th class="px-5 py-3.5 text-right font-medium">US2PK charges</th>
                  <th class="px-5 py-3.5 text-right font-medium">COD</th>
                  <th class="w-16 px-5 py-3.5"><span class="sr-only">Remove</span></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-700/70">
                <tr v-for="(s, index) in selectedShipments" :key="s.id"
                  class="transition-colors hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
                  <td class="px-5 py-3.5 font-mono font-medium text-gray-900 dark:text-gray-100">
                    {{ s.shipment_code ?? 'N/A' }}</td>
                  <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                      <span
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                        {{ initials(s.user?.name) }}
                      </span>
                      <span class="font-medium text-gray-800 dark:text-gray-200">{{ s.user?.name ?? 'Unknown' }}</span>
                    </div>
                  </td>
                  <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ s.user?.city?.city_name ?? 'N/A' }}</td>
                  <td class="px-5 py-3.5 text-right tabular-nums text-gray-800 dark:text-gray-200">
                    {{ (s.weight_kgs ?? 0).toFixed(2) }}</td>
                  <td class="px-5 py-3.5 text-right tabular-nums text-gray-800 dark:text-gray-200">
                    {{ money(s.company_charges) }}</td>
                  <td class="px-5 py-3.5 text-right font-medium tabular-nums text-gray-900 dark:text-white">
                    {{ money(s.receivable_cod) }}</td>
                  <td class="px-5 py-3.5 text-right">
                    <button type="button" @click="removeShipment(index)" title="Remove shipment"
                      aria-label="Remove shipment"
                      class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 dark:hover:bg-red-500/10">
                      <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </td>
                </tr>

                <tr v-if="!selectedShipments.length">
                  <td colspan="7" class="px-5 py-14 text-center">
                    <div
                      class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800">
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                      </svg>
                    </div>
                    <p class="font-medium text-gray-700 dark:text-gray-300">No shipments added yet</p>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Search by code above to add the first
                      one.</p>
                  </td>
                </tr>
              </tbody>

              <tfoot v-if="selectedShipments.length"
                class="border-t border-gray-200 bg-gray-50 font-semibold text-gray-900 dark:border-gray-700 dark:bg-gray-800/60 dark:text-white">
                <tr>
                  <td class="px-5 py-3.5" colspan="3">Totals</td>
                  <td class="px-5 py-3.5 text-right tabular-nums">{{ totalKg.toFixed(2) }}</td>
                  <td class="px-5 py-3.5 text-right tabular-nums">{{ money(totalCompanyCharges) }}</td>
                  <td class="px-5 py-3.5 text-right tabular-nums">{{ money(totalCod) }}</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </section>

      <!-- Action bar -->
      <div class="flex flex-col-reverse items-stretch justify-end gap-3 sm:flex-row sm:items-center">
        <router-link to="/admin/consolidations"
          class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:ring-gray-700">
          Cancel
        </router-link>
        <button type="submit" :disabled="submitting"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-8 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-500/30 disabled:cursor-not-allowed disabled:opacity-60">
          <svg v-if="submitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
          </svg>
          {{ submitting ? 'Saving…' : (isEdit ? 'Save changes' : 'Create consolidation') }}
        </button>
      </div>
    </form>
  </div>

  <div v-else
    class="rounded-2xl border border-red-200 bg-red-50 p-6 text-center text-red-600 dark:border-red-500/30 dark:bg-red-500/10">
    <p class="font-medium">Something went wrong. Please check the console.</p>
    <pre class="mt-4 overflow-auto rounded-lg bg-white p-4 text-left text-sm dark:bg-gray-900">{{ error }}</pre>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onBeforeUnmount, nextTick, onErrorCaptured, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useWarehouseStore } from '@/stores/warehouseStore'
import { useInternationalCourierStore } from '@/stores/internationalCourierStore'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const router = useRouter()
const route = useRoute()
const toast = useToast()
const isEdit = computed(() => !!route.params.id)
const pageTitle = ref(isEdit.value ? 'Edit Consolidation' : 'New Consolidation')

const warehouseStore = useWarehouseStore()
const courierStore = useInternationalCourierStore()

const warehouses = computed(() => warehouseStore.items)
const couriers = computed(() => courierStore.items)

const error = ref<string | null>(null)
onErrorCaptured((err) => {
  console.error('❌ ConsolidationForm error:', err)
  error.value = err.message || 'Unknown error'
  return false
})

const loading = ref(false)
const submitting = ref(false)
const searching = ref(false)
const shipmentCodeQuery = ref('')
const suggestions = ref<any[]>([])
const showSuggestions = ref(false)
const searchBox = ref<HTMLElement | null>(null)

const form = reactive({
  awb_number: '',
  warehouse_id: null,
  international_courier_id: null,
  date_departed: '',
  date_reached: '',
  other_charges: 0, // Replaces ware_house_charges, sales_tax, income_tax, caa_charges
  customs_duty: 0,
  shipment_ids: [] as number[],
})

const selectedShipments = ref<any[]>([])

const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

/* ---------- Derived totals ---------- */
const sumOf = (field: string) =>
  selectedShipments.value.reduce((sum, s) => sum + (Number(s[field]) || 0), 0)

const totalKg = computed(() => sumOf('weight_kgs'))
const totalCompanyCharges = computed(() => sumOf('company_charges'))
const totalCod = computed(() => sumOf('receivable_cod'))
const totalCost = computed(() => (Number(form.other_charges) || 0) + (Number(form.customs_duty) || 0))

/* ---------- Helpers ---------- */
function money(v: any) {
  return (Number(v) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function initials(name?: string) {
  if (!name) return '?'
  const parts = name.trim().split(/\s+/)
  return ((parts[0]?.[0] ?? '') + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase() || '?'
}

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

/* ---------- Shipment search (debounced) ---------- */
let searchTimer: ReturnType<typeof setTimeout> | null = null

function onSearchInput() {
  if (searchTimer) clearTimeout(searchTimer)
  if (shipmentCodeQuery.value.length < 2) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }
  searchTimer = setTimeout(searchShipments, 250)
}

async function searchShipments() {
  searching.value = true
  try {
    const res = await api.get('/admin/consolidations/shipmentsJson', {
      params: { q: shipmentCodeQuery.value }
    })
    const selectedIds = selectedShipments.value.map(s => s.id)
    suggestions.value = res.data.filter((s: any) => !selectedIds.includes(s.id))
    showSuggestions.value = true
  } catch (err) {
    console.error('Search failed:', err)
  } finally {
    searching.value = false
  }
}

function handleClickOutside(e: MouseEvent) {
  if (searchBox.value && !searchBox.value.contains(e.target as Node)) {
    showSuggestions.value = false
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
      toast.success('📦 Shipment added successfully!')
    })
    .catch(err => {
      toast.error('❌ ' + (err.response?.data?.message || 'Failed to fetch shipment details'))
    })
}

function removeShipment(index: number) {
  selectedShipments.value.splice(index, 1)
  form.shipment_ids = selectedShipments.value.map(s => s.id)
  toast.info('🗑️ Shipment removed')
}

/* ---------- Load / save ---------- */
async function loadConsolidation() {
  if (!isEdit.value) return
  loading.value = true
  try {
    const res = await api.get(`/admin/consolidations/${route.params.id}`)
    const data = res.data

    // Sum up the 4 fields into one for the frontend
    const mergedCharges = (Number(data.ware_house_charges) || 0) +
      (Number(data.sales_tax) || 0) +
      (Number(data.income_tax) || 0) +
      (Number(data.caa_charges) || 0)

    Object.assign(form, {
      awb_number: data.awb_number,
      warehouse_id: data.warehouse_id,
      international_courier_id: data.international_courier_id,
      date_departed: data.date_departed || '',
      date_reached: data.date_reached || '',
      other_charges: mergedCharges,
      customs_duty: Number(data.customs_duty) || 0,
      shipment_ids: data.shipments?.map((s: any) => s.id) || [],
    })
    selectedShipments.value = (data.shipments || []).map((s: any) =>
      coerceNumericShipment({ ...s })
    )
    await nextTick()
    updateDatePickers()
  } catch (err: any) {
    toast.error('❌ ' + (err.response?.data?.message || 'Failed to load consolidation'))
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  if (!selectedShipments.value.length) {
    toast.warning('⚠️ Please add at least one shipment.')
    return
  }
  submitting.value = true
  try {
    const payload = {
      awb_number: form.awb_number,
      warehouse_id: form.warehouse_id,
      international_courier_id: form.international_courier_id,
      date_departed: form.date_departed,
      date_reached: form.date_reached,

      // Map the merged UI field back to DB columns
      ware_house_charges: form.other_charges,
      customs_duty: form.customs_duty,
      sales_tax: 0,
      income_tax: 0,
      caa_charges: 0,

      shipment_ids: selectedShipments.value.map(s => s.id)
    }

    if (isEdit.value) {
      await api.put(`/admin/consolidations/${route.params.id}`, payload)
      toast.success('✅ Consolidation updated successfully!')
    } else {
      await api.post('/admin/consolidations', payload)
      toast.success('✅ Consolidation created successfully!')
    }
    router.push('/admin/consolidations')
  } catch (err: any) {
    toast.error('❌ ' + (err.response?.data?.message || 'Failed to save consolidation'))
  } finally {
    submitting.value = false
  }
}

/* ---------- Date pickers ---------- */
function initDatePickers() {
  const dateFields = ['date_departed', 'date_reached']
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
      const val = (form as any)[field]
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
        flatpickrInstances[field].setDate(clean)
      }
    }
  })
}

function updateDatePickers() {
  const dateFields = ['date_departed', 'date_reached']
  dateFields.forEach(field => {
    const inst = flatpickrInstances[field]
    const val = (form as any)[field]
    if (inst && val) {
      const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
      inst.setDate(clean)
    }
  })
}

onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  try {
    await Promise.all([
      warehouseStore.fetchItems(1, { per_page: 1000 }),
      courierStore.fetchItems(1, { per_page: 1000 })
    ])
  } catch (err) {
    console.error('Failed to load dropdown data:', err)
    error.value = 'Could not load required data.'
    toast.error('❌ Failed to load required data')
  }
  if (isEdit.value) {
    await loadConsolidation()
  }
  await nextTick()
  initDatePickers()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  if (searchTimer) clearTimeout(searchTimer)
  Object.values(flatpickrInstances).forEach(inst => {
    if (inst) inst.destroy()
  })
})

watch(() => route.params.id, async (newId) => {
  if (newId) {
    await loadConsolidation()
    await nextTick()
    updateDatePickers()
  }
})
</script>

<style scoped>
/* One shared input style so every field matches.
   Plain CSS using Tailwind theme variables, so no @reference is needed. */
.field {
  width: 100%;
  border-radius: 0.75rem;
  border: 1px solid var(--color-gray-300);
  background-color: #fff;
  padding: 0.625rem 0.875rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  color: var(--color-gray-800);
  box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
  transition: border-color 0.15s, box-shadow 0.15s;
}

.field::placeholder {
  color: var(--color-gray-400);
}

.field:focus {
  outline: none;
  border-color: var(--color-brand-500);
  box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-brand-500) 15%, transparent);
}

.dark .field {
  border-color: var(--color-gray-600);
  background-color: var(--color-gray-800);
  color: #fff;
}

.dark .field::placeholder {
  color: var(--color-gray-500);
}
</style>
