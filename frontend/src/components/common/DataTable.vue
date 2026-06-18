<template>
  <div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.06] dark:bg-gray-900">

    <!-- Header -->
    <div
      class="flex flex-col gap-4 border-b border-gray-100 px-6 py-4 dark:border-white/[0.06] sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ title }}</h2>
        <p v-if="store.pagination" class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
          {{ store.pagination.total }} total records
        </p>
      </div>

      <!-- Controls row -->
      <div class="flex flex-wrap items-center gap-3">
        <!-- Per-page -->
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Rows:</span>
          <select :value="perPage" @change="onPerPageChange"
            class="h-9 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-sm text-gray-700 shadow-sm transition focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-white/[0.08] dark:bg-gray-800 dark:text-gray-200">
            <option v-for="n in [5, 8, 10, 25, 50]" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <!-- Search -->
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"
                fill="currentColor" />
            </svg>
          </span>
          <input v-model="search" @input="onSearchInput" type="text" placeholder="Search..."
            class="h-9 w-56 rounded-lg border border-gray-200 bg-white py-1.5 pl-9 pr-4 text-sm text-gray-700 shadow-sm transition placeholder:text-gray-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-white/[0.08] dark:bg-gray-800 dark:text-gray-200 dark:placeholder:text-gray-500" />
          <!-- Clear search -->
          <button v-if="search" @click="clearSearch"
            class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M6.22 6.22a.75.75 0 011.06 0L12 10.94l4.72-4.72a.75.75 0 111.06 1.06L13.06 12l4.72 4.72a.75.75 0 11-1.06 1.06L12 13.06l-4.72 4.72a.75.75 0 01-1.06-1.06L10.94 12 6.22 7.28a.75.75 0 010-1.06z"
                fill="currentColor" />
            </svg>
          </button>
        </div>

        <!-- Add button -->
        <button @click="openCreateModal"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-brand-600 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700 active:scale-[0.98] whitespace-nowrap">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M8 2.5a.75.75 0 01.75.75v4h4a.75.75 0 010 1.5h-4v4a.75.75 0 01-1.5 0v-4h-4a.75.75 0 010-1.5h4v-4A.75.75 0 018 2.5z"
              fill="currentColor" />
          </svg>
          {{ addButtonLabel || 'Add' }}
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 dark:bg-white/[0.02]">
            <th v-for="col in columns" :key="col.key"
              class="border-b border-gray-100 px-5 py-3 text-left dark:border-white/[0.06]"
              :class="col.sortable !== false ? 'cursor-pointer select-none group' : ''"
              @click="col.sortable !== false && onSort(col.key)">
              <div class="flex items-center gap-1.5">
                <span
                  class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200 transition-colors">
                  {{ col.label }}
                </span>
                <span v-if="col.sortable !== false"
                  class="flex flex-col gap-0.5 opacity-40 group-hover:opacity-70 transition-opacity"
                  :class="{ 'opacity-100 !text-brand-500': sortByKey === col.key }">
                  <svg width="8" height="4" viewBox="0 0 8 4" fill="none"
                    :class="sortByKey === col.key && sortOrder === 'asc' ? 'fill-brand-500' : 'fill-gray-400 dark:fill-gray-600'">
                    <path d="M4 0L7.46 4H0.54L4 0Z" fill="currentColor" />
                  </svg>
                  <svg width="8" height="4" viewBox="0 0 8 4" fill="none"
                    :class="sortByKey === col.key && sortOrder === 'desc' ? 'fill-brand-500' : 'fill-gray-400 dark:fill-gray-600'">
                    <path d="M4 4L0.54 0H7.46L4 4Z" fill="currentColor" />
                  </svg>
                </span>
              </div>
            </th>
            <th class="border-b border-gray-100 px-5 py-3 text-right dark:border-white/[0.06]">
              <span
                class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</span>
            </th>
          </tr>
        </thead>

        <tbody>
          <!-- Loading skeleton -->
          <template v-if="store.loading">
            <tr v-for="i in (store.pagination?.per_page || perPage)" :key="i"
              class="border-b border-gray-50 last:border-0 dark:border-white/[0.04]">
              <td v-for="col in columns" :key="col.key" class="px-5 py-3.5">
                <div class="h-4 w-3/4 animate-pulse rounded bg-gray-100 dark:bg-white/[0.05]"></div>
              </td>
              <td class="px-5 py-3.5">
                <div class="ml-auto h-4 w-16 animate-pulse rounded bg-gray-100 dark:bg-white/[0.05]"></div>
              </td>
            </tr>
          </template>

          <!-- Empty state -->
          <tr v-else-if="!store.items.length">
            <td :colspan="columns.length + 1" class="px-5 py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-white/[0.04]">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-gray-400">
                    <path
                      d="M3 7C3 5.9 3.9 5 5 5H19C20.1 5 21 5.9 21 7V17C21 18.1 20.1 19 19 19H5C3.9 19 3 18.1 3 17V7Z"
                      stroke="currentColor" stroke-width="1.5" fill="none" />
                    <path d="M3 9H21" stroke="currentColor" stroke-width="1.5" />
                  </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No records found</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                  {{ search ? `No results for "${search}"` : 'Add your first entry to get started.' }}
                </p>
              </div>
            </td>
          </tr>

          <!-- Data rows -->
          <tr v-else v-for="item in store.items" :key="item.id"
            class="group border-b border-gray-50 last:border-0 transition-colors hover:bg-gray-50/70 dark:border-white/[0.04] dark:hover:bg-white/[0.02]">
            <td v-for="col in columns" :key="col.key" class="px-5 py-3.5">
              <!-- Status badge auto-detection -->
              <template v-if="col.key === 'status'">
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium" :class="item[col.key]
                  ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400'
                  : 'bg-gray-100 text-gray-500 dark:bg-white/[0.06] dark:text-gray-400'">
                  <span class="h-1.5 w-1.5 rounded-full" :class="item[col.key] ? 'bg-green-500' : 'bg-gray-400'"></span>
                  {{ col.format ? col.format(item[col.key]) : (item[col.key] ? 'Active' : 'Inactive') }}
                </span>
              </template>
              <template v-else>
                <span class="text-sm text-gray-700 dark:text-gray-300">
                  {{ col.format ? col.format(item[col.key]) : item[col.key] }}
                </span>
              </template>
            </td>

            <!-- Actions -->
            <!-- Actions cell -->
            <td class="px-5 py-3.5">
              <div class="flex items-center justify-end gap-0.5">
                <!-- Edit -->
                <button @click="openEditModal(item)"
                  class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
                  title="Edit">
                  <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M17.0911 3.53206C16.2124 2.65338 14.7878 2.65338 13.9091 3.53206L5.6074 11.8337C5.29899 12.1421 5.08687 12.5335 4.99684 12.9603L4.26177 16.445C4.20943 16.6931 4.286 16.9508 4.46529 17.1301C4.64458 17.3094 4.90232 17.3859 5.15042 17.3336L8.63507 16.5985C9.06184 16.5085 9.45324 16.2964 9.76165 15.988L18.0633 7.68631C18.942 6.80763 18.942 5.38301 18.0633 4.50433L17.0911 3.53206ZM14.9697 4.59272C15.2626 4.29982 15.7375 4.29982 16.0304 4.59272L17.0027 5.56499C17.2956 5.85788 17.2956 6.33276 17.0027 6.62565L16.1043 7.52402L14.0714 5.49109L14.9697 4.59272ZM13.0107 6.55175L6.66806 12.8944C6.56526 12.9972 6.49455 13.1277 6.46454 13.2699L5.96704 15.6283L8.32547 15.1308C8.46772 15.1008 8.59819 15.0301 8.70099 14.9273L15.0436 8.58468L13.0107 6.55175Z"
                      fill="currentColor" />
                  </svg>
                </button>

                <!-- Delete -->
                <button @click="deleteItem(item.id)"
                  class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                  title="Delete">
                  <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M7.04142 4.29199C7.04142 3.04935 8.04878 2.04199 9.29142 2.04199H11.7081C12.9507 2.04199 13.9581 3.04935 13.9581 4.29199V4.54199H16.1252H17.166C17.5802 4.54199 17.916 4.87778 17.916 5.29199C17.916 5.70621 17.5802 6.04199 17.166 6.04199H16.8752V8.74687V13.7469V16.7087C16.8752 17.9513 15.8678 18.9587 14.6252 18.9587H6.37516C5.13252 18.9587 4.12516 17.9513 4.12516 16.7087V13.7469V8.74687V6.04199H3.8335C3.41928 6.04199 3.0835 5.70621 3.0835 5.29199C3.0835 4.87778 3.41928 4.54199 3.8335 4.54199H4.87516H7.04142V4.29199ZM15.3752 13.7469V8.74687V6.04199H13.9581H13.2081H7.79142H7.04142H5.62516V8.74687V13.7469V16.7087C5.62516 17.1229 5.96095 17.4587 6.37516 17.4587H14.6252C15.0394 17.4587 15.3752 17.1229 15.3752 16.7087V13.7469ZM8.54142 4.54199H12.4581V4.29199C12.4581 3.87778 12.1223 3.54199 11.7081 3.54199H9.29142C8.87721 3.54199 8.54142 3.87778 8.54142 4.29199V4.54199ZM8.8335 8.50033C9.24771 8.50033 9.5835 8.83611 9.5835 9.25033V14.2503C9.5835 14.6645 9.24771 15.0003 8.8335 15.0003C8.41928 15.0003 8.0835 14.6645 8.0835 14.2503V9.25033C8.0835 8.83611 8.41928 8.50033 8.8335 8.50033ZM12.9168 9.25033C12.9168 8.83611 12.581 8.50033 12.1668 8.50033C11.7526 8.50033 11.4168 8.83611 11.4168 9.25033V14.2503C11.4168 14.6645 11.7526 15.0003 12.1668 15.0003C12.581 15.0003 12.9168 14.6645 12.9168 14.2503V9.25033Z"
                      fill="currentColor" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination footer -->
    <div
      class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 px-6 py-4 dark:border-white/[0.06] sm:flex-row">
      <p class="text-xs text-gray-400 dark:text-gray-500">
        Showing
        <span class="font-medium text-gray-600 dark:text-gray-300">{{ store.pagination?.from || 0 }}</span>
        –
        <span class="font-medium text-gray-600 dark:text-gray-300">{{ store.pagination?.to || 0 }}</span>
        of
        <span class="font-medium text-gray-600 dark:text-gray-300">{{ store.pagination?.total || 0 }}</span>
        results
      </p>

      <div class="flex items-center gap-1">
        <!-- Prev -->
        <button @click="changePage((store.pagination?.current_page ?? 1) - 1)"
          :disabled="!store.pagination?.prev_page_url"
          class="flex h-8 items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/[0.08] dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.06]">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
          Prev
        </button>

        <!-- Page numbers -->
        <button v-for="page in pageNumbers" :key="page" @click="changePage(page)"
          class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-medium transition" :class="page === store.pagination?.current_page
            ? 'bg-brand-600 text-white shadow-sm'
            : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/[0.06]'">
          {{ page }}
        </button>

        <!-- Next -->
        <button @click="changePage((store.pagination?.current_page ?? 1) + 1)"
          :disabled="!store.pagination?.next_page_url"
          class="flex h-8 items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-600 shadow-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/[0.08] dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.06]">
          Next
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <component :is="modalComponent" :isOpen="modalOpen" :initialData="editingItem" @close="modalOpen = false"
    @save="handleSave" v-bind="modalProps" />

  <!-- Error Toast -->
  <Teleport to="body">
    <Transition enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-2 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-2 scale-95">
      <div v-if="errorModalOpen"
        class="fixed bottom-6 right-6 z-50 flex w-full max-w-sm items-start gap-4 rounded-2xl border border-red-100 bg-white p-4 shadow-xl dark:border-red-500/20 dark:bg-gray-900">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-red-500">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 4.5a.75.75 0 01.75.75v5a.75.75 0 01-1.5 0v-5a.75.75 0 01.75-.75zm0 10a1 1 0 100-2 1 1 0 000 2z"
              fill="currentColor" />
          </svg>
        </div>
        <div class="flex-1 pt-0.5">
          <p class="text-sm font-semibold text-gray-800 dark:text-white">Error</p>
          <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ errorMessage }}</p>
          <!-- Progress -->
          <div class="mt-3 h-0.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
            <div class="h-full bg-red-400 transition-all ease-linear dark:bg-red-500"
              :style="{ width: errorProgress + '%', transitionDuration: '50ms' }"></div>
          </div>
        </div>
        <button @click="closeErrorModal"
          class="shrink-0 text-gray-300 transition hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M6.22 6.22a.75.75 0 011.06 0L12 10.94l4.72-4.72a.75.75 0 111.06 1.06L13.06 12l4.72 4.72a.75.75 0 11-1.06 1.06L12 13.06l-4.72 4.72a.75.75 0 01-1.06-1.06L10.94 12 6.22 7.28a.75.75 0 010-1.06z"
              fill="currentColor" />
          </svg>
        </button>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch, onUnmounted } from 'vue'
