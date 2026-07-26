<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Hero Slide' : 'Add Hero Slide'"
    :subtitle="formData.id ? 'Update the hero slide details below.' : 'Fill in the details to add a new hero slide.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Title -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Title <span class="text-red-500">*</span>
          </label>
          <input v-model="formData.title" type="text" placeholder="e.g. #1 Shipping Solution"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required />
        </div>

        <!-- Subtitle -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Subtitle
          </label>
          <input v-model="formData.subtitle" type="text" placeholder="e.g. Shipping & Logistics Solution"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Content -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Content
          </label>
          <textarea v-model="formData.content" rows="3" placeholder="Main heading content (HTML supported)"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Description -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Description
          </label>
          <textarea v-model="formData.description" rows="2" placeholder="Brief description text"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Image Upload -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Image
          </label>
          <div
            class="relative flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg transition-all duration-200"
            :class="[
              displayImage
                ? 'border-green-400 bg-green-50 dark:bg-green-900/10'
                : 'border-gray-300 dark:border-gray-600 hover:border-brand-400 dark:hover:border-brand-500 bg-gray-50 dark:bg-gray-700/50'
            ]">
            <div class="space-y-2 text-center w-full">
              <div v-if="displayImage" class="relative inline-block">
                <img :src="displayImage" alt="Preview"
                  class="mx-auto max-h-48 w-auto object-contain rounded-lg shadow-md" @error="handlePreviewError" />
                <button type="button" @click="removeImage"
                  class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-110"
                  :disabled="deletingImage">
                  <i class="fas fa-times text-sm"></i>
                </button>
              </div>

              <div v-else>
                <div class="flex justify-center">
                  <div class="w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <i class="fas fa-cloud-upload-alt text-3xl text-blue-500 dark:text-blue-400"></i>
                  </div>
                </div>
                <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                  <label for="image-upload"
                    class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-500 transition-colors duration-200">
                    <span>Upload a file</span>
                    <input id="image-upload" type="file" accept="image/*" class="sr-only" @change="handleImageUpload" />
                  </label>
                  <p class="pl-1 text-gray-500 dark:text-gray-400">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, SVG, WEBP up to 2MB</p>
              </div>

              <div v-if="uploading" class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                  <div
                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out"
                    :style="{ width: uploadProgress + '%' }"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">
                  <i class="fas fa-spinner fa-spin mr-1"></i>
                  {{ uploadProgress }}% uploaded
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Button 1 Text -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Button 1 Text
          </label>
          <input v-model="formData.button1_text" type="text" placeholder="e.g. Get Started"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Button 1 Link -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Button 1 Link
          </label>
          <input v-model="formData.button1_link" type="text" placeholder="e.g. /signup"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Button 2 Text -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Button 2 Text
          </label>
          <input v-model="formData.button2_text" type="text" placeholder="e.g. Free Quote"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Button 2 Link -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Button 2 Link
          </label>
          <input v-model="formData.button2_link" type="text" placeholder="e.g. #contact"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Order -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Order
          </label>
          <input v-model.number="formData.order" type="number" min="0"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Status -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { HeroSlide } from '@/stores/heroSlideStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: HeroSlide | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: FormData): void
}>()

const uploading = ref(false)
const uploadProgress = ref(0)
const deletingImage = ref(false)
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)

const formData = ref<Partial<HeroSlide>>({
  title: '',
  subtitle: '',
  content: '',
  description: '',
  image: null,
  button1_text: '',
  button1_link: '',
  button2_text: '',
  button2_link: '',
  order: 0,
  status: true,
})

const getStorageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com'
  if (imagePath.startsWith('/storage/')) return `${baseUrl}${imagePath}`
  if (imagePath.startsWith('storage/')) return `${baseUrl}/${imagePath}`
  return `${baseUrl}/storage/${imagePath}`
}

const displayImage = computed(() => {
  if (imagePreview.value) return imagePreview.value
  if (formData.value.image) return getStorageUrl(formData.value.image)
  return null
})

const resetForm = () => {
  formData.value = {
    title: '',
    subtitle: '',
    content: '',
    description: '',
    image: null,
    button1_text: '',
    button1_link: '',
    button2_text: '',
    button2_link: '',
    order: 0,
    status: true,
  }
  imageFile.value = null
  imagePreview.value = null
  uploadProgress.value = 0
  uploading.value = false
}

const close = () => emit('close')

const handlePreviewError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect width="200" height="200" fill="%23e5e7eb"/%3E%3Ctext x="50" y="110" font-family="sans-serif" font-size="20" fill="%236b7280"%3ENo Image%3C/text%3E%3C/svg%3E'
}

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp']
  if (!validTypes.includes(file.type)) {
    alert('Please upload a valid image file (JPEG, PNG, GIF, SVG, WEBP)')
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    alert('Image size must be less than 2MB')
    return
  }

  imageFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)
  uploading.value = true
  uploadProgress.value = 100
  target.value = ''
}

const removeImage = () => {
  formData.value.image = null
  imageFile.value = null
  imagePreview.value = null
  uploading.value = false
}

const save = () => {
  const form = new FormData()

  // Add all form fields
  const fields = ['title', 'subtitle', 'content', 'description', 'button1_text', 'button1_link', 'button2_text', 'button2_link']
  fields.forEach(field => {
    const value = (formData.value as any)[field]
    if (value !== undefined && value !== null) {
      form.append(field, String(value))
    }
  })

  if (formData.value.order !== undefined && formData.value.order !== null) {
    form.append('order', String(formData.value.order))
  }
  if (formData.value.status !== undefined && formData.value.status !== null) {
    form.append('status', formData.value.status ? '1' : '0')
  }

  // Add image if new file is selected
  if (imageFile.value) {
    form.append('image', imageFile.value)
  }

  emit('save', form)
}

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal, status: !!newVal.status }
    } else {
      resetForm()
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) resetForm()
  }
)
</script>
