<template>
  <PageBreadcrumb :pageTitle="pageTitle" />
  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
      <div class="mb-4 md:mb-0">
        <input v-model="search" @input="fetchConsolidations" placeholder="Search by Consol ID or AWB..."
          class="h-11 w-full max-w-sm rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
      </div>
      <router-link to="/admin/consolidations/create"
        class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
        + New Consolidation
      </router-link>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-800">
          <tr>
            <th class="px-4 py-3 text-left">Consol ID</th>
            <th class="px-4 py-3 text-left">AWB</th>
            <th class="px-4 py-3 text-left">Warehouse</th>
            <th class="px-4 py-3 text-right">Total Charges</th>
            <th class="px-4 py-3 text-right">Customs Duty</th>
            <th class="px-4 py-3 text-right">Other Charges</th>
            <th class="px-4 py-3 text-right">Direct Costs</th>
            <th class="px-4 py-3 text-right">Gross Income</th>
            <th class="px-4 py-3 text-right">ROI %</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in consolidations" :key="c.id" class="border-b hover:bg-gray-50 dark:border-gray-700">
            <td class="px-4 py-2 font-mono">{{ c.consol_id }}</td>
            <td class="px-4 py-2">{{ c.awb_number || '—' }}</td>
            <td class="px-4 py-2">{{ c.warehouse?.name || '—' }}</td>
            <td class="px-4 py-2 text-right">{{ c.total_us2pk_charges?.toLocaleString() }}</td>

            <!-- Customs Duty cell -->
            <td class="px-4 py-2 text-right">
              <span v-if="isCustomsMissing(c)"
                class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400"
                title="Customs Duty has not been entered yet. Update the consolidation after it reaches your office.">
                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                Not set
              </span>
              <span v-else class="font-medium text-gray-800 dark:text-white/90">
                {{ Number(c.customs_duty).toLocaleString() }}
              </span>
            </td>

            <!-- Other Charges cell -->
            <td class="px-4 py-2 text-right font-medium text-gray-800 dark:text-white/90">
              {{ computeOtherCharges(c).toLocaleString() }}
            </td>

            <td class="px-4 py-2 text-right">{{ c.direct_cost?.toLocaleString() }}</td>
            <td class="px-4 py-2 text-right">{{ c.gross_income?.toLocaleString() }}</td>
            <td class="px-4 py-2 text-right">{{ c.roi_percent?.toFixed(0) }}%</td>

            <!-- ✅ Icon-based actions (matching Shipments table style) -->
            <td class="px-4 py-2">
              <div class="flex items-center justify-end gap-0.5">

                <!-- View -->
                <router-link :to="`/admin/consolidations/${c.id}`"
                  class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
                  title="View">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                </router-link>

                <!-- Edit — highlighted in AMBER with pulse when Customs Duty is missing -->
                <router-link :to="`/admin/consolidations/${c.id}/edit`"
                  class="flex h-8 w-8 items-center justify-center rounded-lg transition"
                  :class="isCustomsMissing(c)
                    ? 'bg-amber-50 text-amber-600 ring-2 ring-amber-300 animate-pulse hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-700'
                    : 'text-gray-400 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-500/10 dark:hover:text-brand-400'" :title="isCustomsMissing(c)
                      ? 'Customs Duty is missing — click Edit to add it after arrival'
                      : 'Edit'">
                  <svg width="16" height="16" viewBox="0 0 21 21" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M17.0911 3.53206C16.2124 2.65338 14.7878 2.65338 13.9091 3.53206L5.6074 11.8337C5.29899 12.1421 5.08687 12.5335 4.99684 12.9603L4.26177 16.445C4.20943 16.6931 4.286 16.9508 4.46529 17.1301C4.64458 17.3094 4.90232 17.3859 5.15042 17.3336L8.63507 16.5985C9.06184 16.5085 9.45324 16.2964 9.76165 15.988L18.0633 7.68631C18.942 6.80763 18.942 5.38301 18.0633 4.50433L17.0911 3.53206ZM14.9697 4.59272C15.2626 4.29982 15.7375 4.29982 16.0304 4.59272L17.0027 5.56499C17.2956 5.85788 17.2956 6.33276 17.0027 6.62565L16.1043 7.52402L14.0714 5.49109L14.9697 4.59272ZM13.0107 6.55175L6.66806 12.8944C6.56526 12.9972 6.49455 13.1277 6.46454 13.2699L5.96704 15.6283L8.32547 15.1308C8.46772 15.1008 8.59819 15.0301 8.70099 14.9273L15.0436 8.58468L13.0107 6.55175Z"
                      fill="currentColor" />
                  </svg>
                </router-link>

                <!-- Delete -->
                <button @click="openDeleteModal(c)"
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
          <tr v-if="!consolidations.length && !loading">
            <td colspan="10" class="py-8 text-center text-gray-400">No consolidations found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="meta" class="mt-4 flex items-center justify-between text-sm text-gray-600">
      <span>Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}</span>
      <div class="flex gap-2">
        <button @click="page--; fetchConsolidations()" :disabled="page <= 1"
          class="rounded border px-3 py-1 disabled:opacity-30">Prev</button>
        <button @click="page++; fetchConsolidations()" :disabled="page >= meta.last_page"
          class="rounded border px-3 py-1 disabled:opacity-30">Next</button>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <ConfirmationModal :isOpen="deleteModalOpen" :title="'Delete Consolidation'"
    :message="`Are you sure you want to delete consolidation ${deleteItem?.consol_id || ''}? This action cannot be undone.`"
    :loading="deleting" @confirm="confirmDelete" @cancel="cancelDelete" />
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmationModal from '@/components/common/ConfirmationModal.vue'

