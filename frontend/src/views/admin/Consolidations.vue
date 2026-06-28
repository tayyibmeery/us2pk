<template>

    <PageBreadcrumb :pageTitle="pageTitle" />
    <div
      class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <!-- <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">Consolidations</h2>
          -->
      <div class="mb-4">
        <input v-model="search" @input="fetchConsolidations" placeholder="Search by Consol ID or AWB..."
          class="h-11 w-full max-w-sm rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
      </div>
        <router-link to="/admin/consolidations/create"
          class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
          + New Consolidation
        </router-link>
      </div>

      <!-- Search -->
      <!-- <div class="mb-4">
        <input v-model="search" @input="fetchConsolidations" placeholder="Search by Consol ID or AWB..."
          class="h-11 w-full max-w-sm rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
      </div> -->

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-3 text-left">Consol ID</th>
              <th class="px-4 py-3 text-left">AWB</th>
              <th class="px-4 py-3 text-left">Warehouse</th>
              <th class="px-4 py-3 text-left">Date Dispatched</th>
              <th class="px-4 py-3 text-right">Total Charges</th>
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
              <td class="px-4 py-2">{{ c.date_dispatched || '—' }}</td>
              <td class="px-4 py-2 text-right">{{ c.total_us2pk_charges?.toLocaleString() }}</td>
              <td class="px-4 py-2 text-right">{{ c.direct_cost?.toLocaleString() }}</td>
              <td class="px-4 py-2 text-right">{{ c.gross_income?.toLocaleString() }}</td>
              <td class="px-4 py-2 text-right">{{ c.roi_percent?.toFixed(0) }}%</td>
              <td class="px-4 py-2 text-center">
                <router-link :to="`/admin/consolidations/${c.id}`"
                  class="text-brand-500 hover:underline">View</router-link>
                <router-link :to="`/admin/consolidations/${c.id}/edit`"
                  class="ml-2 text-blue-600 hover:underline">Edit</router-link>
                <button @click="deleteConsolidation(c)" class="ml-2 text-red-500 hover:underline">Delete</button>
              </td>
            </tr>
            <tr v-if="!consolidations.length && !loading">
              <td colspan="9" class="py-8 text-center text-gray-400">No consolidations found.</td>
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

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'
// import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const pageTitle = ref('Consolidations')
const consolidations = ref<any[]>([])
const meta = ref<any>(null)
const page = ref(1)
const search = ref('')
const loading = ref(false)

async function fetchConsolidations() {
  loading.value = true
  const res = await api.get('/admin/consolidations', { params: { search: search.value, page: page.value } })
  consolidations.value = res.data.data
  meta.value = res.data
  loading.value = false
}

async function deleteConsolidation(c: any) {
  if (!confirm(`Delete ${c.consol_id}?`)) return
  await api.delete(`/admin/consolidations/${c.id}`)
  await fetchConsolidations()
}

onMounted(fetchConsolidations)
</script>
