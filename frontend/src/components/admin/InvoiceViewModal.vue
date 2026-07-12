<template>
  <Modal :isOpen="isOpen" @close="close">
    <template #body>
      <div
        class="no-scrollbar mt-18 relative w-full max-w-4xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
        <!-- Close button -->
        <button @click="close"
          class="transition-color absolute right-4 top-4 z-999 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="currentColor" />
          </svg>
        </button>

        <div v-if="invoice" class="print-area" id="invoice-print-area">
          <!-- Invoice Header -->
          <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">INVOICE</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">#{{ invoice.invoice_no }}</p>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Date: {{ formatDate(invoice.date) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status: <span class="text-green-600">Paid</span></p>
              </div>
            </div>
          </div>

          <!-- Company & Customer Info -->
          <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
              <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">From</h4>
              <div class="mt-2">
                <p class="font-medium text-gray-800 dark:text-white">US2PK Logistics</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">123 Business Street</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lahore, Pakistan</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email: info@us2pk.com</p>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bill To</h4>
              <div class="mt-2">
                <p class="font-medium text-gray-800 dark:text-white">{{ invoice.shipment?.user?.name || 'N/A' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ invoice.shipment?.user?.email || 'N/A' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Shipment:
                  {{ invoice.shipment?.shipment_code || invoice.shipment?.pcode || 'N/A' }}</p>
              </div>
            </div>
          </div>

          <!-- Invoice Details -->
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <th class="text-left py-3 px-2 font-semibold text-gray-600 dark:text-gray-400">Description</th>
                  <th class="text-right py-3 px-2 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                  <td class="py-3 px-2 text-gray-800 dark:text-white">Shipment Charges</td>
                  <td class="py-3 px-2 text-right">{{ formatCurrency(invoice.amount_due || 0) }}</td>
                </tr>
                <tr v-if="invoice.cod > 0" class="border-b border-gray-100 dark:border-gray-700/50">
                  <td class="py-3 px-2 text-gray-800 dark:text-white">COD</td>
                  <td class="py-3 px-2 text-right">{{ formatCurrency(invoice.cod || 0) }}</td>
                </tr>
                <tr v-if="invoice.output_tax > 0">
                  <td class="py-3 px-2 text-gray-800 dark:text-white">Output Tax</td>
                  <td class="py-3 px-2 text-right">{{ formatCurrency(invoice.output_tax || 0) }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                  <td class="py-4 px-2 font-bold text-gray-800 dark:text-white">Total</td>
                  <td class="py-4 px-2 text-right font-bold text-green-600">
                    {{ formatCurrency((invoice.amount_due || 0) + (invoice.output_tax || 0)) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Shipment Details -->
          <div v-if="invoice.shipment" class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Shipment
              Details</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
              <div>
                <span class="text-gray-500">Shipment Code:</span>
                <span
                  class="font-medium ml-1">{{ invoice.shipment.shipment_code || invoice.shipment.pcode || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Weight:</span>
                <span class="font-medium ml-1">{{ invoice.shipment.weight || 'N/A' }} kg</span>
              </div>
              <div>
                <span class="text-gray-500">Description:</span>
                <span class="font-medium ml-1">{{ invoice.shipment.description || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Status:</span>
                <span class="font-medium ml-1">{{ invoice.shipment.status || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <p class="text-xs text-gray-400">Generated on {{ new Date().toLocaleString() }}</p>
            <div class="flex gap-2">
              <button @click="handleDownload"
                class="flex items-center gap-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="7 10 12 15 17 10" />
                  <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Download PDF
              </button>
              <button @click="handlePrint"
                class="flex items-center gap-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 6 2 18 2 18 9" />
                  <path d="M18 9H6" />
                  <rect x="6" y="13" width="12" height="8" />
                  <line x1="9" y1="13" x2="9" y2="17" />
                </svg>
                Print
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Modal from '@/components/Modal.vue'

const props = defineProps<{
  isOpen: boolean
  invoice?: any | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'download', invoice: any): void
  (e: 'print', invoice: any): void
}>()

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value)
}

const formatDate = (date: string): string => {
  return date ? new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }) : 'N/A'
}

const close = () => emit('close')

const handleDownload = () => {
  if (props.invoice) {
    emit('download', props.invoice)
  }
}

const handlePrint = () => {
  if (props.invoice) {
    // Print the invoice
    const printContents = document.getElementById('invoice-print-area')
    if (printContents) {
      const originalContents = document.body.innerHTML
      document.body.innerHTML = printContents.innerHTML
      window.print()
      document.body.innerHTML = originalContents
      window.location.reload()
    }
    emit('print', props.invoice)
  }
}
</script>

<style scoped>
@media print {
  .print-area {
    padding: 20px;
  }

  .no-print {
    display: none !important;
  }
}
</style>
