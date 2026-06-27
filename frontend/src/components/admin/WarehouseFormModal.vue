<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Warehouse' : 'Add Warehouse'"
    :subtitle="formData.id ? 'Update the warehouse details below.' : 'Fill in the details to add a new warehouse.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Name
          </label>
          <input v-model="formData.name" type="text" placeholder="e.g. USA"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Code
          </label>
          <input v-model="formData.code" type="text" placeholder="e.g. US"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Address
          </label>
          <textarea v-model="formData.address" rows="3" placeholder="Enter full address"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
        <div class="sm:col-span-2">
          <label class="flex items-center space-x-2">
            <input v-model="formData.status" type="checkbox"
              class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Active</span>
          </label>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import FormModal from '@/components/common/FormModal.vue';
import type { Warehouse } from '@/stores/warehouseStore';

const props = defineProps<{
  isOpen: boolean;
  initialData?: Warehouse | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', data: Partial<Warehouse>): void;
}>();

const formData = ref<Partial<Warehouse>>({
  name: '',
  code: '',
  address: '',
  status: true,
});

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal };
    } else {
      formData.value = { name: '', code: '', address: '', status: true };
    }
  },
  { immediate: true },
);

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = { name: '', code: '', address: '', status: true };
    }
  },
);

const close = () => emit('close');
const save = () => emit('save', formData.value);
</script>
