<template>
  <FormModal :isOpen="isOpen" :title="initialData ? 'Edit Expense' : 'Add Expense'"
    :subtitle="initialData ? 'Update the expense details.' : 'Record a new expense.'"
    :saveLabel="initialData ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Category</label>
          <select v-model="form.category_id" class="w-full rounded-lg border p-2 dark:bg-gray-800">
            <option :value="null">Uncategorized</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Amount (PKR) *</label>
          <input v-model.number="form.amount" type="number" step="0.01"
            class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Date *</label>
          <input v-model="form.date" type="date" class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Description</label>
          <textarea v-model="form.description" rows="2"
            class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium">Reference (Invoice/Bill #)</label>
          <input v-model="form.reference" class="w-full rounded-lg border p-2 dark:bg-gray-800" />
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import FormModal from '@/components/common/FormModal.vue';
import { useExpenseCategoryStore } from '@/stores/expenseCategoryStore';

const props = defineProps<{
  isOpen: boolean;
  initialData?: any;
}>();

const emit = defineEmits(['close', 'save']);

const categoryStore = useExpenseCategoryStore();
const categories = ref<any[]>([]);

const form = ref({
  category_id: null,
  amount: null,
  date: new Date().toISOString().split('T')[0],
  description: '',
  reference: '',
});

const resetForm = () => {
  form.value = {
    category_id: null,
    amount: null,
    date: new Date().toISOString().split('T')[0],
    description: '',
    reference: '',
  };
};

watch(() => props.initialData, (data) => {
  if (data) {
    form.value = {
      category_id: data.category_id || null,
      amount: data.amount || null,
      date: data.date || new Date().toISOString().split('T')[0],
      description: data.description || '',
      reference: data.reference || '',
    };
  } else {
    resetForm();
  }
}, { immediate: true });

onMounted(async () => {
  await categoryStore.fetchAll();
  categories.value = categoryStore.items;
});

const close = () => emit('close');
const save = () => {
  if (!form.value.amount || !form.value.date) {
    alert('Amount and Date are required.');
    return;
  }
  emit('save', { ...form.value });
};
</script>
