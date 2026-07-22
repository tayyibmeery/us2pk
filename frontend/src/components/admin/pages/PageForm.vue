<!-- frontend/src/components/admin/pages/PageForm.vue -->
<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- Backdrop -->
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="close"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal -->
      <div
        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-6 py-4 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-white">
            {{ page ? '✏️ Edit Page' : '➕ Create New Page' }}
          </h3>
          <button @click="close"
            class="text-white/80 hover:text-white transition-colors duration-200 rounded-lg p-1 hover:bg-white/10">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <form @submit.prevent="save" class="px-6 py-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Title -->
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Title <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <i class="fas fa-heading absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <input v-model="form.title" type="text"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                  placeholder="Enter title" required />
              </div>
            </div>

            <!-- Slug -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Slug
              </label>
              <div class="relative">
                <i class="fas fa-link absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <input v-model="form.slug" type="text"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                  placeholder="auto-generated" />
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty to auto-generate from title</p>
            </div>

            <!-- Type -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Type <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <select v-model="form.type"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 appearance-none"
                  required>
                  <option value="">Select type</option>
                  <option v-for="(label, value) in types" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
                <i
                  class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none"></i>
              </div>
            </div>

            <!-- Image Upload -->
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Image
              </label>
              <div
                class="relative mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl transition-all duration-200"
                :class="[
                  displayImage
                    ? 'border-green-400 bg-green-50 dark:bg-green-900/10'
                    : 'border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 bg-gray-50 dark:bg-gray-700/50'
                ]">
                <div class="space-y-2 text-center w-full">
                  <!-- Image Preview -->
                  <div v-if="displayImage" class="relative inline-block">
                    <img :src="displayImage" alt="Preview"
                      class="mx-auto max-h-48 w-auto object-contain rounded-lg shadow-md" />
                    <button type="button" @click="removeImage"
                      class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-110"
                      :disabled="deletingImage">
                      <i class="fas fa-times text-sm"></i>
                    </button>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                      <i class="fas fa-check-circle text-green-500 mr-1"></i>
                      Image uploaded successfully
                    </p>
                  </div>

                  <!-- Upload Area -->
                  <div v-else>
                    <div class="flex justify-center">
                      <div
                        class="w-20 h-20 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-blue-500 dark:text-blue-400"></i>
                      </div>
                    </div>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                      <label for="image-upload"
                        class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 transition-colors duration-200">
                        <span>Upload a file</span>
                        <input id="image-upload" type="file" accept="image/*" class="sr-only"
                          @change="handleImageUpload" />
                      </label>
                      <p class="pl-1 text-gray-500 dark:text-gray-400">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      <i class="fas fa-info-circle mr-1"></i>
                      PNG, JPG, GIF, SVG, WEBP up to 2MB
                    </p>
                  </div>

                  <!-- Upload Progress -->
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

            <!-- Content -->
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Content
              </label>
              <div class="relative">
                <i class="fas fa-align-left absolute left-3 top-3 text-gray-400 dark:text-gray-500"></i>
                <textarea v-model="form.content" rows="5"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                  placeholder="Enter content (HTML supported)"></textarea>
              </div>
            </div>

            <!-- Icon -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Icon
              </label>
              <div class="relative">
                <i class="fas fa-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <input v-model="form.icon" type="text"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                  placeholder="fa fa-icon" />
              </div>
              <div v-if="form.icon" class="mt-2 flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Preview:</span>
                <i :class="form.icon" class="text-2xl text-blue-600 dark:text-blue-400"></i>
              </div>
            </div>

            <!-- Order -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Order
              </label>
              <div class="relative">
                <i
                  class="fas fa-sort-numeric-up absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <input v-model.number="form.order" type="number"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                  min="0" />
              </div>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Status
              </label>
              <div class="relative">
                <i
                  class="fas fa-toggle-on absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <select v-model="form.status"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 appearance-none">
                  <option :value="true">✅ Active</option>
                  <option :value="false">❌ Inactive</option>
                </select>
                <i
                  class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none"></i>
              </div>
            </div>

            <!-- Meta Data -->
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                Meta Data (JSON)
              </label>
              <div class="relative">
                <i class="fas fa-code absolute left-3 top-3 text-gray-400 dark:text-gray-500"></i>
                <textarea v-model="metaJson" rows="6"
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 pl-10 pr-4 py-2.5 font-mono text-sm text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                  placeholder='{"subtitle": "Your subtitle", "description": "Your description"}'></textarea>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                Add custom meta data as JSON. This is optional and depends on the page type.
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" @click="close"
              class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 font-medium">
              Cancel
            </button>
            <button type="submit"
              class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 font-medium shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              :disabled="submitting || uploading">
              <i v-if="submitting || uploading" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-save"></i>
              {{ submitting ? 'Saving...' : (uploading ? 'Uploading...' : (page ? 'Update Page' : 'Create Page')) }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { usePageStore } from '@/stores/pageStore';
import type { Page } from '@/types/page';

const props = defineProps<{
  isOpen: boolean;
  page: Page | null;
  types: Record<string, string>;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'save', data: any): void;
}>();

const pageStore = usePageStore();

// State
const submitting = ref(false);
const uploading = ref(false);
const uploadProgress = ref(0);
const deletingImage = ref(false);
const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