const pageTitle = ref('Consolidations')
const consolidations = ref<any[]>([])
const meta = ref<any>(null)
const page = ref(1)
const search = ref('')
const loading = ref(false)
const toast = useToast()

// Delete confirmation modal
const deleteModalOpen = ref(false)
const deleting = ref(false)
const deleteItem = ref<any>(null)
let isDeleting = false

// ✅ Customs Duty missing when 0, null, or NaN
function isCustomsMissing(c: any): boolean {
  const val = Number(c?.customs_duty ?? 0)
  return isNaN(val) || val === 0
}

// ✅ Other Charges = Warehouse + Sales Tax + Income Tax + CAA
function computeOtherCharges(c: any): number {
  return (
    Number(c?.ware_house_charges ?? 0) +
    Number(c?.sales_tax ?? 0) +
    Number(c?.income_tax ?? 0) +
    Number(c?.caa_charges ?? 0)
  )
}

async function fetchConsolidations() {
  loading.value = true
  try {
    const res = await api.get('/admin/consolidations', {
      params: { search: search.value, page: page.value }
    })
    consolidations.value = res.data.data
    meta.value = res.data
  } catch (err: any) {
    toast.error('❌ ' + (err.response?.data?.message || 'Failed to fetch consolidations'))
  } finally {
    loading.value = false
  }
}

// Delete functions
const openDeleteModal = (item: any) => {
  deleteItem.value = item
  deleteModalOpen.value = true
}

const cancelDelete = () => {
  deleteModalOpen.value = false
  deleteItem.value = null
}

const confirmDelete = async () => {
  if (!deleteItem.value || isDeleting) return

  isDeleting = true
  deleting.value = true
  deleteModalOpen.value = false

  try {
    await api.delete(`/admin/consolidations/${deleteItem.value.id}`)
    await fetchConsolidations()
    await nextTick()
    setTimeout(() => {
      toast.success('🗑️ Consolidation deleted successfully!')
    }, 100)
  } catch (err: any) {
    const msg = err.response?.data?.message || 'Failed to delete consolidation'
    await nextTick()
    setTimeout(() => {
      toast.error('❌ ' + msg)
    }, 100)
  } finally {
    deleting.value = false
    isDeleting = false
    deleteItem.value = null
  }
}

onMounted(fetchConsolidations)
</script>
