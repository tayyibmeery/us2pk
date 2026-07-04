<template>
  <div class="mt-6">
    <h3 class="text-lg font-semibold mb-3">Payment History</h3>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Payable</p>
        <p class="text-xl font-bold text-gray-800 dark:text-white/90">{{ totalPayable.toFixed(2) }} PKR</p>
      </div>
      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Received</p>
        <p class="text-xl font-bold text-gray-800 dark:text-white/90">{{ totalReceived.toFixed(2) }} PKR</p>
      </div>
      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Remaining</p>
        <p class="text-xl font-bold"
          :class="remaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
          {{ remaining > 0 ? remaining.toFixed(2) + ' PKR' : '—' }}
        </p>
      </div>
      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
        <p class="text-xl font-bold">
          <span v-if="remaining <= 0" class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Cleared
          </span>
          <span v-else class="text-amber-600 dark:text-amber-400">Pending</span>
        </p>
      </div>
    </div>

    <!-- Add Payment Form -->
    <div class="mb-6 p-5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800/30 shadow-sm">
      <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Record New Payment</h4>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Amount -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (PKR)</label>
          <input v-model.number="newPayment.amount" type="number" step="0.01" placeholder="0.00"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
        </div>
        <!-- Payment Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.new_payment_date = el }" v-model="newPayment.payment_date"
              type="text"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="Select date" readonly />
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                  clip-rule="evenodd" />
              </svg>
            </span>
          </div>
        </div>
        <!-- Payment Method -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
          <select v-model="newPayment.payment_method"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            <option value="">Select Method</option>
            <option v-for="method in paymentMethods" :key="method.id" :value="method.name">
              {{ method.name }}
            </option>
          </select>
        </div>
        <!-- Reference Number -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference No.</label>
          <input v-model="newPayment.reference_number" placeholder="e.g. TXN12345"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
        </div>
        <!-- Notes (full width) -->
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
          <textarea v-model="newPayment.notes" rows="2" placeholder="Optional notes"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"></textarea>
        </div>
      </div>
      <button @click="addPayment" :disabled="submitting || !newPayment.amount || !newPayment.payment_date"
        class="mt-4 bg-brand-500 text-white px-5 py-2 rounded-lg hover:bg-brand-600 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium">
        <span v-if="submitting">Saving…</span>
        <span v-else>Add Payment</span>
      </button>
    </div>

    <!-- Payment Table -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/30">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Date</th>
            <th
              class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Amount</th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Method</th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Reference</th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Notes</th>
            <th
              class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments" :key="payment.id"
            class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ formatDate(payment.payment_date) }}</td>
            <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">
              {{ Number(payment.amount).toFixed(2) }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ payment.payment_method || '—' }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ payment.reference_number || '—' }}</td>
            <td class="px-4 py-3 max-w-xs truncate text-gray-600 dark:text-gray-400" :title="payment.notes">
              {{ payment.notes || '—' }}</td>
            <td class="px-4 py-3 text-center">
              <button @click="editPayment(payment)"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-3 transition">Edit</button>
              <button @click="deletePayment(payment.id)"
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition">Delete</button>
            </td>
          </tr>
          <tr v-if="!payments.length && !store.loading">
            <td colspan="6" class="py-6 text-center text-gray-400 dark:text-gray-500">No payments recorded yet.</td>
          </tr>
          <tr v-if="store.loading">
            <td colspan="6" class="py-6 text-center text-gray-400 dark:text-gray-500">Loading payments…</td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50 dark:bg-gray-800/80 border-t border-gray-200 dark:border-gray-700">
          <tr>
            <td class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300" colspan="1">Total Received:
            </td>
            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white/90">
              {{ totalReceived.toFixed(2) }}</td>
            <td colspan="4"></td>
          </tr>
          <tr class="border-t border-gray-200 dark:border-gray-700">
            <td class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300" colspan="1">COD Amount:</td>
            <td class="px-4 py-3 text-right font-bold"
              :class="remaining <= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
              {{ remaining <= 0 ? '✅ Cleared' : remaining.toFixed(2) + ' PKR' }}
            </td>
            <td colspan="4"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Edit Modal -->
    <div v-if="editing" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Edit Payment</h3>
          <button @click="editing = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (PKR)</label>
            <input v-model.number="editForm.amount" type="number" step="0.01" placeholder="0.00"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
            <div class="relative">
              <input :ref="el => { if (el) datePickerRefs.edit_payment_date = el }" v-model="editForm.payment_date"
                type="text"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                placeholder="Select date" readonly />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd" />
                </svg>
              </span>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
            <select v-model="editForm.payment_method"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
              <option value="">Select Method</option>
              <option v-for="method in paymentMethods" :key="method.id" :value="method.name">{{ method.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference No.</label>
            <input v-model="editForm.reference_number" placeholder="e.g. TXN12345"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
            <textarea v-model="editForm.notes" rows="2" placeholder="Optional notes"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent"></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button @click="editing = false"
              class="border px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
            <button @click="updatePayment"
              class="bg-brand-500 text-white px-4 py-2 rounded-lg hover:bg-brand-600 transition">Update</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useShipmentPaymentStore } from '@/stores/shipmentPaymentStore';
import { usePaymentMethodStore } from '@/stores/paymentMethodStore';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';

const props = defineProps<{
  shipmentId: number;
  totalPayable?: number;
  totalReceived?: number;
  amountDue?: number;
  receivableCod?: number;
}>();

const totalPayable = computed(() => props.totalPayable ?? 0);
const totalReceived = computed(() => props.totalReceived ?? 0);
const amountDue = computed(() => props.amountDue ?? 0);
const receivableCod = computed(() => props.receivableCod ?? 0);
const remaining = computed(() => totalPayable.value - totalReceived.value);

const paymentMethodStore = usePaymentMethodStore();
const store = useShipmentPaymentStore();

const payments = computed(() => store.items);
const paymentMethods = computed(() => paymentMethodStore.items);

// Date pickers
const datePickerRefs = ref<Record<string, any>>({});
const flatpickrInstances: Record<string, any> = {};

function initDatePickers() {
  const fields = ['new_payment_date', 'edit_payment_date'];
  fields.forEach(field => {
    const el = datePickerRefs.value[field];
    if (el) {
      if (flatpickrInstances[field]) flatpickrInstances[field].destroy();
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          if (field === 'new_payment_date') {
            newPayment.value.payment_date = dateStr;
          } else if (field === 'edit_payment_date') {
            editForm.value.payment_date = dateStr;
          }
        },
      });
      const val = field === 'new_payment_date' ? newPayment.value.payment_date : editForm.value.payment_date;
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val;
        flatpickrInstances[field].setDate(clean);
      }
    }
  });
}

