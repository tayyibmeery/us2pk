<template>
  <Modal :isOpen="isOpen" @close="close" size="xl">
    <template #body>
      <div class="relative bg-white dark:bg-gray-900 p-8 lg:p-12">

        <!-- Action Buttons (Hidden when printing) -->
        <div class="absolute right-4 top-4 flex gap-2 print:hidden">

          <!-- ✅ Cancel Invoice (only when pending/overdue) -->
          <button v-if="canCancel" @click="openCancelDialog" :disabled="cancelling"
            class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition disabled:opacity-50 dark:bg-red-900/20 dark:text-red-400"
            title="Cancel this invoice and free its shipments">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancel Invoice
          </button>

          <button @click="handlePrint" :disabled="printing"
            class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 transition disabled:opacity-50">
            <svg v-if="!printing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span v-else class="animate-spin">⟳</span>
            {{ printing ? 'Preparing...' : 'Print' }}
          </button>

          <button @click="handleDownload" :disabled="downloading"
            class="flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition disabled:opacity-50">
            <svg v-if="!downloading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span v-else class="animate-spin">⟳</span>
            {{ downloading ? 'Downloading...' : 'Download PDF' }}
          </button>

          <button @click="close" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg dark:hover:bg-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="invoice" class="max-w-4xl mx-auto">
          <!-- Header -->
          <div class="flex justify-between items-start border-b-2 border-gray-900 dark:border-white pb-6 mb-8">
            <div>
              <div class="text-2xl font-black tracking-wider text-gray-900 dark:text-white">US2PK</div>
              <p class="text-xs text-gray-500 tracking-widest mt-1">BRINGING OPPORTUNITIES HOME</p>
            </div>
            <div class="text-right">
              <h1 class="text-4xl font-light text-gray-400 dark:text-gray-600">INVOICE</h1>
              <p class="text-sm font-bold text-gray-900 dark:text-white mt-2">#{{ invoice.invoice_no }}</p>
            </div>
          </div>

          <!-- ✅ Cancelled banner -->
          <div v-if="invoice.status === 'cancelled'"
            class="mb-6 rounded-xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">This invoice has been cancelled</p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
              Its shipments were released and can be invoiced again.
            </p>
          </div>

          <!-- Info Grid -->
          <div class="grid grid-cols-3 gap-8 mb-10">
            <div>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</p>
              <p class="font-bold text-gray-900 dark:text-white">
                {{ invoice.user?.name || invoice.shipment?.user?.name || 'N/A' }}
              </p>
              <p class="text-sm text-gray-500">
                {{ invoice.user?.email || invoice.shipment?.user?.email || 'N/A' }}
              </p>
              <p class="text-sm text-gray-500">
                {{ invoice.user?.city?.city_name || invoice.shipment?.user?.city?.city_name || 'N/A' }}
              </p>
            </div>
            <div>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Shipments Covered</p>
              <template v-if="invoice.shipments && invoice.shipments.length">
                <p class="font-bold text-gray-900 dark:text-white">
                  {{ invoice.shipments.length }} shipment{{ invoice.shipments.length > 1 ? 's' : '' }}
                </p>
                <p class="text-sm text-gray-500">
                  Total weight:
                  {{invoice.shipments.reduce((s: number, x: any) => s + Number(x.weight || 0), 0)}} KG
                </p>
              </template>
              <template v-else>
                <p class="font-bold text-gray-900 dark:text-white">{{ invoice.shipment?.shipment_code || 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ invoice.shipment?.description || 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ invoice.shipment?.weight || '0' }} KG</p>
              </template>
            </div>
            <div>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Invoice Details</p>
              <div class="flex justify-between text-sm py-0.5">
                <span class="text-gray-500">Date:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(invoice.date) }}</span>
              </div>
              <div class="flex justify-between text-sm py-0.5">
                <span class="text-gray-500">Status:</span>
                <span class="font-bold uppercase" :class="statusColor">{{ invoice.status }}</span>
              </div>
            </div>
          </div>

          <!-- Items Table -->
          <table class="w-full mb-8">
            <thead>
              <tr class="bg-gray-900 dark:bg-gray-800 text-white text-xs uppercase tracking-wider">
                <th class="py-3 px-4 text-left font-medium">Description</th>
                <th class="py-3 px-4 text-right font-medium">Amount (PKR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <template v-if="invoice.shipments && invoice.shipments.length">
                <tr v-for="s in invoice.shipments" :key="s.id">
                  <td class="py-4 px-4">
                    <p class="font-bold text-gray-900 dark:text-white">Shipment Charges</p>
                    <p class="text-xs text-gray-500 mt-1">
                      Ref: {{ s.shipment_code }}
                      <template v-if="s.description"> · {{ s.description }}</template>
                      <template v-if="s.weight"> · {{ s.weight }} KG</template>
                    </p>
                  </td>
                  <td class="py-4 px-4 text-right font-mono font-medium">
                    {{ formatCurrency(s.pivot?.amount ?? 0) }}
                  </td>
                </tr>
              </template>
              <template v-else>
                <tr>
                  <td class="py-4 px-4">
                    <p class="font-bold text-gray-900 dark:text-white">Shipment Charges</p>
                    <p class="text-xs text-gray-500 mt-1">Ref: {{ invoice.shipment?.shipment_code || 'N/A' }}</p>
                  </td>
                  <td class="py-4 px-4 text-right font-mono font-medium">{{ formatCurrency(invoice.amount_due) }}</td>
                </tr>
              </template>

              <tr v-if="invoice.cod > 0">
                <td class="py-4 px-4">
                  <p class="font-bold text-gray-900 dark:text-white">Cash on Delivery (COD)</p>
                </td>
                <td class="py-4 px-4 text-right font-mono font-medium">{{ formatCurrency(invoice.cod) }}</td>
              </tr>

              <tr v-if="invoice.output_tax > 0">
                <td class="py-4 px-4">
                  <p class="font-bold text-gray-900 dark:text-white">Output Tax</p>
                </td>
                <td class="py-4 px-4 text-right font-mono font-medium">{{ formatCurrency(invoice.output_tax) }}</td>
              </tr>
            </tbody>
          </table>

          <!-- Totals -->
          <div class="flex justify-end mb-12">
            <div class="w-64 space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-mono">{{ formatCurrency(invoice.amount_due) }}</span>
              </div>
              <div v-if="invoice.output_tax > 0" class="flex justify-between text-sm">
                <span class="text-gray-500">Tax</span>
                <span class="font-mono">{{ formatCurrency(invoice.output_tax) }}</span>
              </div>
              <div
                class="flex justify-between text-lg font-bold border-t-2 border-gray-900 dark:border-white pt-2 mt-2">
                <span>Total Amount</span>
                <span class="font-mono text-brand-600 dark:text-brand-400">
                  {{ formatCurrency(invoice.amount_due + invoice.output_tax) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Footer Signature Area -->
          <div class="flex justify-between items-end border-t border-gray-200 dark:border-gray-700 pt-8 mt-12">
            <div class="text-xs text-gray-500">
              <p>Thank you for your business!</p>
              <p class="mt-1">Generated on {{ new Date().toLocaleString() }}</p>
            </div>
            <div class="text-center relative">
              <div class="h-10 mb-1 text-gray-300 dark:text-gray-600 italic text-sm">[Signature Placeholder]</div>
              <div
                class="w-48 border-t border-gray-400 pt-2 text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                Authorized Signature
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>

  <!-- ✅ Cancel confirmation dialog -->
  <div v-if="showCancelDialog" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4"
    @click.self="closeCancelDialog">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
      <h3 class="text-lg font-bold text-gray-900 dark:text-white">Cancel this invoice?</h3>
      <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        Invoice <span class="font-semibold text-gray-800 dark:text-gray-200">#{{ invoice?.invoice_no }}</span>
        will be marked as <span class="font-semibold text-red-600 dark:text-red-400">cancelled</span>.
        All its shipments will be <strong>freed</strong> and can be invoiced again.
        <br><br>
        This action cannot be undone.
      </p>

      <label class="mt-4 block text-sm font-medium text-gray-700 dark:text-gray-300">
        Reason (optional)
      </label>
      <textarea v-model="cancelReason" rows="3" placeholder="Why is this invoice being cancelled?"
        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800"></textarea>

      <div class="mt-6 flex justify-end gap-3">
        <button @click="closeCancelDialog" :disabled="cancelling"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-800 dark:text-gray-300">
          Keep Invoice
        </button>
        <button @click="confirmCancel" :disabled="cancelling"
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
          {{ cancelling ? 'Cancelling…' : 'Yes, Cancel Invoice' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Modal from '@/components/Modal.vue'
import api from '@/services/api'
import { useToastStore } from '@/stores/toastStore'
import { useInvoiceStore } from '@/stores/invoiceStore'

const props = defineProps<{
  isOpen: boolean
  invoice?: any | null
}>()

const emit = defineEmits<{ (e: 'close'): void }>()

const toastStore = useToastStore()
const invoiceStore = useInvoiceStore()

const downloading = ref(false)
const printing = ref(false)

/* ✅ Cancel state */
const showCancelDialog = ref(false)
const cancelling = ref(false)
const cancelReason = ref('')

const statusColor = computed(() => {
  if (props.invoice?.status === 'paid') return 'text-emerald-500'
  if (props.invoice?.status === 'overdue') return 'text-red-500'
  if (props.invoice?.status === 'cancelled') return 'text-gray-400 line-through'
  return 'text-amber-500'
})

/* ✅ Show cancel button only for pending/overdue */
const canCancel = computed(
  () =>
    !!props.invoice &&
    ['pending', 'overdue'].includes(props.invoice.status)
)

const formatCurrency = (value: number) =>
  new Intl.NumberFormat('en-PK', { style: 'currency', currency: 'PKR' }).format(value || 0)

const formatDate = (date: string) =>
  date ? new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A'

const close = () => emit('close')

/* ✅ Cancel handlers */
const openCancelDialog = () => {
  cancelReason.value = ''
  showCancelDialog.value = true
}

const closeCancelDialog = () => {
  if (cancelling.value) return
  showCancelDialog.value = false
}

const confirmCancel = async () => {
  if (!props.invoice || cancelling.value) return
  cancelling.value = true
  try {
    await invoiceStore.cancel(props.invoice.id, cancelReason.value.trim() || undefined)
    toastStore.success(`Invoice ${props.invoice.invoice_no} cancelled`)
    showCancelDialog.value = false
    emit('close')
  } catch (err: any) {
    toastStore.error(err.response?.data?.message || 'Failed to cancel invoice')
  } finally {
    cancelling.value = false
  }
}

/* ── Existing handlers ─────────────────────────────────── */

const handleDownload = async () => {
  if (!props.invoice || downloading.value) return
  downloading.value = true
  try {
    const response = await api.get(`/admin/invoices/${props.invoice.id}/download`, {
      responseType: 'blob',
    })
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `invoice-${props.invoice.invoice_no}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    toastStore.success('Invoice downloaded')
  } catch (error) {
    console.error('Download failed:', error)
    toastStore.error('Failed to download invoice')
  } finally {
    downloading.value = false
  }
}

const handlePrint = async () => {
  if (!props.invoice || printing.value) return
  printing.value = true
  try {
    const response = await api.get(`/admin/invoices/${props.invoice.id}/download`, {
      responseType: 'blob',
    })
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const printWindow = window.open(url, '_blank')
    if (printWindow) {
      printWindow.onload = () => printWindow.print()
    }
  } catch (error) {
    console.error('Print failed:', error)
    toastStore.error('Failed to prepare invoice for printing')
  } finally {
    printing.value = false
  }
}
</script>
