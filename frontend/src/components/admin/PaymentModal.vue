<template>
  <Modal :isOpen="isOpen" @close="close">
    <template #body>
      <div
        class="no-scrollbar mt-18 relative w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8">
        <button @click="close"
          class="transition-color absolute right-4 top-4 z-999 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
          <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
              fill="currentColor" />
          </svg>
        </button>

        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Record Payment</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
          Invoice: <span class="font-medium">{{ debtor?.invoice_no }}</span>
        </p>

        <form @submit.prevent="submitPayment" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Amount (PKR)
              </label>
              <input v-model.number="payment.amount" type="number" step="0.01" min="0" required
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Payment Date
              </label>
              <input v-model="payment.date" type="date" required
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Payment Method
            </label>
            <select v-model="payment.method" required
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">Select Method</option>
              <option value="ABL">ABL</option>
              <option value="SCB">SCB</option>
              <option value="BAFL">BAFL</option>
              <option value="FAISAL">FAISAL</option>
              <option value="CASH">CASH</option>
              <option value="JAZZCASH">JAZZCASH</option>
              <option value="EASYPAISA">EASYPAISA</option>
              <option value="Bank Transfer">Bank Transfer</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Reference No.
            </label>
            <input v-model="payment.reference" type="text" placeholder="e.g. TXN12345"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Notes
            </label>
            <textarea v-model="payment.notes" rows="2" placeholder="Optional notes"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
          </div>

          <!-- Summary -->
          <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div>
                <span class="text-gray-500">Total Payable:</span>
                <span class="font-medium ml-2">{{ formatCurrency(debtor?.amount_due || 0) }}</span>
              </div>
              <div>
                <span class="text-gray-500">Remaining Balance:</span>
                <span class="font-medium ml-2 text-red-600">{{ formatCurrency(debtor?.balance || 0) }}</span>
              </div>
            </div>
          </div>

          <div class="flex gap-3 pt-2">
            <button type="button" @click="close"
              class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
              Cancel
            </button>
            <button type="submit" :disabled="submitting"
              class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
              {{ submitting ? 'Processing...' : 'Add Payment' }}
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

const submitting = ref(false)

const payment = ref({
  amount: 0,
  date: new Date().toISOString().split('T')[0],
  method: '',
  reference: '',
  notes: ''
})

watch(() => props.debtor, (newVal) => {
  if (newVal) {
    payment.value.amount = newVal.balance || 0
  }
})

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value || 0)
}

const close = () => {
  payment.value = {
    amount: 0,
    date: new Date().toISOString().split('T')[0],
    method: '',
    reference: '',
    notes: ''
  }
  emit('close')
}

const submitPayment = async () => {
  if (!payment.value.amount || payment.value.amount <= 0) {
    alert('Please enter a valid amount')
    return
  }
  if (!payment.value.method) {
    alert('Please select a payment method')
    return
  }

  submitting.value = true
  try {
    await api.post(`/admin/debtors/${props.debtor?.id}/payment`, payment.value)
    emit('payment-recorded')
  } catch (error) {
    console.error('Failed to record payment:', error)
    alert('Failed to record payment')
  } finally {
    submitting.value = false
  }
}
</script>
