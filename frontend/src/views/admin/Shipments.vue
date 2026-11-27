<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Shipments'" />

    <!-- Filter Bar -->
    <div
      class="flex flex-wrap items-center gap-3 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
        <select v-model="statusFilter" @change="onStatusChange"
          class="h-9 rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
          <option value="">All</option>
          <option v-for="status in statusOptions" :key="status.id" :value="status.id">
            {{ status.name }}
          </option>
        </select>
      </div>
      <button @click="clearFilters" class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">
        Clear
      </button>

      <!-- ✅ Toggle Trashed Button -->
      <button @click="toggleTrashed" class="ml-auto text-sm font-medium px-4 py-1.5 rounded-lg border transition-colors"
        :class="showTrashed
          ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800'
          : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700'">
        {{ showTrashed ? '← View Active Shipments' : 'View Deleted Shipments →' }}
      </button>
    </div>

    <!-- Bulk Actions Bar -->
    <div v-if="selectedIds.length > 0"
      class="flex flex-wrap items-center gap-3 p-4 bg-brand-50 dark:bg-brand-900/20 rounded-xl border border-brand-200 dark:border-brand-800">
      <span class="text-sm font-medium text-brand-700 dark:text-brand-300">
        {{ selectedIds.length }} shipment(s) selected
      </span>
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Change Status:</label>
        <select v-model="bulkStatusId"
          class="h-9 rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
          <option value="">Select status</option>
          <option v-for="status in statusOptions" :key="status.id" :value="status.id">
            {{ status.name }}
          </option>
        </select>
        <button @click="applyBulkStatus" :disabled="!bulkStatusId || bulkUpdating"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-brand-600 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
          <span v-if="bulkUpdating" class="inline-block animate-spin mr-1">⟳</span>
          Apply
        </button>
        <button @click="clearSelection"
          class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
          Clear Selection
        </button>
      </div>
    </div>

    <DataTable :store="shipmentStore" :columns="columns" title="All Shipments" addButtonLabel="New Shipment"
      :modalComponent="ShipmentFormModal" :selfSaving="true" @saved="handleSaved">

      <template #header-start>
        <th class="border-b border-gray-100 px-5 py-3 text-left dark:border-white/[0.06]">
          <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll"
            class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700" />
        </th>
      </template>

      <template #row-start="{ item }">
        <td class="px-5 py-3.5">
          <input type="checkbox" :checked="isSelected(item.id)" @change="toggleSelect(item.id)"
            class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700" />
        </td>
      </template>

      <template #actions="{ item, edit }">
        <div class="flex items-center justify-end gap-0.5">
          <router-link :to="`/admin/shipments/${item.id}`"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
            title="View Details">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </router-link>

          <!-- ✅ Quick Pay Action -->
          <button v-if="!showTrashed" @click="openQuickPay(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg transition" :class="Number(item.receivable_cod) > 0
              ? 'text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 dark:hover:text-red-400'
              : (Number(item.amount_due) > 0
                ? 'text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400'
                : 'text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 dark:hover:text-blue-400')" :title="Number(item.receivable_cod) > 0
                  ? 'Collect COD'
                  : (Number(item.amount_due) > 0 ? 'Record Payment' : 'Manage Payments')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </button>

          <button v-if="!showTrashed" @click="edit(item)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
            title="Edit">
            <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M17.0911 3.53206C16.2124 2.65338 14.7878 2.65338 13.9091 3.53206L5.6074 11.8337C5.29899 12.1421 5.08687 12.5335 4.99684 12.9603L4.26177 16.445C4.20943 16.6931 4.286 16.9508 4.46529 17.1301C4.64458 17.3094 4.90232 17.3859 5.15042 17.3336L8.63507 16.5985C9.06184 16.5085 9.45324 16.2964 9.76165 15.988L18.0633 7.68631C18.942 6.80763 18.942 5.38301 18.0633 4.50433L17.0911 3.53206ZM14.9697 4.59272C15.2626 4.29982 15.7375 4.29982 16.0304 4.59272L17.0027 5.56499C17.2956 5.85788 17.2956 6.33276 17.0027 6.62565L16.1043 7.52402L14.0714 5.49109L14.9697 4.59272ZM13.0107 6.55175L6.66806 12.8944C6.56526 12.9972 6.49455 13.1277 6.46454 13.2699L5.96704 15.6283L8.32547 15.1308C8.46772 15.1008 8.59819 15.0301 8.70099 14.9273L15.0436 8.58468L13.0107 6.55175Z"
                fill="currentColor" />
            </svg>
          </button>

          <button v-if="!showTrashed" @click="handleDelete(item.id)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
            title="Delete">
            <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.04142 4.29199C7.04142 3.04935 8.04878 2.04199 9.29142 2.04199H11.7081C12.9507 2.04199 13.9581 3.04935 13.9581 4.29199V4.54199H16.1252H17.166C17.5802 4.54199 17.916 4.87778 17.916 5.29199C17.916 5.70621 17.5802 6.04199 17.166 6.04199H16.8752V8.74687V13.7469V16.7087C16.8752 17.9513 15.8678 18.9587 14.6252 18.9587H6.37516C5.13252 18.9587 4.12516 17.9513 4.12516 16.7087V13.7469V8.74687V6.04199H3.8335C3.41928 6.04199 3.0835 5.70621 3.0835 5.29199C3.0835 4.87778 3.41928 4.54199 3.8335 4.54199H4.87516H7.04142V4.29199ZM15.3752 13.7469V8.74687V6.04199H13.9581H13.2081H7.79142H7.04142H5.62516V8.74687V13.7469V16.7087C5.62516 17.1229 5.96095 17.4587 6.37516 17.4587H14.6252C15.0394 17.4587 15.3752 17.1229 15.3752 16.7087V13.7469ZM8.54142 4.54199H12.4581V4.29199C12.4581 3.87778 12.1223 3.54199 11.7081 3.54199H9.29142C8.87721 3.54199 8.54142 3.87778 8.54142 4.29199V4.54199ZM8.8335 8.50033C9.24771 8.50033 9.5835 8.83611 9.5835 9.25033V14.2503C9.5835 14.6645 9.24771 15.0003 8.8335 15.0003C8.41928 15.0003 8.0835 14.6645 8.0835 14.2503V9.25033C8.0835 8.83611 8.41928 8.50033 8.8335 8.50033ZM12.9168 9.25033C12.9168 8.83611 12.581 8.50033 12.1668 8.50033C11.7526 8.50033 11.4168 8.83611 11.4168 9.25033V14.2503C11.4168 14.6645 11.7526 15.0003 12.1668 15.0003C12.581 15.0003 12.9168 14.6645 12.9168 14.2503V9.25033Z"
                fill="currentColor" />
            </svg>
          </button>

          <button v-if="showTrashed" @click="handleRestore(item.id)"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-500 transition hover:bg-emerald-50 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400"
            title="Restore">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </template>

      <template #cell-status="{ item }">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
            :class="statusBadgeClass(item.shipment_status?.name || '')">
            <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(item.shipment_status?.name || '')"></span>
            {{ item.shipment_status?.name || 'Unknown' }}
          </span>
          <select v-if="!showTrashed"
            @change="updateStatus(item.id, Number(($event.target as HTMLSelectElement).value))"
            class="h-6 rounded border border-gray-200 bg-white text-xs dark:border-gray-700 dark:bg-gray-800">
            <option v-for="status in statusOptions" :key="status.id" :value="status.id"
              :selected="status.id === item.shipment_status_id">
              {{ status.name }}
            </option>
          </select>
        </div>
      </template>

      <template #cell-total="{ item }">
        <span class="font-mono">{{ formatCurrency(item.total) }}</span>
      </template>

      <template #cell-user="{ item }">
        {{ item.user?.name || 'N/A' }}
      </template>
    </DataTable>
  </div>

  <!-- ✅ FULL Quick Pay Modal -->
  <div v-if="quickPayModalOpen"
    class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 p-4 pt-20 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
            Record Payment - {{ quickPayShipment?.shipment_code }}
          </h3>
          <button @click="quickPayModalOpen = false"
            class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        <!-- ✅ Summary Breakdown -->
        <div class="mb-5 bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center py-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Total Payable</span>
            <span
              class="font-medium text-gray-800 dark:text-gray-200">{{ formatCurrency(quickPayShipment?.total) }}</span>
          </div>
          <div class="flex justify-between items-center py-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Already Paid</span>
            <span
              class="font-medium text-gray-800 dark:text-gray-200">{{ formatCurrency(quickPayShipment?.received_amount) }}</span>
          </div>
          <div class="flex justify-between items-center py-2 mt-1 border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Remaining to Collect</span>
            <span class="text-xl font-bold text-brand-600 dark:text-brand-400">
              {{ formatCurrency(Math.max(0, Number(quickPayShipment?.total || 0) - Number(quickPayShipment?.received_amount || 0))) }}
            </span>
          </div>
        </div>

        <div class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (PKR)</label>
              <input v-model.number="quickPayAmount" type="number" step="0.01"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                placeholder="0.00" />
            </div>

            <!-- Payment Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
              <div class="relative">
                <input ref="quickPayDateInput" v-model="quickPayDate" type="text"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                  placeholder="Select date" readonly />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                      clip-rule="evenodd" />
                  </svg>
                </span>
              </div>
            </div>

            <!-- Payment Method -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
              <select v-model="quickPayMethod"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                <option value="">Select method</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.name"
                  :disabled="method.name === 'Cash on Delivery' && !quickPayShipment?.date_delivered">
                  {{ method.name }}
                  <template v-if="method.name === 'Cash on Delivery' && !quickPayShipment?.date_delivered"> (available
                    after delivery)</template>
                </option>
              </select>
            </div>

            <!-- Reference Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference Number</label>
              <input v-model="quickPayReference" placeholder="e.g. TXN12345"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
            </div>
          </div>

          <!-- ✅ NEW: Actual Courier Charges Field -->
          <div v-if="quickPayMethod === 'Cash on Delivery' && quickPayShipment?.date_delivered"
            class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Actual Courier Charges (PKR)
            </label>
            <input v-model.number="quickPayCourierCharges" type="number" step="0.01" min="0"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
            <p class="text-xs text-gray-500 mt-2">
              This will update the shipment's courier fee and deduct it from the COD remittance.
            </p>

            <div
              class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between font-bold text-brand-600 dark:text-brand-400">
              <span>Net Amount to be Deposited in Bank:</span>
              <span>{{ formatCurrency(quickPayNetRemittance) }}</span>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
            <textarea v-model="quickPayNotes" rows="2" placeholder="Optional notes"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 mt-4 border-t border-gray-100 dark:border-gray-700">
          <button @click="quickPayModalOpen = false" :disabled="submittingQuickPay"
            class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 transition disabled:opacity-50">
            Cancel
          </button>
          <button @click="submitQuickPay"
            :disabled="submittingQuickPay || !quickPayAmount || quickPayAmount <= 0 || !quickPayDate || !quickPayMethod"
            class="inline-flex items-center gap-2 px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 disabled:opacity-50 transition">
            <span v-if="submittingQuickPay" class="inline-block animate-spin">⟳</span>
            {{ submittingQuickPay ? 'Saving...' : 'Save Payment' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, nextTick, watch } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import DataTable from '@/components/common/DataTable.vue'
import ShipmentFormModal from '@/components/admin/ShipmentFormModal.vue'
import { useShipmentStore } from '@/stores/shipmentStore'
import { useShipmentStatusStore } from '@/stores/shipmentStatusStore'
import { useShipmentPaymentStore } from '@/stores/shipmentPaymentStore'
import { usePaymentMethodStore } from '@/stores/paymentMethodStore'
import type { ColumnDefinition } from '@/types/table'
import { useToastStore } from '@/stores/toastStore'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const shipmentStore = useShipmentStore()
const statusStore = useShipmentStatusStore()
const paymentStore = useShipmentPaymentStore()
const paymentMethodStore = usePaymentMethodStore()
const toastStore = useToastStore()

const statusOptions = ref<{ id: number; name: string }[]>([])
const paymentMethods = ref<any[]>([])
const statusFilter = ref('')
const showTrashed = ref(false)

// Selection state
const selectedIds = ref<number[]>([])
const bulkStatusId = ref<number | string>('')
const bulkUpdating = ref(false)

// Quick Pay State
const quickPayModalOpen = ref(false)
const quickPayShipment = ref<any>(null)
const quickPayAmount = ref<number | null>(null)
const quickPayDate = ref(new Date().toISOString().split('T')[0])
const quickPayMethod = ref('')
const quickPayReference = ref('')
const quickPayNotes = ref('')
const quickPayCourierCharges = ref<number>(0) // ✅ NEW
const quickPayDateInput = ref<HTMLInputElement | null>(null)
const submittingQuickPay = ref(false)
let flatpickrInstance: any = null

// ✅ NEW: Live calculation for Net Remittance
const quickPayNetRemittance = computed(() => {
  const gross = quickPayAmount.value || 0
  const charges = quickPayCourierCharges.value || 0
  return Math.max(0, gross - charges)
})

// ✅ NEW: Watch for method changes to auto-fill courier charges
watch(quickPayMethod, (newVal) => {
  if (newVal === 'Cash on Delivery' && quickPayShipment.value?.date_delivered) {
    quickPayCourierCharges.value = quickPayShipment.value.delivery_charges || 0

    const total = Number(quickPayShipment.value?.total) || 0
    const received = Number(quickPayShipment.value?.received_amount) || 0
    quickPayAmount.value = Math.max(0, total - received)
  }
})

const openQuickPay = (item: any) => {
  quickPayShipment.value = item

  const total = Number(item.total) || 0
  const received = Number(item.received_amount) || 0
  const grossRemaining = Math.max(0, total - received)

  quickPayAmount.value = grossRemaining
  quickPayCourierCharges.value = item.delivery_charges || 0 // ✅ Initialize

  if (item.receivable_cod > 0 || (item.local_courier_id && item.date_delivered)) {
    quickPayMethod.value = 'Cash on Delivery'
  } else {
    quickPayMethod.value = 'Bank Transfer'
  }

  quickPayDate.value = new Date().toISOString().split('T')[0]
  quickPayReference.value = ''
  quickPayNotes.value = ''

  quickPayModalOpen.value = true

  nextTick(() => {
    if (quickPayDateInput.value) {
      if (flatpickrInstance) flatpickrInstance.destroy()
      flatpickrInstance = flatpickr(quickPayDateInput.value, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        defaultDate: quickPayDate.value,
        onChange: (_dates, dateStr) => {
          quickPayDate.value = dateStr
        }
      })
    }
  })
}

