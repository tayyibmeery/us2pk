<template>
  <FormModal :isOpen="isOpen" :title="initialData ? 'Edit Employee' : 'Add Employee'"
    :subtitle="initialData ? 'Update employee details.' : 'Add a new employee.'"
    :saveLabel="initialData ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Name *</label>
          <input v-model="form.name" class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Position</label>
          <input v-model="form.position" class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>
        <div>
          <label class="block text-sm font-medium">Monthly Salary (PKR) *</label>
          <input v-model.number="form.monthly_salary" type="number" step="0.01"
            class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Joining Date</label>
          <input v-model="form.joining_date" type="date" class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="form.status" id="emp_status" />
          <label for="emp_status" class="text-sm font-medium">Active</label>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import FormModal from '@/components/common/FormModal.vue';

const props = defineProps<{
  isOpen: boolean;
  initialData?: any;
}>();

const emit = defineEmits(['close', 'save']);

const form = ref({
  name: '',
  position: '',
  monthly_salary: null,
  joining_date: '',
  status: true,
});

const resetForm = () => {
  form.value = {
    name: '',
    position: '',
    monthly_salary: null,
    joining_date: '',
    status: true,
  };
};

watch(() => props.initialData, (data) => {
  if (data) {
    form.value = {
      name: data.name || '',
      position: data.position || '',
      monthly_salary: data.monthly_salary || null,
      joining_date: data.joining_date || '',
      status: data.status !== undefined ? data.status : true,
    };
  } else {
    resetForm();
  }
}, { immediate: true });

const close = () => emit('close');
const save = () => {
  if (!form.value.name || !form.value.monthly_salary) {
    alert('Name and Monthly Salary are required.');
    return;
  }
  emit('save', { ...form.value });
};
</script>
