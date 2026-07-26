<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <PageBreadcrumb :pageTitle="'Page Settings'" />
      <div class="flex gap-2">
        <button @click="resetSettings"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
          Reset to Defaults
        </button>
        <button @click="saveAll" :disabled="saving"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-brand-600 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-50">
          <svg v-if="saving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
          </svg>
          <span v-else>
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" class="inline mr-1">
              <path d="M2 4L6 8L14 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
            Save All
          </span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="store.loading" class="flex items-center justify-center py-12">
      <div class="h-8 w-8 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
    </div>

    <!-- Settings Grid -->
    <div v-else class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
      <div v-for="item in store.orderedItems" :key="item.id"
        class="group rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
        :class="{
          'opacity-60': !item.enabled,
          'border-l-4 border-l-green-500': item.enabled
        }">
        <!-- Header -->
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
              <i :class="item.icon || 'fas fa-cog'"></i>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ item.section_name }}
              </h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Component: {{ item.component_name }}
              </p>
            </div>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" :checked="item.enabled" @change="toggleItem(item.id, $event)" class="peer sr-only" />
            <div
              class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-brand-500/20 dark:bg-gray-700">
            </div>
          </label>
        </div>

        <!-- Order Controls -->
        <div class="mt-3 flex items-center gap-2">
          <span class="text-xs text-gray-400">Order: {{ item.order }}</span>
          <div class="flex gap-0.5">
            <button @click="moveItem(item.id, -1)" :disabled="item.order <= 1"
              class="rounded p-0.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 disabled:opacity-30 disabled:hover:bg-transparent dark:hover:bg-gray-700">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 12L4 8L8 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </button>
            <button @click="moveItem(item.id, 1)" :disabled="item.order >= store.orderedItems.length"
              class="rounded p-0.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 disabled:opacity-30 disabled:hover:bg-transparent dark:hover:bg-gray-700">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 4L12 8L8 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Section Title & Subtitle -->
        <div class="mt-3 space-y-2">
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
              Section Title
            </label>
            <input v-model="item.section_title" type="text" placeholder="Enter section title"
              class="mt-0.5 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-700 placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
              Section Subtitle
            </label>
            <input v-model="item.section_subtitle" type="text" placeholder="Enter section subtitle"
              class="mt-0.5 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-700 placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
          </div>
        </div>

        <!-- Navbar Settings -->
        <div class="mt-3 border-t border-gray-100 pt-3 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Navbar</span>
              <span class="text-xs text-gray-400">({{ item.nav_label || 'Not set' }})</span>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
              <input type="checkbox" :checked="item.show_in_navbar" @change="toggleNavbar(item.id, $event)"
                :disabled="!item.enabled" class="peer sr-only" />
              <div
                class="peer h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-brand-500/20 peer-disabled:opacity-40 dark:bg-gray-700">
              </div>
            </label>
          </div>
          <div class="mt-1">
            <input v-model="item.nav_label" type="text" placeholder="Navbar label" :disabled="!item.enabled"
              class="w-full rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-700 placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 disabled:opacity-40 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
          </div>
        </div>

        <!-- Footer Settings -->
        <div class="mt-2 flex items-center justify-between">
          <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Show in Footer</span>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" :checked="item.show_in_footer" @change="toggleFooter(item.id, $event)"
              :disabled="!item.enabled" class="peer sr-only" />
            <div
              class="peer h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-brand-500/20 peer-disabled:opacity-40 dark:bg-gray-700">
            </div>
          </label>
        </div>

        <!-- Route Path -->
        <div class="mt-2">
          <input v-model="item.route_path" type="text" placeholder="Route path (e.g. #section-id)"
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-xs text-gray-700 placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
        </div>

        <!-- Status Badge -->
        <div class="absolute bottom-3 right-3">
          <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
            :class="item.enabled ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'">
            {{ item.enabled ? 'Active' : 'Disabled' }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useLandingSettingStore } from '@/stores/landingSettingStore'
import { useToastStore } from '@/stores/toastStore'

const store = useLandingSettingStore()
const toast = useToastStore()
const saving = ref(false)

onMounted(() => {
  store.fetchItems()
})

const toggleItem = async (id: number, event: Event) => {
  const target = event.target as HTMLInputElement
  try {
    await store.toggleItem(id, target.checked)
    toast.success(`Section ${target.checked ? 'enabled' : 'disabled'} successfully`)
  } catch (error) {
    target.checked = !target.checked
    toast.error('Failed to update section status')
  }
}

const toggleNavbar = async (id: number, event: Event) => {
  const target = event.target as HTMLInputElement
  try {
    await store.toggleNavbar(id, target.checked)
    toast.success(`Navbar visibility updated`)
  } catch (error) {
    target.checked = !target.checked
    toast.error('Failed to update navbar visibility')
  }
}

const toggleFooter = async (id: number, event: Event) => {
  const target = event.target as HTMLInputElement
  try {
    await store.toggleFooter(id, target.checked)
    toast.success(`Footer visibility updated`)
  } catch (error) {
    target.checked = !target.checked
    toast.error('Failed to update footer visibility')
  }
}

const moveItem = async (id: number, direction: number) => {
  const current = store.orderedItems.find(item => item.id === id)
  if (!current) return

  const newOrder = current.order + direction
  if (newOrder < 1 || newOrder > store.orderedItems.length) return

  const other = store.orderedItems.find(item => item.order === newOrder)
  if (!other) return

  try {
    await store.bulkUpdate([
      { id: current.id, order: newOrder },
      { id: other.id, order: current.order }
    ])
    toast.success('Order updated successfully')
  } catch (error) {
    toast.error('Failed to update order')
  }
}

const saveAll = async () => {
  saving.value = true
  try {
    const settings = store.orderedItems.map(item => ({
      id: item.id,
      enabled: item.enabled,
      section_title: item.section_title,
      section_subtitle: item.section_subtitle,
      nav_label: item.nav_label,
      route_path: item.route_path,
      show_in_navbar: item.show_in_navbar,
      show_in_footer: item.show_in_footer,
    }))
    await store.bulkUpdate(settings)
    toast.success('All settings saved successfully')
  } catch (error) {
    toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

const resetSettings = async () => {
  if (!confirm('Are you sure you want to reset all landing page settings to defaults? This cannot be undone.')) return
  try {
    await store.resetSettings()
    toast.success('Settings reset to defaults')
  } catch (error) {
    toast.error('Failed to reset settings')
  }
}
</script>