const submitQuickPay = async () => {
  if (submittingQuickPay.value) return
  if (!quickPayAmount.value || quickPayAmount.value <= 0 || !quickPayDate.value || !quickPayMethod.value) return

  submittingQuickPay.value = true

  try {
    await paymentStore.createPayment(quickPayShipment.value.id, {
      amount: quickPayAmount.value,
      payment_date: quickPayDate.value,
      payment_method: quickPayMethod.value,
      reference_number: quickPayReference.value,
      notes: quickPayNotes.value || 'Quick payment from list',
      // ✅ Send the courier charges if it's a COD payment
      actual_courier_charges: quickPayMethod.value === 'Cash on Delivery' ? quickPayCourierCharges.value : undefined
    })
    toastStore.success('Payment recorded successfully')
    quickPayModalOpen.value = false
    await shipmentStore.fetchItems(shipmentStore.pagination?.current_page || 1, { trashed: showTrashed.value })
  } catch (error: any) {
    alert(error.response?.data?.message || 'Failed to record payment')
  } finally {
    submittingQuickPay.value = false
  }
}

const toggleTrashed = () => {
  showTrashed.value = !showTrashed.value
  shipmentStore.fetchItems(1, { trashed: showTrashed.value })
  clearSelection()
}

const handleDelete = async (id: number) => {
  const password = window.prompt(
    '⚠️ WARNING: This will soft-delete the shipment and all its payments/vouchers.\n\nPlease enter the Admin Password to confirm:'
  )
  if (password === null) return
  if (!password.trim()) {
    alert('Password is required to delete.')
    return
  }

  try {
    await shipmentStore.delete(id, password)
    toastStore.success('Shipment and related records deleted successfully.')
    await shipmentStore.fetchItems(shipmentStore.pagination?.current_page || 1, { trashed: showTrashed.value })
  } catch (error: any) {
    alert(error.response?.data?.message || 'Failed to delete shipment. Incorrect password?')
  }
}