const form = ref({
  title: '',
  slug: '',
  type: '',
  content: '',
  image: null as string | null,
  icon: null as string | null,
  order: 0,
  status: true,
  meta: {} as Record<string, any>,
});

// ONLY handle storage images - no fallbacks
const getStorageUrl = (imagePath: string | null | undefined): string => {
  if (!imagePath) return '';

  // Allow external URLs
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath;
  }

  // Only accept storage paths
  if (imagePath.startsWith('/storage/')) {
    return imagePath;
  }

  if (imagePath.startsWith('storage/')) {
    return '/' + imagePath;
  }

  if (imagePath.startsWith('pages/')) {
    return `/storage/${imagePath}`;
  }

  // Reject any other paths
  return '';
};

// Check if image is valid (from storage or external)
const isValidImage = (imagePath: string | null | undefined): boolean => {
  if (!imagePath) return false;

  // Allow external URLs
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return true;
  }

  // Only valid storage paths
  return imagePath.startsWith('/storage/') ||
    imagePath.startsWith('storage/') ||
    imagePath.startsWith('pages/');
};

// Computed property for display image
const displayImage = computed(() => {
  // If there's a new image preview (from upload), show it
  if (imagePreview.value) {
    return imagePreview.value;
  }

  // If there's an existing image in the form and it's valid
  if (form.value.image && isValidImage(form.value.image)) {
    return getStorageUrl(form.value.image);
  }

  return null;
});

// Handle image preview errors
const handlePreviewError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect width="200" height="200" fill="%23e5e7eb"/%3E%3Ctext x="50" y="110" font-family="sans-serif" font-size="20" fill="%236b7280"%3ENo Image%3C/text%3E%3C/svg%3E';
};

const metaJson = computed({
  get: () => {
    try {
      return JSON.stringify(form.value.meta, null, 2);
    } catch {
      return '{}';
    }
  },
  set: (value: string) => {
    try {
      form.value.meta = JSON.parse(value);
    } catch {
      // Invalid JSON, ignore
    }
  }
});

// Watch for modal open to populate form
watch(
  () => props.isOpen,
  (open) => {
    if (open && props.page) {
      form.value = {
        title: props.page.title,
        slug: props.page.slug || '',
        type: props.page.type,
        content: props.page.content || '',
        image: props.page.image || null,
        icon: props.page.icon || null,
        order: props.page.order || 0,
        status: props.page.status,
        meta: props.page.meta || {},
      };

      imagePreview.value = null;
      imageFile.value = null;

      console.log('Page loaded for edit:', {
        id: props.page.id,
        image: props.page.image,
        imageUrl: getStorageUrl(props.page.image)
      });

    } else if (open) {
      resetForm();
    }
  },
  { immediate: true }
);

const resetForm = () => {
  form.value = {
    title: '',
    slug: '',
    type: '',
    content: '',
    image: null,
    icon: null,
    order: 0,
    status: true,
    meta: {},
  };
  imageFile.value = null;
  imagePreview.value = null;
};

const close = () => {
  emit('close');
};

const handleImageUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;

  // Validate file type
  const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
  if (!validTypes.includes(file.type)) {
    alert('Please upload a valid image file (JPEG, PNG, GIF, SVG, WEBP)');
    return;
  }

  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert('Image size must be less than 2MB');
    return;
  }

  imageFile.value = file;

  // Create preview
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target?.result as string;
  };
  reader.readAsDataURL(file);

  // Upload image
  uploading.value = true;
  uploadProgress.value = 0;

  try {
    const url = await pageStore.uploadImage(file);
    form.value.image = url;
    uploadProgress.value = 100;
    console.log('Image uploaded successfully:', url);
  } catch (error) {
    alert('Failed to upload image. Please try again.');
    imageFile.value = null;
    imagePreview.value = null;
  } finally {
    uploading.value = false;
    target.value = '';
  }
};

const removeImage = async () => {
  // If we have a page with an image, delete it from server
  if (props.page && props.page.id && props.page.image) {
    deletingImage.value = true;
    try {
      const result = await pageStore.deleteImage(props.page.id);
      if (result) {
        // Update the local page object
        props.page.image = null;
        // Also update the form
        form.value.image = null;
        imageFile.value = null;
        imagePreview.value = null;
        console.log('Image removed successfully');
      }
    } catch (error) {
      console.error('Error removing image:', error);
      alert('Failed to remove image. Please try again.');
    } finally {
      deletingImage.value = false;
    }
  } else {
    // Just clear the local form
    form.value.image = null;
    imageFile.value = null;
    imagePreview.value = null;
  }
};

const save = async () => {
  submitting.value = true;
  try {
    const formData = new FormData();

    formData.append('title', form.value.title || '');
    formData.append('slug', form.value.slug || '');
    formData.append('type', form.value.type || '');
    formData.append('content', form.value.content || '');
    formData.append('order', String(form.value.order || 0));
    formData.append('status', form.value.status ? '1' : '0');
    formData.append('icon', form.value.icon || '');
    formData.append('meta', JSON.stringify(form.value.meta || {}));

    if (imageFile.value) {
      formData.append('image', imageFile.value);
    }

    emit('save', formData);
  } catch (error) {
    console.error('Error saving form:', error);
  } finally {
    submitting.value = false;
  }
};

// Generate slug from title
watch(
  () => form.value.title,
  (title) => {
    if (title && !form.value.slug) {
      form.value.slug = title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }
  }
);
</script>
