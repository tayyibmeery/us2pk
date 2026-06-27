<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Weight Discount' : 'Add Weight Discount'"
    :subtitle="formData.id ? 'Update the discount details below.' : 'Fill in the details to add a new weight discount.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Warehouse
          </label>
          <select v-model="formData.warehouse_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Select Warehouse</option>
            <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">
              {{ wh.name }} ({{ wh.code }})
            </option>
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Discount Percent (%)
          </label>
          <input v-model.number="formData.discount_percent" type="number" step="0.01" min="0" max="100"
            placeholder="e.g. 10.5"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import FormModal from '@/components/common/FormModal.vue';
import { useWarehouseStore } from '@/stores/warehouseStore';
import type { WeightDiscount } from '@/stores/weightDiscountStore';

const props = defineProps<{
  isOpen: boolean;
  initialData?: WeightDiscount | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', data: Partial<WeightDiscount>): void;
}>();

const warehouseStore = useWarehouseStore();
const warehouses = ref<any[]>([]);

const formData = ref<Partial<WeightDiscount>>({
  warehouse_id: undefined,
  discount_percent: 0,
});

// Load warehouses when modal opens
const loadWarehouses = async () => {
  if (warehouseStore.items.length === 0) {
    await warehouseStore.fetchItems(1, { per_page: 999 });
  }
  warehouses.value = warehouseStore.items;
};

watch(
  () => props.isOpen,
  async (open) => {
    if (open) {
      await loadWarehouses();
      if (props.initialData) {
        formData.value = { ...props.initialData };
      } else {
        formData.value = { warehouse_id: undefined, discount_percent: 0 };
      }
    } else {
      formData.value = { warehouse_id: undefined, discount_percent: 0 };
    }
  },
  { immediate: true }
);

const close = () => emit('close');
const save = () => emit('save', formData.value);
</script>