const handleRestore = async (id: number) => {
  if (!confirm('Are you sure you want to restore this shipment?\n\nNote: It will NOT be re-added to its previous consolidation.')) {
    return
  }

  const password = window.prompt('🔐 Please enter the Admin Password to restore this shipment:')
  if (password === null) return
  if (!password.trim()) {
    alert('Password is required to restore.')
    return
  }

  try {
    await shipmentStore.restore(id, password, showTrashed.value)
    toastStore.success('Shipment and related records restored successfully.')
  } catch (error: any) {
    alert(error.response?.data?.message || 'Failed to restore shipment. Incorrect password?')
  }
}

const columns: ColumnDefinition[] = [
  { key: 'user', label: 'User' },
  { key: 'shipment_code', label: 'Sh-Code' },
  {
    key: 'weight',
    label: 'Weight',
    format: (v: any) => {
      if (v === undefined || v === null || v === '') return '0.00'
      const num = typeof v === 'string' ? parseFloat(v) : v
      return isNaN(num) ? '0.00' : num.toFixed(2)
    },
  },
  {
    key: 'shipment_status',
    label: 'Status',
    format: (value: any) => value?.name || 'Unknown',
  },
  {
    key: 'total',
    label: 'Total',
    format: (v: any) => formatCurrency(v),
  },
  {
    key: 'receivable_cod',
    label: 'COD ',
    format: (v: any) => formatCurrency(v),
  },
]

