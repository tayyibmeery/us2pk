<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Footer Section' : 'Add Footer Section'"
    :subtitle="formData.id ? 'Update the footer section details below.' : 'Fill in the details to add a new footer section.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Title
            </label>
            <input v-model="formData.title" type="text" placeholder="e.g. US2PK"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
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

        <!-- Contact Information -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Address
            </label>
            <input v-model="formData.address" type="text" placeholder="e.g. Lahore, Pakistan"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Phone
            </label>
            <input v-model="formData.phone" type="text" placeholder="+92 123 4567890"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Email
            </label>
            <input v-model="formData.email" type="email" placeholder="info@us2pk.com"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              WhatsApp Number
            </label>
            <input v-model="formData.whatsapp_number" type="text" placeholder="923015579810"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            <p class="mt-1 text-xs text-gray-500">Enter without + sign, e.g., 923015579810</p>
          </div>
        </div>

        <!-- Copyright & Newsletter -->
        <div class="grid grid-cols-1 gap-5">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Copyright Text
            </label>
            <input v-model="formData.copyright" type="text" placeholder="© 2026 US2PK. All rights reserved."
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Newsletter / Brand Text
            </label>
            <textarea v-model="formData.newsletter_text" rows="3"
              placeholder="Connecting Pakistan to the world's best products..."
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
          </div>
        </div>

        <!-- Social Icons -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
          <div class="flex items-center justify-between mb-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-400">
              Social Icons
            </label>
            <button type="button" @click="addSocialIcon"
              class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-600">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Add Social
            </button>
          </div>
          <div class="space-y-3">
            <div v-for="(icon, index) in formData.social_icons" :key="index"
              class="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
              <div class="flex-1 min-w-[120px]">
                <input v-model="icon.platform" type="text" placeholder="Platform"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div class="flex-1 min-w-[150px]">
                <input v-model="icon.url" type="text" placeholder="URL"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div class="flex-1 min-w-[120px]">
                <input v-model="icon.icon" type="text" placeholder="Icon class"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <button type="button" @click="removeSocialIcon(index)" class="text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          <p class="mt-2 text-xs text-gray-500">Common platforms: twitter, facebook, linkedin, youtube, instagram</p>
        </div>

        <!-- Service Links -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
          <div class="flex items-center justify-between mb-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-400">
              Service Links
            </label>
            <button type="button" @click="addServiceLink"
              class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-600">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Add Link
            </button>
          </div>
          <div class="space-y-3">
            <div v-for="(link, index) in formData.service_links" :key="index"
              class="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
              <div class="flex-1 min-w-[150px]">
                <input v-model="link.title" type="text" placeholder="Title"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div class="flex-1 min-w-[200px]">
                <input v-model="link.url" type="text" placeholder="URL"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <button type="button" @click="removeServiceLink(index)" class="text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Company Links -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
          <div class="flex items-center justify-between mb-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-400">
              Company Links
            </label>
            <button type="button" @click="addCompanyLink"
              class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-600">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Add Link
            </button>
          </div>
          <div class="space-y-3">
            <div v-for="(link, index) in formData.company_links" :key="index"
              class="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
              <div class="flex-1 min-w-[150px]">
                <input v-model="link.title" type="text" placeholder="Title"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div class="flex-1 min-w-[200px]">
                <input v-model="link.url" type="text" placeholder="URL"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <button type="button" @click="removeCompanyLink(index)" class="text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
          <div class="flex items-center justify-between mb-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-400">
              Quick Links
            </label>
            <button type="button" @click="addQuickLink"
              class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-600">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              Add Link
            </button>
          </div>
          <div class="space-y-3">
            <div v-for="(link, index) in formData.quick_links" :key="index"
              class="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
              <div class="flex-1 min-w-[150px]">
                <input v-model="link.title" type="text" placeholder="Title"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <div class="flex-1 min-w-[200px]">
                <input v-model="link.url" type="text" placeholder="URL"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
              </div>
              <button type="button" @click="removeQuickLink(index)" class="text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Legacy Social Fields (Hidden) -->
        <input type="hidden" v-model="formData.twitter" />
        <input type="hidden" v-model="formData.facebook" />
        <input type="hidden" v-model="formData.youtube" />
        <input type="hidden" v-model="formData.linkedin" />
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import FormModal from '@/components/common/FormModal.vue'
import type { FooterSection, SocialIcon, LinkItem } from '@/stores/footerSectionStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: FooterSection | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<FooterSection>): void
}>()

const formData = ref<Partial<FooterSection>>({
  title: '',
  address: '',
  phone: '',
  email: '',
  whatsapp_number: '',
  social_icons: [],
  twitter: '',
  facebook: '',
  youtube: '',
  linkedin: '',
  copyright: '',
  newsletter_text: '',
  service_links: [],
  quick_links: [],
  company_links: [],
  status: true,
})

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = {
        ...newVal,
        social_icons: newVal.social_icons || [],
        service_links: newVal.service_links || [],
        quick_links: newVal.quick_links || [],
        company_links: newVal.company_links || [],
        status: !!newVal.status,
      }
    } else {
      formData.value = {
        title: '',
        address: '',
        phone: '',
        email: '',
        whatsapp_number: '',
        social_icons: [],
        twitter: '',
        facebook: '',
        youtube: '',
        linkedin: '',
        copyright: '',
        newsletter_text: '',
        service_links: [],
        quick_links: [],
        company_links: [],
        status: true,
      }
    }
  },
  { immediate: true }
)

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      formData.value = {
        title: '',
        address: '',
        phone: '',
        email: '',
        whatsapp_number: '',
        social_icons: [],
        twitter: '',
        facebook: '',
        youtube: '',
        linkedin: '',
        copyright: '',
        newsletter_text: '',
        service_links: [],
        quick_links: [],
        company_links: [],
        status: true,
      }
    }
  }
)

// Social Icon Helpers
const addSocialIcon = () => {
  if (!formData.value.social_icons) formData.value.social_icons = []
  formData.value.social_icons.push({ platform: '', url: '', icon: '' })
}

const removeSocialIcon = (index: number) => {
  if (formData.value.social_icons) {
    formData.value.social_icons.splice(index, 1)
  }
}

// Service Links Helpers
const addServiceLink = () => {
  if (!formData.value.service_links) formData.value.service_links = []
  formData.value.service_links.push({ title: '', url: '' })
}

const removeServiceLink = (index: number) => {
  if (formData.value.service_links) {
    formData.value.service_links.splice(index, 1)
  }
}

// Company Links Helpers
const addCompanyLink = () => {
  if (!formData.value.company_links) formData.value.company_links = []
  formData.value.company_links.push({ title: '', url: '' })
}

const removeCompanyLink = (index: number) => {
  if (formData.value.company_links) {
    formData.value.company_links.splice(index, 1)
  }
}

// Quick Links Helpers
const addQuickLink = () => {
  if (!formData.value.quick_links) formData.value.quick_links = []
  formData.value.quick_links.push({ title: '', url: '' })
}

const removeQuickLink = (index: number) => {
  if (formData.value.quick_links) {
    formData.value.quick_links.splice(index, 1)
  }
}

const close = () => emit('close')
const save = () => emit('save', formData.value)
</script>
