<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Account' : 'Add Account'"
    :subtitle="formData.id ? 'Update account details.' : 'Create a new account.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm font-medium">Name *</label>
          <input v-model="formData.name" type="text" class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Class *</label>
          <select v-model="formData.acc_class" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
            <option value="Assets">Assets</option>
            <option value="Liabilities">Liabilities</option>
            <option value="Equity">Equity</option>
            <option value="Income">Income</option>
            <option value="Expense">Expense</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Nature *</label>
          <select v-model="formData.nature" class="w-full rounded-lg border p-2 dark:bg-gray-800" required>
            <option value="Debit">Debit</option>
            <option value="Credit">Credit</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Ownership</label>
          <select v-model="formData.ownership" class="w-full rounded-lg border p-2 dark:bg-gray-800">
            <option value="US2PK">US2PK</option>
            <option value="Others">Others</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">P&L Category</label>
          <select v-model="formData.pandlcategory" class="w-full rounded-lg border p-2 dark:bg-gray-800">
            <option value="Revenue">Revenue</option>
            <option value="Cost of Sales">Cost of Sales</option>
            <option value="Operating Expenses">Operating Expenses</option>
            <option value="Other Project Expenses">Other Project Expenses</option>
            <option value="None">None</option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="formData.is_active" id="is_active" />
          <label for="is_active" class="text-sm font-medium">Active</label>
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

const formData = ref({
  id: null,
  name: '',
  acc_class: 'Assets',
  nature: 'Debit',
  ownership: 'US2PK',
  pandlcategory: 'None',
  is_active: true,
});

// ✅ Moved resetForm BEFORE the watches
const resetForm = () => {
  formData.value = {
    id: null,
    name: '',
    acc_class: 'Assets',
    nature: 'Debit',
    ownership: 'US2PK',
    pandlcategory: 'None',
    is_active: true,
  };
};

// Now watches can safely call resetForm
watch(() => props.initialData, (data) => {
  if (data) {
    formData.value = { ...data };
  } else {
    resetForm();
  }
}, { immediate: true });

watch(() => props.isOpen, (open) => {
  if (!open) resetForm();
});

const close = () => emit('close');
const save = () => {
  if (!formData.value.name) {
    alert('Name is required.');
    return;
  }
  emit('save', { ...formData.value });
};
</script>
