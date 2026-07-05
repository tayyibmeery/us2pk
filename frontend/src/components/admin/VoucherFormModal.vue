<template>
  <FormModal :isOpen="isOpen" :title="initialData ? 'Edit Voucher' : 'Create Voucher'"
    :subtitle="initialData ? 'Update voucher details.' : 'Enter voucher entries.'"
    :saveLabel="initialData ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="space-y-4">
        <!-- Date -->
        <div>
          <label class="block text-sm font-medium">Date</label>
          <input v-model="form.date" type="date" class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <!-- Description -->
        <div>
          <label class="block text-sm font-medium">Description</label>
          <input v-model="form.description" class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>
        <!-- Entries Table -->
        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-medium">Entries</label>
            <button type="button" @click="addRow" class="text-sm text-brand-600 hover:underline">+ Add Row</button>
          </div>
          <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th class="px-2 py-1 text-left">Account</th>
                <th class="px-2 py-1 text-right">Debit</th>
                <th class="px-2 py-1 text-right">Credit</th>
                <th class="px-2 py-1 text-left">Description</th>
                <th class="px-2 py-1 text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(entry, idx) in form.entries" :key="idx" class="border-b">
                <td class="px-2 py-1">
                  <select v-model="entry.account_id" class="w-full rounded border p-1 dark:bg-gray-800">
                    <option v-for="acc in accounts" :key="acc.id" :value="acc.id">{{ acc.name }}</option>
                  </select>
                </td>
                <td class="px-2 py-1">
                  <input v-model.number="entry.debit" type="number" step="0.01"
                    class="w-24 rounded border p-1 text-right dark:bg-gray-800" />
                </td>
                <td class="px-2 py-1">
                  <input v-model.number="entry.credit" type="number" step="0.01"
                    class="w-24 rounded border p-1 text-right dark:bg-gray-800" />
                </td>
                <td class="px-2 py-1">
                  <input v-model="entry.description" class="w-full rounded border p-1 dark:bg-gray-800" />
                </td>
                <td class="px-2 py-1 text-center">
                  <button type="button" @click="removeRow(idx)" class="text-red-500 hover:underline">×</button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="font-bold">
                <td class="px-2 py-1">Total</td>
                <td class="px-2 py-1 text-right">{{ totalDebit.toFixed(2) }}</td>
                <td class="px-2 py-1 text-right">{{ totalCredit.toFixed(2) }}</td>
                <td colspan="2"></td>
              </tr>
              <tr v-if="!isBalanced" class="text-red-500 text-sm">
                <td colspan="5" class="text-center">Debit and Credit totals must match.</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import { useAccountStore } from '@/stores/accountStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: any
}>()

const emit = defineEmits(['close', 'save'])

const accountStore = useAccountStore()
const accounts = ref<any[]>([])

const defaultEntry = () => ({ account_id: null, debit: 0, credit: 0, description: '' })

const form = reactive({
  date: new Date().toISOString().split('T')[0],
  description: '',
  entries: [defaultEntry(), defaultEntry()],
})

const totalDebit = computed(() => form.entries.reduce((sum, e) => sum + (e.debit || 0), 0))
const totalCredit = computed(() => form.entries.reduce((sum, e) => sum + (e.credit || 0), 0))
const isBalanced = computed(() => totalDebit.value === totalCredit.value)

const addRow = () => form.entries.push(defaultEntry())
const removeRow = (idx: number) => {
  if (form.entries.length > 2) form.entries.splice(idx, 1)
}

const resetForm = () => {
  form.date = new Date().toISOString().split('T')[0]
  form.description = ''
  form.entries = [defaultEntry(), defaultEntry()]
}

watch(() => props.isOpen, (open) => {
  if (!open) return
  if (props.initialData) {
    // Load existing data
    form.date = props.initialData.date || ''
    form.description = props.initialData.description || ''
    form.entries = props.initialData.details?.map((d: any) => ({
      account_id: d.account_id,
      debit: d.debit,
      credit: d.credit,
      description: d.description,
    })) || [defaultEntry(), defaultEntry()]
  } else {
    resetForm()
  }
})

onMounted(async () => {
  await accountStore.fetchItems(1, { perPage: 1000 })
  accounts.value = accountStore.items
})

const close = () => emit('close')
const save = () => {
  if (!isBalanced.value) {
    alert('Debit and Credit totals must match.')
    return
  }
  const payload = {
    date: form.date,
    description: form.description,
    entries: form.entries.map(e => ({
      account_id: e.account_id,
      debit: e.debit || 0,
      credit: e.credit || 0,
      description: e.description,
    })),
  }
  emit('save', payload)
  close()
}
</script>