import type { ColumnDefinition } from '@/types/table'

const props = defineProps<{
  store: any
  columns: ColumnDefinition[]
  title?: string
  addButtonLabel?: string
  modalComponent: any
  modalProps?: Record<string, any>
}>()

// ── Table state ──────────────────────────────────────────────────────────────
const search = ref<string>(props.store.search ?? '')
const perPage = ref<number>(Number(props.store.perPage ?? 10))
const sortByKey = ref<string>(props.store.sortBy ?? 'id')
const sortOrder = ref<'asc' | 'desc'>(props.store.sortOrder ?? 'asc')

const modalOpen = ref(false)
const editingItem = ref<any>(null)

// ── Error toast ───────────────────────────────────────────────────────────────
const errorModalOpen = ref(false)
const errorMessage = ref('')
const errorProgress = ref(0)
let errorTimer: ReturnType<typeof setTimeout> | null = null
let progressTimer: ReturnType<typeof setInterval> | null = null

// ── Sync store → local state ──────────────────────────────────────────────────
watch(() => props.store.sortBy, (v) => { sortByKey.value = v }, { immediate: true })
watch(() => props.store.sortOrder, (v) => { sortOrder.value = v }, { immediate: true })
watch(() => props.store.perPage, (v) => { perPage.value = Number(v) }, { immediate: true })

