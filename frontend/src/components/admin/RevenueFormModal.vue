<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Revenue' : 'Add Revenue'"
    :subtitle="formData.id ? 'Update the revenue details below.' : 'Fill in the details to add a new revenue record.'"
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
            Date
          </label>
          <input v-model="formData.date" type="date"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Type
          </label>
          <input v-model="formData.type" type="text" placeholder="e.g. Sale"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            User
          </label>
          <select v-model="formData.user_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option :value="null">Select User</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.pcode }})
            </option>
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            PCODE
          </label>
          <input v-model="formData.pcode" type="text" placeholder="e.g. LHE1"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Revenue
          </label>
          <input v-model.number="formData.revenue" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Output Tax
          </label>
          <input v-model.number="formData.output_tax" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Paid By
          </label>
          <input v-model="formData.bought_by" type="text" placeholder="e.g. Customer"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Vendor Payment
          </label>
          <input v-model.number="formData.vendor_payment" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { Revenue } from '@/stores/revenueStore'
import { useUserStore } from '@/stores/userStore' // assuming you have userStore

const props = defineProps<{
  isOpen: boolean
  initialData?: Revenue | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<Revenue>): void
}>()

const userStore = useUserStore()
const users = ref<any[]>([])

const formData = ref<Partial<Revenue>>({
  invoice_no: '',
  date: '',
  type: '',
  user_id: null,
  pcode: '',
  revenue: 0,
  output_tax: 0,
  bought_by: '',
  vendor_payment: null,
})

const fetchUsers = async () => {
  try {
    await userStore.fetchItems(1, { per_page: 1000 })
    users.value = userStore.items
  } catch (_) {
    users.value = []
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
        date: '',
        type: '',
        user_id: null,
        pcode: '',
        revenue: 0,
        output_tax: 0,
        bought_by: '',
        vendor_payment: null,
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
        date: '',
        type: '',
        user_id: null,
        pcode: '',
        revenue: 0,
        output_tax: 0,
        bought_by: '',
        vendor_payment: null,
      }
    } else {
      fetchUsers()
    }
  }
)

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