const isAllSelected = computed(() => {
  if (!shipmentStore.items || shipmentStore.items.length === 0) return false
  return shipmentStore.items.every((item: any) => selectedIds.value.includes(item.id))
})

const isSelected = (id: number) => selectedIds.value.includes(id)

const toggleSelect = (id: number) => {
  const index = selectedIds.value.indexOf(id)
  if (index > -1) {
    selectedIds.value.splice(index, 1)
  } else {
    selectedIds.value.push(id)
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = shipmentStore.items.map((item: any) => item.id)
  }
}

const clearSelection = () => {
  selectedIds.value = []
  bulkStatusId.value = ''
}

const applyBulkStatus = async () => {
  if (!bulkStatusId.value || selectedIds.value.length === 0 || bulkUpdating.value) return

  const selectedStatus = statusOptions.value.find(s => s.id === Number(bulkStatusId.value))

  if (selectedStatus?.name === 'Delivered') {
    const confirmMsg = `WARNING: You are marking ${selectedIds.value.length} shipment(s) as DELIVERED.\n\nThis will generate Revenue Recognition and COGS vouchers, and create a COD Receivable.\n\nAre you sure?`
    if (!confirm(confirmMsg)) return
  } else {
    if (!confirm(`Change status to ${selectedStatus?.name} for ${selectedIds.value.length} shipment(s)?`)) return
  }

  bulkUpdating.value = true
  try {
    const promises = selectedIds.value.map(id =>
      shipmentStore.updateStatus(id, Number(bulkStatusId.value))
    )
    await Promise.all(promises)
    await shipmentStore.fetchItems(shipmentStore.pagination?.current_page || 1, { trashed: showTrashed.value })
    clearSelection()
    alert('Status updated successfully for all selected shipments!')
  } catch (error) {
    console.error('Bulk status update failed:', error)
    alert('Failed to update status for some shipments. Please try again.')
  } finally {
    bulkUpdating.value = false
  }
}

