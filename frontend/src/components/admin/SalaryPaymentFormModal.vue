<template>
  <FormModal :isOpen="isOpen" :title="initialData ? 'Edit Salary Payment' : 'Add Salary Payment'"
    :subtitle="initialData ? 'Update payment details.' : 'Record a salary payment.'"
    :saveLabel="initialData ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Employee *</label>
          <select v-model="form.employee_id" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Month *</label>
            <select v-model="form.month" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
              <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">Year *</label>
            <select v-model="form.year" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
              <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium">Amount (PKR) *</label>
          <input v-model.number="form.amount" type="number" step="0.01"
            class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Paid Date *</label>
          <input v-model="form.paid_date" type="date" class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Notes</label>
          <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import FormModal from '@/components/common/FormModal.vue';
import { useEmployeeStore } from '@/stores/employeeStore';

const props = defineProps<{
  isOpen: boolean;
  initialData?: any;
}>();

const emit = defineEmits(['close', 'save']);

const employeeStore = useEmployeeStore();
const employees = ref<any[]>([]);

const months = [
  { value: 1, label: 'January' },
  { value: 2, label: 'February' },
  { value: 3, label: 'March' },
  { value: 4, label: 'April' },
  { value: 5, label: 'May' },
  { value: 6, label: 'June' },
  { value: 7, label: 'July' },
  { value: 8, label: 'August' },
  { value: 9, label: 'September' },
  { value: 10, label: 'October' },
  { value: 11, label: 'November' },
  { value: 12, label: 'December' },
];

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 10 }, (_, i) => currentYear - i);

const form = ref({
  employee_id: null,
  month: new Date().getMonth() + 1,
  year: currentYear,
  amount: null,
  paid_date: new Date().toISOString().split('T')[0],
  notes: '',
});

const resetForm = () => {
  form.value = {
    employee_id: null,
    month: new Date().getMonth() + 1,
    year: currentYear,
    amount: null,
    paid_date: new Date().toISOString().split('T')[0],
    notes: '',
  };
};

watch(() => props.initialData, (data) => {
  if (data) {
    form.value = {
      employee_id: data.employee_id || null,
      month: data.month || new Date().getMonth() + 1,
      year: data.year || currentYear,
      amount: data.amount || null,
      paid_date: data.paid_date || new Date().toISOString().split('T')[0],
      notes: data.notes || '',
    };
  } else {
    resetForm();
  }
}, { immediate: true });

onMounted(async () => {
  await employeeStore.fetchAll();
  employees.value = employeeStore.items;
});

const close = () => emit('close');
const save = () => {
  if (!form.value.employee_id || !form.value.amount || !form.value.paid_date) {
    alert('Employee, Amount, and Paid Date are required.');
    return;
  }
  emit('save', { ...form.value });
};
</script>
