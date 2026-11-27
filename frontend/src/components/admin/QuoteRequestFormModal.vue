<template>
  <FormModal :isOpen="isOpen" :title="'Quote Request Details'" :subtitle="'Full details of the quote request'"
    :saveLabel="'Close'" @close="close" @save="close">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Name -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Name
          </label>
          <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            {{ formData.name || 'N/A' }}
          </div>
        </div>

        <!-- Email -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Email
          </label>
          <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            {{ formData.email || 'N/A' }}
          </div>
        </div>

        <!-- Mobile -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Mobile
          </label>
          <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            {{ formData.mobile || 'N/A' }}
          </div>
        </div>

        <!-- Service -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Service
          </label>
          <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            {{ formData.service || 'N/A' }}
          </div>
        </div>

        <!-- Status -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusClass(formData.status)">
              {{ getStatusLabel(formData.status) }}
            </span>
          </div>
        </div>

        <!-- Created At -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Created At
          </label>
          <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            {{ formatDate(formData.created_at) }}
          </div>
        </div>

        <!-- Note / Message -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Message / Note
          </label>
          <div
            class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 min-h-[100px] whitespace-pre-wrap">
            {{ formData.note || 'No message provided' }}
          </div>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { QuoteRequest } from '@/stores/quoteRequestStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: QuoteRequest | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: FormData): void
}>()

const formData = ref<Partial<QuoteRequest>>({
  name: '',
  email: '',
  mobile: '',
  service: '',
  note: '',
  status: 'pending',
  created_at: '',
})

const getStatusClass = (status: string): string => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    contacted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    pending: 'Pending',
    contacted: 'Contacted',
    completed: 'Completed'
  }
  return labels[status] || status
}

const formatDate = (date: string | undefined): string => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const close = () => emit('close')

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
    } else {
      formData.value = {
        name: '',
        email: '',
        mobile: '',
        service: '',
        note: '',
        status: 'pending',
        created_at: '',
      }
    }
  },
  { immediate: true }
)
</script>