const statusBadgeClass = (status: string) => {
  const map: Record<string, string> = {
    Delivered: 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
    'Out for Delivery': 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
    'Reached Lahore Company Office': 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
    'Custom Office at Lahore Airport': 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
    'Departed Operations Facility - In Transit': 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
    'Reached Shipment in USA facility': 'bg-cyan-50 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400',
    'Bought by Company': 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
    'Bought by Customer': 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
    'Damaged - Returned': 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    'Damaged - Awaiting Return': 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
    'Returned to Sender': 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
  }
  return map[status] || 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400'
}

const statusDotClass = (status: string) => {
  const map: Record<string, string> = {
    Delivered: 'bg-green-500',
    'Out for Delivery': 'bg-blue-500',
    'Reached Lahore Company Office': 'bg-purple-500',
    'Custom Office at Lahore Airport': 'bg-yellow-500',
    'Departed Operations Facility - In Transit': 'bg-indigo-500',
    'Reached Shipment in USA facility': 'bg-cyan-500',
    'Bought by Company': 'bg-gray-500',
    'Bought by Customer': 'bg-gray-500',
    'Damaged - Returned': 'bg-red-500',
    'Damaged - Awaiting Return': 'bg-orange-500',
    'Returned to Sender': 'bg-red-500',
  }
  return map[status] || 'bg-gray-500'
}

const formatCurrency = (value: any) => {
  const num = typeof value === 'string' ? parseFloat(value) : value
  if (isNaN(num)) return 'PKR 0.00'
  return new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(num)
}

const fetchStatuses = async () => {
  await statusStore.fetchItems(1, { per_page: 100 })
  statusOptions.value = statusStore.items
}

const updateStatus = async (id: number, statusId: number) => {
  try {
    await shipmentStore.updateStatus(id, statusId, showTrashed.value)
    await shipmentStore.fetchItems(shipmentStore.pagination?.current_page || 1, { trashed: showTrashed.value })
  } catch (error) {
    console.error('Status update failed:', error)
    alert('Failed to update status. Please try again.')
  }
}

const onStatusChange = () => {
  shipmentStore.setStatusFilter(String(statusFilter.value))
  clearSelection()
}

const clearFilters = () => {
  statusFilter.value = ''
  shipmentStore.setStatusFilter('')
  clearSelection()
}

const handleSaved = async () => {
  await shipmentStore.fetchItems(shipmentStore.pagination?.current_page || 1, { trashed: showTrashed.value })
  clearSelection()
}

onMounted(async () => {
  shipmentStore.fetchItems(1, { trashed: showTrashed.value })
  fetchStatuses()
  await paymentMethodStore.fetchItems(1, { per_page: 100 })
  paymentMethods.value = paymentMethodStore.items
})
</script>
