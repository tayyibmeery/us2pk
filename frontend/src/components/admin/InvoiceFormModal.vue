<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Invoice' : 'Add Invoice'"
    :subtitle="formData.id ? 'Update the invoice details below.' : 'Fill in the details to add a new invoice.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Invoice No
          </label>
          <input v-model="formData.invoice_no" type="text" placeholder="e.g. INV-001"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Shipment
          </label>
          <select v-model="formData.shipment_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option :value="null">Select Shipment</option>
            <option v-for="shipment in shipments" :key="shipment.id" :value="shipment.id">
              {{ shipment.pcode }} - {{ shipment.description || 'N/A' }}
            </option>
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Date
          </label>
          <input v-model="formData.date" type="date"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Amount Due
          </label>
          <input v-model.number="formData.amount_due" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            COD
          </label>
          <input v-model.number="formData.cod" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            COD Date
          </label>
          <input v-model="formData.cod_date" type="date"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Output Tax
          </label>
          <input v-model.number="formData.output_tax" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
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

const formData = ref<Partial<Invoice>>({
  invoice_no: '',
  shipment_id: null,
  date: '',
  amount_due: 0,
  cod: 0,
  cod_date: '',
  output_tax: 0,
})

const fetchShipments = async () => {
  try {
    await shipmentStore.fetchItems(1, { per_page: 1000 })
    shipments.value = shipmentStore.items
  } catch (_) {
    shipments.value = []
  }
}

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = {
        invoice_no: '',
        shipment_id: null,
        date: '',
        amount_due: 0,
        cod: 0,
        cod_date: '',
        output_tax: 0,
      }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = {
        invoice_no: '',
        shipment_id: null,
        date: '',
        amount_due: 0,
        cod: 0,
        cod_date: '',
        output_tax: 0,
      }
    } else {
      fetchShipments()
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
