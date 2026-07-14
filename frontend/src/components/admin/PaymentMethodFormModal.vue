<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Payment Method' : 'Add Payment Method'"
    :subtitle="formData.id ? 'Update the payment method details below.' : 'Fill in the details to add a new payment method.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Payment Method Name -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Name <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.name" type="text" placeholder="e.g. Credit Card, Bank Transfer, JazzCash"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Account Mapping -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Linked Account <span class="text-xs text-gray-400">(Optional - maps this payment method to a specific
              account)</span>
          </label>
          <select v-model="formData.account_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="accountsLoading">
            <option value="">None (Default Account)</option>
            <option v-for="account in accounts" :key="account.id" :value="account.id">
              {{ account.name }} ({{ account.code || account.id }})
            </option>
          </select>
          <p v-if="accountsLoading" class="text-xs text-gray-400 mt-1">Loading accounts...</p>
          <p class="text-xs text-gray-400 mt-1">
            When this payment method is used, the voucher will debit this account.
          </p>
        </div>

        <!-- Status -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { PaymentMethod } from '@/stores/paymentMethodStore'
import { useAccountStore } from '@/stores/accountStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: PaymentMethod | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<PaymentMethod>): void
}>()

const accountStore = useAccountStore()
const accounts = ref<any[]>([])
const accountsLoading = ref(false)

const formData = ref<Partial<PaymentMethod>>({
  name: '',
  account_id: null,
  status: true,
})

// Fetch accounts for dropdown
const fetchAccounts = async () => {
  accountsLoading.value = true
  try {
    await accountStore.fetchItems(1, { per_page: 1000 })
    accounts.value = accountStore.items
  } catch (error) {
    console.error('Failed to fetch accounts:', error)
  } finally {
    accountsLoading.value = false
  }
}

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = {
        ...newVal,
        status: !!newVal.status,
        account_id: newVal.account_id || null
      }
    } else {
      formData.value = { name: '', account_id: null, status: true }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = { name: '', account_id: null, status: true }
    } else {
      // Fetch accounts when modal opens
      fetchAccounts()
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)

onMounted(() => {
  fetchAccounts()
})
</script>