function updateDatePickers() {
  Object.keys(flatpickrInstances).forEach(field => {
    const inst = flatpickrInstances[field];
    const val = field === 'new_payment_date' ? newPayment.value.payment_date : editForm.value.payment_date;
    if (inst && val) {
      const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val;
      inst.setDate(clean);
    }
  });
}

const newPayment = ref({
  amount: null as number | null,
  payment_date: new Date().toISOString().split('T')[0],
  payment_method: '',
  reference_number: '',
  notes: '',
});

const submitting = ref(false);
const editing = ref(false);
const editForm = ref<any>({});
const editId = ref<number | null>(null);

async function addPayment() {
  if (!newPayment.value.amount || !newPayment.value.payment_date) return;
  submitting.value = true;
  try {
    await store.createPayment(props.shipmentId, newPayment.value);
    newPayment.value = {
      amount: null,
      payment_date: new Date().toISOString().split('T')[0],
      payment_method: '',
      reference_number: '',
      notes: '',
    };
    await nextTick();
    updateDatePickers();
    emit('paymentChanged');
  } catch (err) {
    console.error(err);
  } finally {
    submitting.value = false;
  }
}

function editPayment(payment: any) {
  editId.value = payment.id;
  editForm.value = { ...payment };
  editing.value = true;
  nextTick(() => initDatePickers());
}

async function updatePayment() {
  if (!editId.value) return;
  try {
    await store.updatePayment(editId.value, editForm.value);
    editing.value = false;
    emit('paymentChanged');
  } catch (err) {
    console.error(err);
  }
}

async function deletePayment(id: number) {
  if (!confirm('Delete this payment?')) return;
  try {
    await store.deletePayment(id);
    emit('paymentChanged');
  } catch (err) {
    console.error(err);
  }
}

const emit = defineEmits<{
  (e: 'paymentChanged'): void;
}>();

onMounted(async () => {
  await paymentMethodStore.fetchItems(1, { per_page: 100 });
  store.fetchPayments(props.shipmentId);
  await nextTick();
  initDatePickers();
});

watch(() => props.shipmentId, async (newId) => {
  if (newId) {
    store.fetchPayments(newId);
    await nextTick();
    initDatePickers();
  }
});

onBeforeUnmount(() => {
  Object.values(flatpickrInstances).forEach(inst => {
    if (inst) inst.destroy();
  });
});

watch(editing, (newVal) => {
  if (newVal) {
    nextTick(() => {
      const el = datePickerRefs.value.edit_payment_date;
      if (el && flatpickrInstances.edit_payment_date) {
        flatpickrInstances.edit_payment_date.destroy();
        flatpickrInstances.edit_payment_date = flatpickr(el, {
          dateFormat: 'Y-m-d',
          allowInput: true,
          onChange: (_dates: any, dateStr: string) => {
            editForm.value.payment_date = dateStr;
          },
        });
        if (editForm.value.payment_date) {
          const clean = typeof editForm.value.payment_date === 'string' && editForm.value.payment_date.includes('T')
            ? editForm.value.payment_date.split('T')[0]
            : editForm.value.payment_date;
          flatpickrInstances.edit_payment_date.setDate(clean);
        }
      }
    });
  }
});
</script>