// Watch store errors
watch(() => props.store.error, (err) => {
  if (err) {
    showError(err)
    props.store.error = null
  }
})

// ── Pagination ────────────────────────────────────────────────────────────────
const pageNumbers = computed(() => {
  const total = props.store.pagination?.last_page ?? 1
  const current = props.store.pagination?.current_page ?? 1
  const delta = 2
  const start = Math.max(1, current - delta)
  const end = Math.min(total, current + delta)
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

const changePage = (page: number) => {
  const last = props.store.pagination?.last_page ?? 1
  if (page < 1 || page > last) return
  props.store.fetchItems(page)
}

// ── Search (debounced, with clear) ────────────────────────────────────────────
let searchTimer: ReturnType<typeof setTimeout> | null = null

const onSearchInput = () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    props.store.setSearch(search.value)
  }, 350)
}

const clearSearch = () => {
  search.value = ''
  props.store.setSearch('')
}

// ── Per-page ──────────────────────────────────────────────────────────────────
// FIX: cast to Number so store receives a number, not a string from <select>
const onPerPageChange = (event: Event) => {
  const val = Number((event.target as HTMLSelectElement).value)
  perPage.value = val
  props.store.setPerPage(val)
}

// ── Sort ──────────────────────────────────────────────────────────────────────
const onSort = (key: string) => {
  props.store.setSort(key)
}

