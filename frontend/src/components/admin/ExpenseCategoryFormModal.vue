<template>
  <FormModal :isOpen="isOpen" :title="initialData ? 'Edit Category' : 'Add Category'"
    :subtitle="initialData ? 'Update the category details.' : 'Create a new expense category.'"
    :saveLabel="initialData ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Name *</label>
          <input v-model="form.name" class="w-full rounded-lg border p-2 dark:bg-gray-800" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Type *</label>
          <select v-model="form.type" class="w-full rounded-lg border p-2 dark:bg-gray-800">
            <option value="operational">Operational</option>
            <option value="salary">Salary</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Description</label>
          <textarea v-model="form.description" rows="2"
            class="w-full rounded-lg border p-2 dark:bg-gray-800"></textarea>
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="form.status" id="status" />
          <label for="status" class="text-sm font-medium">Active</label>
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
  type: 'operational',
  description: '',
  status: true,
});

const resetForm = () => {
  form.value = {
    name: '',
    type: 'operational',
    description: '',
    status: true,
  };
};

watch(() => props.initialData, (data) => {
  if (data) {
    form.value = {
      name: data.name || '',
      type: data.type || 'operational',
      description: data.description || '',
      status: data.status !== undefined ? data.status : true,
    };
  } else {
    resetForm();
  }
}, { immediate: true });

const close = () => emit('close');
const save = () => {
  if (!form.value.name) {
    alert('Name is required.');
    return;
  }
  emit('save', { ...form.value });
};
</script>
