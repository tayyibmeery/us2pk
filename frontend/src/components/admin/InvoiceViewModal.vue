<template>
  <Modal :isOpen="isOpen" @close="close" size="xl">
    <template #body>
      <div class="relative p-6 lg:p-10">
        <!-- Close Button -->
        <button @click="close"
          class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z" />
          </svg>
        </button>

        <div v-if="invoice" id="invoice-print-area" class="print-area">
          <!-- Header with Logo -->
          <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <div class="flex flex-wrap justify-between items-start gap-4">
              <div class="flex items-center gap-4">
                <!-- Logo -->
                <div
                  class="w-16 h-16 rounded-xl bg-brand-500 flex items-center justify-center text-white font-bold text-xl shadow-sm">
                  <span>US</span>
                </div>
                <div>
                  <h1 class="text-3xl font-bold text-gray-800 dark:text-white">INVOICE</h1>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">#{{ invoice.invoice_no }}</p>
                </div>
              </div>
              <div class="text-right">
                <span :class="{
                  'inline-flex px-3 py-1 rounded-full text-sm font-medium': true,
                  'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': invoice.status === 'paid',
                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': invoice.status === 'pending',
                  'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': invoice.status === 'overdue',
                  'bg-gray-100 text-gray-700 dark:bg-gray-800/50 dark:text-gray-400': invoice.status === 'cancelled',
                }">
                  {{ invoice.status }}
                </span>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ formatDate(invoice.date) }}</p>
              </div>
            </div>
          </div>

          <!-- Company & Customer Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">From</h4>
              <div class="mt-2">
                <p class="font-medium text-gray-800 dark:text-white text-lg">US2PK Logistics</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">123 Business Street</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lahore, Pakistan</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">info@us2pk.com</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">+92-300-1234567</p>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bill To</h4>
              <div class="mt-2">
                <p class="font-medium text-gray-800 dark:text-white text-lg">{{ invoice.shipment?.user?.name || 'N/A' }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ invoice.shipment?.user?.email || 'N/A' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Shipment:
                  {{ invoice.shipment?.shipment_code || 'N/A' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">City:
                  {{ invoice.shipment?.user?.city?.city_name || 'N/A' }}</p>
              </div>
            </div>
          </div>

          <!-- Invoice Details -->
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                  <th class="text-left py-3 px-2 font-semibold text-gray-600 dark:text-gray-400">Description</th>
                  <th class="text-right py-3 px-2 font-semibold text-gray-600 dark:text-gray-400">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                  <td class="py-3 px-2 text-gray-800 dark:text-white">
                    <span class="font-medium">Shipment Charges</span>
                    <span class="text-xs text-gray-400 ml-2">({{ invoice.shipment?.shipment_code || 'N/A' }})</span>
                  </td>
                  <td class="py-3 px-2 text-right font-mono text-gray-800 dark:text-white">
                    {{ formatCurrency(invoice.amount_due || 0) }}
                  </td>
                </tr>
                <tr v-if="invoice.cod > 0" class="border-b border-gray-100 dark:border-gray-700/50">
                  <td class="py-3 px-2 text-gray-800 dark:text-white">Cash on Delivery (COD)</td>
                  <td class="py-3 px-2 text-right font-mono text-gray-800 dark:text-white">
                    {{ formatCurrency(invoice.cod || 0) }}
                  </td>
                </tr>
                <tr v-if="invoice.output_tax && invoice.output_tax > 0"
                  class="border-b border-gray-100 dark:border-gray-700/50">
                  <td class="py-3 px-2 text-gray-800 dark:text-white">Output Tax</td>
                  <td class="py-3 px-2 text-right font-mono text-gray-800 dark:text-white">
                    {{ formatCurrency(invoice.output_tax || 0) }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50">
                  <td class="py-4 px-2 text-lg font-bold text-gray-800 dark:text-white">Total Amount</td>
                  <td class="py-4 px-2 text-right text-lg font-bold text-emerald-600 dark:text-emerald-400">
                    {{ formatCurrency((invoice.amount_due || 0) + (invoice.output_tax || 0)) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Shipment Details -->
          <div v-if="invoice.shipment"
            class="mt-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Shipment
              Details</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
              <div>
                <span class="text-gray-500">Shipment Code:</span>
                <span
                  class="font-medium ml-1 text-gray-800 dark:text-white/90">{{ invoice.shipment.shipment_code || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Weight:</span>
                <span class="font-medium ml-1 text-gray-800 dark:text-white/90">{{ invoice.shipment.weight || 'N/A' }}
                  kg</span>
              </div>
              <div>
                <span class="text-gray-500">Description:</span>
                <span
                  class="font-medium ml-1 text-gray-800 dark:text-white/90">{{ invoice.shipment.description || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Bought By:</span>
                <span
                  class="font-medium ml-1 text-gray-800 dark:text-white/90">{{ invoice.shipment.bought_by || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="invoice.notes"
            class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/10 rounded-lg border border-amber-200 dark:border-amber-800/30">
            <p class="text-sm text-amber-700 dark:text-amber-400">
              <span class="font-semibold">Note:</span> {{ invoice.notes }}
            </p>
          </div>

          <!-- Footer -->
          <div
            class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-center gap-4">
            <p class="text-xs text-gray-400">Generated on {{ new Date().toLocaleString() }}</p>
            <div class="flex gap-2">
              <button @click="handleDownload" :disabled="downloading"
                class="download-btn flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg v-if="!downloading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="7 10 12 15 17 10" />
                  <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                <svg v-else class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                {{ downloading ? 'Downloading...' : 'Download PDF' }}
              </button>
              <button @click="handlePrint"
                class="flex items-center gap-1.5 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium transition">
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
import api from '@/services/api'
import { useToastStore } from '@/stores/toastStore'

const props = defineProps<{
  isOpen: boolean
  invoice?: any | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const toastStore = useToastStore()
const downloading = ref(false)

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value || 0)
}

const formatDate = (date: string): string => {
  return date ? new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }) : 'N/A'
}

const close = () => emit('close')

// ✅ Download Invoice PDF
const handleDownload = async () => {
  if (!props.invoice || downloading.value) return

  downloading.value = true
  try {
    toastStore.info(`Downloading invoice ${props.invoice.invoice_no}...`)

    const response = await api.get(`/admin/invoices/${props.invoice.id}/download`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `invoice-${props.invoice.invoice_no}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)

    toastStore.success(`Invoice ${props.invoice.invoice_no} downloaded successfully`)
  } catch (error: any) {
    console.error('Download failed:', error)
    toastStore.error(error?.response?.data?.message || 'Failed to download invoice')
  } finally {
    downloading.value = false
  }
}

// ✅ Print Invoice
const handlePrint = async () => {
  if (!props.invoice) return

  try {
    toastStore.info(`Preparing invoice ${props.invoice.invoice_no} for printing...`)

    const printWindow = window.open('', '_blank', 'width=900,height=700')
    if (!printWindow) {
      toastStore.error('Please allow popups to print the invoice')
      return
    }

    const response = await api.get(`/admin/invoices/${props.invoice.id}/print`)
    const data = response.data

    const printHtml = buildPrintHTML(data)

    printWindow.document.write(printHtml)
    printWindow.document.close()

    toastStore.success(`Invoice ${props.invoice.invoice_no} sent to printer`)
  } catch (error: any) {
    console.error('Print failed:', error)
    toastStore.error(error?.response?.data?.message || 'Failed to print invoice')
  }
}

function buildPrintHTML(data: any): string {
  const invoice = data.invoice
  const company = data.company

  return `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Invoice ${invoice.invoice_no}</title>
      <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 40px; background: #fff; color: #333; }
        .invoice-container { max-width: 900px; margin: 0 auto; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-start; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .logo { width: 50px; height: 50px; background: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 18px; }
        .header-left h1 { font-size: 28px; color: #1a1a2e; }
        .header-left .invoice-no { color: #666; font-size: 14px; margin-top: 4px; }
        .header-right { text-align: right; }
        .status-badge { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #4b5563; }
        .date-text { color: #666; font-size: 14px; margin-top: 6px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; }
        .info-grid h4 { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-bottom: 8px; }
        .info-grid .name { font-weight: 600; font-size: 16px; color: #1a1a2e; }
        .info-grid p { font-size: 14px; line-height: 1.8; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f1f5f9; padding: 12px 16px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #4b5563; border-bottom: 2px solid #e5e7eb; }
        td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; }
        td:last-child, th:last-child { text-align: right; }
        .total-row { background: #f8fafc; font-weight: 700; }
        .total-row td { border-top: 2px solid #e5e7eb; padding: 16px; font-size: 16px; }
        .total-row .amount { color: #2563eb; font-size: 20px; }
        .shipment-details { margin: 20px 0; padding: 16px 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e5e7eb; }
        .shipment-details h4 { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-bottom: 10px; }
        .shipment-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px; }
        .shipment-grid .label { font-size: 12px; color: #666; }
        .shipment-grid .value { font-size: 14px; color: #333; font-weight: 500; }
        .notes { margin: 20px 0; padding: 12px 16px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b; }
        .notes p { font-size: 13px; color: #92400e; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #666; }
        .footer .thankyou { font-size: 14px; font-weight: 500; color: #1a1a2e; }
        @media print { body { padding: 20px; } }
      </style>
    </head>
    <body>
      <div class="invoice-container">
        <div class="header">
          <div class="header-left">
            <div class="logo">US</div>
            <div>
              <h1>INVOICE</h1>
              <div class="invoice-no">#${invoice.invoice_no}</div>
            </div>
          </div>
          <div class="header-right">
            <span class="status-badge status-${invoice.status}">${invoice.status}</span>
            <div class="date-text">Date: ${new Date(invoice.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
          </div>
        </div>

        <div class="info-grid">
          <div>
            <h4>From</h4>
            <p>
              <span class="name">${company.name}</span><br>
              ${company.address}<br>
              Phone: ${company.phone}<br>
              Email: ${company.email}
            </p>
          </div>
          <div>
            <h4>Bill To</h4>
            <p>
              <span class="name">${invoice.shipment?.user?.name || 'N/A'}</span><br>
              Email: ${invoice.shipment?.user?.email || 'N/A'}<br>
              Shipment: ${invoice.shipment?.shipment_code || 'N/A'}<br>
              City: ${invoice.shipment?.user?.city?.city_name || 'N/A'}
            </p>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Shipment Charges <span style="color:#999;font-size:12px;">(${invoice.shipment?.shipment_code || 'N/A'})</span></td>
              <td>${Number(invoice.amount_due).toFixed(2)} PKR</td>
            </tr>
            ${invoice.cod > 0 ? `<tr><td>Cash on Delivery (COD)</td><td>${Number(invoice.cod).toFixed(2)} PKR</td></tr>` : ''}
            ${invoice.output_tax > 0 ? `<tr><td>Output Tax</td><td>${Number(invoice.output_tax).toFixed(2)} PKR</td></tr>` : ''}
          </tbody>
          <tfoot>
            <tr class="total-row">
              <td>Total Amount</td>
              <td class="amount">${(Number(invoice.amount_due) + Number(invoice.output_tax)).toFixed(2)} PKR</td>
            </tr>
          </tfoot>
        </table>

        ${invoice.shipment ? `
        <div class="shipment-details">
          <h4>Shipment Details</h4>
          <div class="shipment-grid">
            <div>
              <div class="label">Shipment Code</div>
              <div class="value">${invoice.shipment.shipment_code || 'N/A'}</div>
            </div>
            <div>
              <div class="label">Weight</div>
              <div class="value">${invoice.shipment.weight || 'N/A'} kg</div>
            </div>
            <div>
              <div class="label">Description</div>
              <div class="value">${invoice.shipment.description || 'N/A'}</div>
            </div>
            <div>
              <div class="label">Bought By</div>
              <div class="value">${invoice.shipment.bought_by || 'N/A'}</div>
            </div>
          </div>
        </div>
        ` : ''}

        ${invoice.notes ? `
        <div class="notes">
          <p><strong>Note:</strong> ${invoice.notes}</p>
        </div>
        ` : ''}

        <div class="footer">
          <div>
            <div class="thankyou">Thank you for your business!</div>
            <div style="margin-top:4px;">Generated on ${new Date().toLocaleString()}</div>
          </div>
          <div style="text-align:right;">
            <div style="color:#2563eb;font-weight:500;">Payment Due: ${invoice.status === 'pending' ? 'Immediate' : 'Paid'}</div>
            ${invoice.status === 'pending' ? '<div style="color:#dc2626;font-size:12px;margin-top:4px;">Please pay within 7 days</div>' : ''}
          </div>
        </div>
      </div>
    </body>
    </html>
  `
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
