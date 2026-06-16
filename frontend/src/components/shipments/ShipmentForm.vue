<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
      <h3 class="text-lg font-semibold mb-4">{{ isEdit ? 'Edit Shipment' : 'Create Shipment' }}</h3>
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <!-- User selection (admin only) -->
          <div>
            <label class="block text-sm font-medium">User</label>
            <select v-model="form.user_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required>
              <option value="">Select User</option>
              <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium">Description</label>
            <input v-model="form.description" type="text" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">Weight</label>
              <input v-model="form.weight" type="number" step="0.01" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required />
            </div>
            <div>
              <label class="block text-sm font-medium">Weight Unit</label>
              <select v-model="form.weight_unit" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required>
                <option value="ounce">Ounce</option>
                <option value="gram">Gram</option>
                <option value="kg">Kilogram</option>
                <option value="pound">Pound</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium">Seller Tracker ID</label>
            <input v-model="form.seller_tracker_id" type="text" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block text-sm font-medium">Purchase Date</label>
            <input v-model="form.purchase_date" type="date" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block text-sm font-medium">Status</label>
            <select v-model="form.status" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required>
              <option value="pending">Pending</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium">Item Value (PKR)</label>
              <input v-model="form.item_value_pkr" type="number" step="0.01" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required />
            </div>
            <div>
              <label class="block text-sm font-medium">Company Charges (PKR)</label>
              <input v-model="form.company_charges" type="number" step="0.01" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium">Paid By</label>
            <select v-model="form.paid_by" class="w-full border rounded px-3 py-2 dark:bg-gray-700" required>
              <option value="By Company">By Company</option>
              <option value="By Customer">By Customer</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium">Payment Method</label>
            <input v-model="form.payment_method" type="text" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block text-sm font-medium">Receivable COD</label>
            <input v-model="form.receivable_cod" type="number" step="0.01" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block text-sm font-medium">Local Delivery By</label>
            <input v-model="form.local_delivery_by" type="text" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block text-sm font-medium">BlueEx Charges</label>
            <input v-model="form.blueex_charges" type="number" step="0.01" class="w-full border rounded px-3 py-2 dark:bg-gray-700" />
          </div>

          <div>
            <label class="block text-sm font-medium">Comments</label>
            <textarea v-model="form.comments" rows="2" class="w-full border rounded px-3 py-2 dark:bg-gray-700"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
          <button type="button" @click="close" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancel</button>
          <button type="submit" :disabled="loading" class="px-4 py-2 bg-brand-500 text-white rounded hover:bg-brand-600 disabled:opacity-50">
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import api from '@/services/api'

const props = defineProps<{
  isOpen: boolean
  shipment?: any | null
}>()

const emit = defineEmits(['close', 'saved'])

const form = ref({
  user_id: '',
  description: '',
  weight: '',
  weight_unit: 'kg',
  seller_tracker_id: '',
  purchase_date: '',
  status: 'pending',
  item_value_pkr: '',
  company_charges: '',
  paid_by: 'By Customer',
  payment_method: '',
  receivable_cod: '',
  local_delivery_by: '',
  blueex_charges: '',
  comments: '',
})

const users = ref<any[]>([])
const loading = ref(false)
const isEdit = computed(() => !!props.shipment?.id)

const resetForm = () => {
  form.value = {
    user_id: '',
    description: '',
    weight: '',
    weight_unit: 'kg',
    seller_tracker_id: '',
    purchase_date: '',
    status: 'pending',
    item_value_pkr: '',
    company_charges: '',
    paid_by: 'By Customer',
    payment_method: '',
    receivable_cod: '',
    local_delivery_by: '',
    blueex_charges: '',
    comments: '',
  }
}

watch(() => props.shipment, (newVal) => {
  if (newVal) {
    form.value = { ...newVal }
  } else {
    resetForm()
  }
}, { immediate: true })

onMounted(async () => {
  try {
    const res = await api.get('/admin/users')
    users.value = res.data
  } catch (e) {
    console.error('Failed to load users', e)
  }
})

const close = () => emit('close')

const submit = async () => {
  loading.value = true
  try {
    const payload = { ...form.value }
    // Convert string numbers to float
    payload.weight = parseFloat(payload.weight) || 0
    payload.item_value_pkr = parseFloat(payload.item_value_pkr) || 0
    payload.company_charges = parseFloat(payload.company_charges) || 0
    payload.receivable_cod = parseFloat(payload.receivable_cod) || 0
    payload.blueex_charges = parseFloat(payload.blueex_charges) || 0

    if (isEdit.value) {
      await api.put(`/admin/shipments/${props.shipment.id}`, payload)
    } else {
      await api.post('/admin/shipments', payload)
    }
    emit('saved')
    close()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to save shipment')
  } finally {
    loading.value = false
  }
}
</script>
