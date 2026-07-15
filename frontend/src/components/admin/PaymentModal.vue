<template>
  <Modal :isOpen="isOpen" @close="close" size="md">
    <template #body>
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Record Payment</h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="debtor" class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
          <p class="text-sm"><span class="font-medium">Invoice:</span> {{ debtor.invoice_no }}</p>
          <p class="text-sm"><span class="font-medium">Customer:</span> {{ debtor.customer_name }}</p>
          <p class="text-sm"><span class="font-medium">Balance:</span> <span
              class="text-red-600">{{ formatCurrency(debtor.balance) }}</span></p>
        </div>

        <form @submit.prevent="submitPayment" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Amount <span class="text-red-500">*</span>
            </label>
            <input v-model.number="form.amount" type="number" step="0.01" min="0.01" :max="debtor?.balance"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              placeholder="0.00" required />
            <p class="text-xs text-gray-500 mt-1">Max: {{ formatCurrency(debtor?.balance || 0) }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Payment Date <span class="text-red-500">*</span>
            </label>
            <input v-model="form.payment_date" type="date"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              required />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Payment Method <span class="text-red-500">*</span>
            </label>
            <select v-model="form.payment_method"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              required>
              <option value="">Select Method</option>
              <option value="Cash">Cash</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Credit Card">Credit Card</option>
              <option value="JazzCash">JazzCash</option>
              <option value="EasyPaisa">EasyPaisa</option>
              <option value="ABL">ABL</option>
              <option value="SCB">SCB</option>
              <option value="BAFL">BAFL</option>
              <option value="FAISAL">FAISAL</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Reference Number
            </label>
            <input v-model="form.reference_number" type="text"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              placeholder="e.g. TXN12345" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Notes
            </label>
            <textarea v-model="form.notes" rows="2"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              placeholder="Optional notes"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="close"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
              Cancel
            </button>
            <button type="submit" :disabled="submitting"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="submitting">Processing...</span>
              <span v-else>Record Payment</span>
            </button>
          </div>
        </form>
      </div>
    </template>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import Modal from '@/components/Modal.vue'
import api from '@/services/api'

const props = defineProps<{
  isOpen: boolean
  debtor?: any | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'payment-recorded'): void
}>()

const form = ref({
  amount: null as number | null,
  payment_date: new Date().toISOString().split('T')[0],
  payment_method: '',
  reference_number: '',
  notes: '',
})

const submitting = ref(false)

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value || 0)
}

const close = () => {
  form.value = {
    amount: null,
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: '',
    reference_number: '',
    notes: '',
  }
  emit('close')
}

const submitPayment = async () => {
  if (!props.debtor || submitting.value) return

  submitting.value = true
  try {
    await api.post(`/admin/debtors/${props.debtor.id}/payment`, form.value)
    emit('payment-recorded')
    close()
  } catch (error: any) {
    console.error('Payment failed:', error)
    alert(error.response?.data?.message || 'Failed to record payment')
  } finally {
    submitting.value = false
  }
}

watch(() => props.isOpen, (open) => {
  if (open) {
    form.value.payment_date = new Date().toISOString().split('T')[0]
  }
})
</script>
