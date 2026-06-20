<template>

    <PageBreadcrumb :pageTitle="pageTitle" />
    <div
      class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="max-w-7xl">
        <form @submit.prevent="submitForm">
          <!-- Consolidation Details & Costs (2 columns) -->
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Left: Details -->
            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
              <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Consolidation Details</h3>
              <div class="space-y-3">
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">Warehouse</label>
                  <select v-model="form.warehouse_id"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm">
                    <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                  </select>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">Courier</label>
                  <input v-model="form.courier"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">Date Departed</label>
                  <input v-model="form.date_departed" type="date"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">Date Reached</label>
                  <input v-model="form.date_reached" type="date"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">AWB Number</label>
                  <input v-model="form.awb_number"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">Date Dispatched</label>
                  <input v-model="form.date_dispatched" type="date"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                </div>
              </div>
            </div>

            <!-- Right: Costs -->
            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
              <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Costs</h3>
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700 dark:text-gray-400">Warehouse Charges</label>
                  <input v-model.number="form.ware_house_charges" type="number" step="0.01"
                    class="dark:bg-dark-900 h-11 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm text-right"
                    @input="recalcTotals" />
                </div>
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700 dark:text-gray-400">Customs Duty</label>
                  <input v-model.number="form.customs_duty" type="number" step="0.01"
                    class="dark:bg-dark-900 h-11 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm text-right"
                    @input="recalcTotals" />
                </div>
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700 dark:text-gray-400">Sales Tax</label>
                  <input v-model.number="form.sales_tax" type="number" step="0.01"
                    class="dark:bg-dark-900 h-11 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm text-right"
                    @input="recalcTotals" />
                </div>
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700 dark:text-gray-400">Income Tax</label>
                  <input v-model.number="form.income_tax" type="number" step="0.01"
                    class="dark:bg-dark-900 h-11 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm text-right"
                    @input="recalcTotals" />
                </div>
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700 dark:text-gray-400">CAA Charges</label>
                  <input v-model.number="form.caa_charges" type="number" step="0.01"
                    class="dark:bg-dark-900 h-11 w-40 rounded-lg border border-gray-300 px-3 py-2 text-sm text-right"
                    @input="recalcTotals" />
                </div>
              </div>
            </div>
          </div>

          <!-- Shipments -->
          <div class="mt-6 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Shipments</h3>
            <!-- Autocomplete -->
            <div class="mb-4 flex items-center gap-4">
              <div class="relative flex-1">
                <input v-model="psiQuery" @input="searchShipments" placeholder="Type PSI to add..."
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm" />
                <div v-if="showSuggestions && suggestions.length"
                  class="absolute z-10 mt-1 w-full rounded-lg border bg-white shadow-lg dark:bg-gray-800">
                  <div v-for="s in suggestions" :key="s.id" @click="addShipment(s)"
                    class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">{{ s.psi }}</div>
                </div>
              </div>
              <span class="text-sm text-gray-500">Total KGs: {{ totalKg.toFixed(2) }}</span>
            </div>

            <!-- Shipment table -->
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                  <tr>
                    <th class="px-3 py-2 text-left">PSI</th>
                    <th class="px-3 py-2 text-left">Customer</th>
                    <th class="px-3 py-2 text-left">City</th>
                    <th class="px-3 py-2 text-left">Arrival Date</th>
                    <th class="px-3 py-2 text-left">Item</th>
                    <th class="px-3 py-2 text-right">KGs</th>
                    <th class="px-3 py-2 text-right">PS Charges</th>
                    <th class="px-3 py-2 text-right">Amount Due</th>
                    <th class="px-3 py-2 text-right">COD</th>
                    <th class="px-3 py-2 text-left">Delivery By</th>
                    <th class="px-3 py-2 text-right">BE Charges</th>
                    <th class="px-3 py-2 text-right">Blue-Ex</th>
                    <th class="px-3 py-2 text-right">Output Tax</th>
                    <th class="px-3 py-2 text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(s, index) in selectedShipments" :key="s.id"
                    class="border-b hover:bg-gray-50 dark:border-gray-700">
                    <td class="px-3 py-2 font-mono">{{ s.psi }}</td>
                    <td class="px-3 py-2">{{ s.user?.name }}</td>
                    <td class="px-3 py-2">{{ s.user?.city?.city_name }}</td>
                    <td class="px-3 py-2">{{ s.arrival_date || '—' }}</td>
                    <td class="px-3 py-2 max-w-xs truncate" :title="s.description">{{ s.description || '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ s.weight_kgs?.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ s.company_charges?.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ s.amount_due?.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ s.receivable_cod?.toFixed(2) }}</td>
                    <td class="px-3 py-2">{{ s.local_delivery_by || '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ s.blueex_charges?.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ s.receivable_cod?.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ s.output_tax || 0 }}</td>
                    <td class="px-3 py-2 text-center">
                      <button type="button" @click="removeShipment(index)"
                        class="text-red-500 hover:underline">Remove</button>
                    </td>
                  </tr>
                  <tr v-if="!selectedShipments.length">
                    <td colspan="14" class="py-4 text-center text-gray-400">No shipments added yet.</td>
                  </tr>
                </tbody>
                <tfoot class="font-bold">
                  <tr>
                    <td colspan="5" class="px-3 py-2 text-right">Totals</td>
                    <td class="px-3 py-2 text-right">{{ totalKg.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ totalPsCharges.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ totalAmountDue.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ totalCod.toFixed(2) }}</td>
                    <td colspan="2"></td>
                    <td class="px-3 py-2 text-right">{{ totalBluexCharges.toFixed(2) }}</td>
                    <td class="px-3 py-2 text-right">{{ totalOutputTax.toFixed(2) }}</td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Submit -->
          <div class="mt-6 flex justify-end gap-4">
            <router-link to="/admin/consolidations"
              class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</router-link>
            <button type="submit" :disabled="submitting"
              class="rounded-lg bg-brand-500 px-6 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50">
              {{ submitting ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const router = useRouter()
const route = useRoute()
const isEdit = computed(() => !!route.params.id)
const pageTitle = ref(isEdit.value ? 'Edit Consolidation' : 'New Consolidation')

const warehouses = ref<any[]>([])
const loading = ref(false)
const submitting = ref(false)
const psiQuery = ref('')
const suggestions = ref<any[]>([])
const showSuggestions = ref(false)

const form = reactive({
  awb_number: '',
  warehouse_id: null,
  date_dispatched: '',
  courier: '',
  date_departed: '',
  date_reached: '',
  ware_house_charges: 0,
  customs_duty: 0,
  sales_tax: 0,
  income_tax: 0,
  caa_charges: 0,
  shipment_ids: [] as number[], // will hold IDs of selected shipments
})

const selectedShipments = ref<any[]>([])

// Computed totals from selected shipments
const totalKg = computed(() => selectedShipments.value.reduce((sum, s) => sum + (s.weight_kgs || 0), 0))
const totalPsCharges = computed(() => selectedShipments.value.reduce((sum, s) => sum + (s.company_charges || 0), 0))
const totalAmountDue = computed(() => selectedShipments.value.reduce((sum, s) => sum + (s.amount_due || 0), 0))
const totalCod = computed(() => selectedShipments.value.reduce((sum, s) => sum + (s.receivable_cod || 0), 0))
const totalBluexCharges = computed(() => selectedShipments.value.reduce((sum, s) => sum + (s.blueex_charges || 0), 0))
const totalOutputTax = computed(() => selectedShipments.value.reduce((sum, s) => sum + (s.output_tax || 0), 0))

// When totals change, we don't need to store them in form; backend will recalc on save.

async function fetchWarehouses() {
  const res = await api.get('/admin/warehouses')
  warehouses.value = res.data
}

async function searchShipments() {
  if (psiQuery.value.length < 2) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }
  const res = await api.get('/admin/consolidations/shipmentsJson', { params: { q: psiQuery.value } })
  // Filter out already selected
  const selectedIds = selectedShipments.value.map(s => s.id)
  suggestions.value = res.data.filter((s: any) => !selectedIds.includes(s.id))
  showSuggestions.value = true
}

function addShipment(s: any) {
  // Fetch full details
  api.get('/admin/consolidations/shipment', { params: { shipment_tracker_id: s.psi } })
    .then(res => {
      selectedShipments.value.push(res.data)
      psiQuery.value = ''
      suggestions.value = []
      showSuggestions.value = false
      // Update form.shipment_ids
      form.shipment_ids = selectedShipments.value.map(s => s.id)
    })
    .catch(err => alert('Failed to fetch shipment details'))
}

function removeShipment(index: number) {
  selectedShipments.value.splice(index, 1)
  form.shipment_ids = selectedShipments.value.map(s => s.id)
}

// Recalculate costs on input changes (auto-fill some fields)
function recalcTotals() {
  // We'll let backend do the heavy lifting, but we can update some derived fields here if needed.
  // For now, nothing.
}

// Edit mode: load existing consolidation
async function loadConsolidation() {
  if (!isEdit.value) return
  loading.value = true
  try {
    const res = await api.get(`/admin/consolidations/${route.params.id}`)
    const data = res.data
    // Populate form
    Object.assign(form, {
      awb_number: data.awb_number,
      warehouse_id: data.warehouse_id,
      date_dispatched: data.date_dispatched,
      courier: data.courier,
      date_departed: data.date_departed,
      date_reached: data.date_reached,
      ware_house_charges: data.ware_house_charges || 0,
      customs_duty: data.customs_duty || 0,
      sales_tax: data.sales_tax || 0,
      income_tax: data.income_tax || 0,
      caa_charges: data.caa_charges || 0,
      shipment_ids: data.shipments?.map((s: any) => s.id) || [],
    })
    selectedShipments.value = data.shipments || []
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  // Validate at least one shipment
  if (!selectedShipments.value.length) {
    alert('Please add at least one shipment.')
    return
  }
  submitting.value = true
  try {
    const payload = { ...form, shipment_ids: selectedShipments.value.map(s => s.id) }
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

onMounted(async () => {
  await fetchWarehouses()
  if (isEdit.value) await loadConsolidation()
})

// Watch for click outside to close suggestions (not implemented fully)
</script>