// ── Modal ─────────────────────────────────────────────────────────────────────
const openCreateModal = () => {
  editingItem.value = null
  modalOpen.value = true
}

const openEditModal = (item: any) => {
  editingItem.value = { ...item }
  modalOpen.value = true
}

const handleSave = async (data: any) => {
  try {
    if (editingItem.value?.id) {
      await props.store.update(editingItem.value.id, data)
    } else {
      await props.store.create(data)
    }
    modalOpen.value = false
    props.store.fetchItems(props.store.pagination?.current_page ?? 1)
  } catch (err: any) {
    const msg = err?.response?.data?.message || err?.message || 'Save failed'
    showError(msg)
  }
}

const deleteItem = async (id: number) => {
  if (!confirm('Delete this record? This action cannot be undone.')) return
  try {
    await props.store.delete(id)
    props.store.fetchItems(props.store.pagination?.current_page ?? 1)
  } catch (err: any) {
    const msg = err?.response?.data?.message || err?.message || 'Delete failed'
    showError(msg)
  }
}

// ── Error toast ───────────────────────────────────────────────────────────────
const showError = (message: string) => {
  clearError()
  errorMessage.value = message || 'An unexpected error occurred.'
  errorModalOpen.value = true
  errorProgress.value = 0

  const duration = 5000
  const step = 50
  const totalSteps = duration / step
  let steps = 0

  progressTimer = setInterval(() => {
    steps++
    errorProgress.value = Math.min(100, (steps / totalSteps) * 100)
    if (steps >= totalSteps) clearInterval(progressTimer!)
  }, step)

  errorTimer = setTimeout(closeErrorModal, duration)
}

const clearError = () => {
  if (errorTimer) { clearTimeout(errorTimer); errorTimer = null }
  if (progressTimer) { clearInterval(progressTimer); progressTimer = null }
}

const closeErrorModal = () => {
  clearError()
  errorModalOpen.value = false
  errorMessage.value = ''
  errorProgress.value = 0
}

onUnmounted(() => {
  clearError()
  if (searchTimer) clearTimeout(searchTimer)
})
</script>
