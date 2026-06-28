<template>
  <div class="mt-6">
    <h3 class="text-lg font-semibold mb-3">Payment History</h3>

    <!-- Add Payment Form -->
    <div class="mb-4 p-4 border rounded-lg dark:border-gray-700">
      <h4 class="text-sm font-medium mb-3">Record New Payment</h4>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Amount -->
        <div>
          <label class="block text-sm font-medium mb-1">Amount (PKR)</label>
          <input v-model.number="newPayment.amount" type="number" step="0.01" placeholder="0.00"
            class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>

        <!-- Payment Date -->
        <div>
          <label class="block text-sm font-medium mb-1">Payment Date</label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.new_payment_date = el }" v-model="newPayment.payment_date"
              type="text" class="w-full rounded-lg border p-2 pr-10 dark:bg-gray-800" placeholder="Select date"
              readonly />
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Payment Method -->
        <div>
          <label class="block text-sm font-medium mb-1">Payment Method</label>
          <select v-model="newPayment.payment_method" class="w-full rounded-lg border p-2 dark:bg-gray-800">
            <option value="">Select Method</option>
            <option v-for="method in paymentMethods" :key="method.id" :value="method.name">
              {{ method.name }}
            </option>
          </select>
        </div>

        <!-- Reference Number -->
        <div>
          <label class="block text-sm font-medium mb-1">Reference No.</label>
          <input v-model="newPayment.reference_number" placeholder="e.g. TXN12345"
            class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>

        <!-- Notes (full width) -->
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium mb-1">Notes</label>
          <textarea v-model="newPayment.notes" rows="1" placeholder="Optional notes"
            class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
        </div>
      </div>

      <button @click="addPayment" :disabled="submitting || !newPayment.amount || !newPayment.payment_date"
        class="mt-3 bg-brand-500 text-white px-4 py-2 rounded-lg hover:bg-brand-600 disabled:opacity-50">
        Add Payment
      </button>
    </div>

    <!-- Payment List -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-800">
          <tr>
            <th class="px-3 py-2 text-left">Date</th>
            <th class="px-3 py-2 text-right">Amount</th>
            <th class="px-3 py-2 text-left">Method</th>
            <th class="px-3 py-2 text-left">Reference</th>
            <th class="px-3 py-2 text-left">Notes</th>
            <th class="px-3 py-2 text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments" :key="payment.id" class="border-b dark:border-gray-700">
            <td class="px-3 py-2">{{ payment.payment_date }}</td>
            <td class="px-3 py-2 text-right">{{ Number(payment.amount).toFixed(2) }}</td>
            <td class="px-3 py-2">{{ payment.payment_method || '—' }}</td>
            <td class="px-3 py-2">{{ payment.reference_number || '—' }}</td>
            <td class="px-3 py-2 max-w-xs truncate" :title="payment.notes">{{ payment.notes || '—' }}</td>
            <td class="px-3 py-2 text-center">
              <button @click="editPayment(payment)" class="text-blue-500 hover:underline mr-2">Edit</button>
              <button @click="deletePayment(payment.id)" class="text-red-500 hover:underline">Delete</button>
            </td>
          </tr>
          <tr v-if="!payments.length">
            <td colspan="6" class="py-4 text-center text-gray-400">No payments recorded yet.</td>
          </tr>
          <tr v-if="store.loading">
            <td colspan="6" class="py-4 text-center text-gray-400">Loading...</td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50 dark:bg-gray-800 font-medium">
          <tr>
            <td class="px-3 py-2 text-right" colspan="1">Total Received:</td>
            <td class="px-3 py-2 text-right">{{ totalReceived.toFixed(2) }}</td>
            <td colspan="4"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Edit Modal (inline with labels) -->
    <div v-if="editing" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Edit Payment</h3>
          <button @click="editing = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
        </div>

        <div class="space-y-4">
          <!-- Amount -->
          <div>
            <label class="block text-sm font-medium mb-1">Amount (PKR)</label>
            <input v-model.number="editForm.amount" type="number" step="0.01" placeholder="0.00"
              class="w-full rounded-lg border p-2 dark:bg-gray-800" />
          </div>

          <!-- Payment Date -->
          <div>
            <label class="block text-sm font-medium mb-1">Payment Date</label>
            <div class="relative">
              <input :ref="el => { if (el) datePickerRefs.edit_payment_date = el }" v-model="editForm.payment_date"
                type="text" class="w-full rounded-lg border p-2 pr-10 dark:bg-gray-800" placeholder="Select date"
                readonly />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                    fill="currentColor" />
                </svg>
              </span>
            </div>
          </div>

          <!-- Payment Method -->
          <div>
            <label class="block text-sm font-medium mb-1">Payment Method</label>
            <select v-model="editForm.payment_method" class="w-full rounded-lg border p-2 dark:bg-gray-800">
              <option value="">Select Method</option>
              <option v-for="method in paymentMethods" :key="method.id" :value="method.name">
                {{ method.name }}
              </option>
            </select>
          </div>

          <!-- Reference -->
          <div>
            <label class="block text-sm font-medium mb-1">Reference No.</label>
            <input v-model="editForm.reference_number" placeholder="e.g. TXN12345"
              class="w-full rounded-lg border p-2 dark:bg-gray-800" />
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea v-model="editForm.notes" rows="2" placeholder="Optional notes"
              class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-2">
            <button @click="editing = false" class="border px-4 py-2 rounded-lg">Cancel</button>
            <button @click="updatePayment" class="bg-brand-500 text-white px-4 py-2 rounded-lg">Update</button>
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
}>();

const paymentMethodStore = usePaymentMethodStore();
const store = useShipmentPaymentStore();

const payments = computed(() => store.items);
const totalReceived = computed(() => payments.value.reduce((sum, p) => sum + Number(p.amount), 0));
const paymentMethods = computed(() => paymentMethodStore.items);

// ── Date picker refs ────────────────────────────────────
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
      // Set initial value if any
      const val = field === 'new_payment_date'
        ? newPayment.value.payment_date
        : editForm.value.payment_date;
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
    const val = field === 'new_payment_date'
      ? newPayment.value.payment_date
      : editForm.value.payment_date;
    if (inst && val) {
      const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val;
      inst.setDate(clean);
    }
  });
}

// ── State ──────────────────────────────────────────────────
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

// ── Methods ──────────────────────────────────────────────
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
  nextTick(() => {
    initDatePickers();
  });
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

// ── Lifecycle ────────────────────────────────────────────
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
