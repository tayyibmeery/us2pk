<template>
  <PageBreadcrumb :pageTitle="pageTitle" />
  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <div v-if="loading" class="text-center py-10 text-gray-400">Loading...</div>
    <div v-else-if="!consolidation" class="text-center py-10 text-gray-400">Consolidation not found.</div>
    <div v-else>
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ consolidation.consol_id }}</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            AWB: {{ consolidation.awb_number || '—' }} &bull;
            {{ consolidation.warehouse?.name || '—' }} &bull;
            {{ consolidation.international_courier?.name || '—' }}
          </p>
        </div>
        <div class="flex gap-2">
          <router-link :to="`/admin/consolidations/${consolidation.id}/edit`"
            class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
            Edit
          </router-link>
          <button @click="goBack"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition">
            Back
          </button>
        </div>
      </div>

      <!-- Summary Cards (Overview) -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Shipments</p>
          <p class="text-xl font-bold text-gray-800 dark:text-white/90">{{ consolidation.shipments?.length || 0 }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Weight</p>
          <p class="text-xl font-bold text-gray-800 dark:text-white/90">
            {{ (Number(consolidation.total_weight_kg) || 0).toFixed(2) }} kg</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Revenue (US2PK)</p>
          <p class="text-xl font-bold text-gray-800 dark:text-white/90">PKR
            {{ (Number(consolidation.total_us2pk_charges) || 0).toLocaleString() }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Net Receivable from Courier</p>
          <p class="text-xl font-bold text-green-600 dark:text-green-400">PKR
            {{ (Number(consolidation.receivable_from_courier) || 0).toLocaleString() }}</p>
        </div>
      </div>

      <!-- Main Two‑Column Layout -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Left: Consolidation Details & Courier Receivable -->
        <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
          <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            📦 Consolidation Details
          </h3>
          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Warehouse</span>
              <span class="text-gray-800 dark:text-white/90">{{ consolidation.warehouse?.name || '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">International Courier</span>
              <span
                class="text-gray-800 dark:text-white/90">{{ consolidation.international_courier?.name || '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">AWB Number</span>
              <span class="font-mono text-gray-800 dark:text-white/90">{{ consolidation.awb_number || '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Date Departed</span>
              <span class="text-gray-800 dark:text-white/90">{{ formatDate(consolidation.date_departed) || '—' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Date Reached</span>
              <span class="text-gray-800 dark:text-white/90">{{ formatDate(consolidation.date_reached) || '—' }}</span>
            </div>
          </div>

          <!-- Courier Receivable Breakdown -->
          <h3 class="mt-6 mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            💰 Courier Receivable
          </h3>
          <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
            <div class="flex justify-between text-sm border-b border-blue-200 dark:border-blue-800/30 py-1">
              <span class="font-medium text-gray-600 dark:text-gray-400">Gross COD Collected</span>
              <span class="text-gray-800 dark:text-white/90">PKR
                {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.receivable_cod) || 0), 0).toLocaleString()}}</span>
            </div>
            <div class="flex justify-between text-sm border-b border-blue-200 dark:border-blue-800/30 py-1">
              <span class="font-medium text-gray-600 dark:text-gray-400">Less: Local Delivery Charges</span>
              <span class="text-red-600 dark:text-red-400">- PKR
                {{ (Number(consolidation.courier_charges) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold pt-2">
              <span class="text-gray-800 dark:text-white/90">Net Receivable</span>
              <span class="text-green-700 dark:text-green-400">PKR
                {{ (Number(consolidation.receivable_from_courier) || 0).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <!-- Right: Costs & Profitability -->
        <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
          <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            📊 Costs & Profitability
          </h3>
          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Total US2PK Revenue</span>
              <span class="font-bold text-gray-800 dark:text-white/90">PKR
                {{ (Number(consolidation.total_us2pk_charges) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Local Delivery Charges</span>
              <span class="text-gray-800 dark:text-white/90">PKR
                {{ (Number(consolidation.courier_charges) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Warehouse Charges</span>
              <span class="text-gray-800 dark:text-white/90">PKR
                {{ (Number(consolidation.ware_house_charges) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Import Taxes</span>
              <span class="text-gray-800 dark:text-white/90">PKR
                {{ (Number(consolidation.import_taxes) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50 font-semibold">
              <span class="text-gray-700 dark:text-gray-300">Direct Costs (Total)</span>
              <span class="text-gray-800 dark:text-white/90">PKR
                {{ (Number(consolidation.direct_costs) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between py-1 text-base font-bold">
              <span class="text-gray-800 dark:text-white/90">Gross Income</span>
              <span
                :class="(Number(consolidation.gross_income) || 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                PKR {{ (Number(consolidation.gross_income) || 0).toLocaleString() }}
              </span>
            </div>
            <div class="flex justify-between py-1 text-sm">
              <span class="font-medium text-gray-500 dark:text-gray-400">ROI</span>
              <span class="font-bold"
                :class="(Number(consolidation.roi_percent) || 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                {{ (Number(consolidation.roi_percent) || 0).toFixed(0) }}%
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Taxes Breakdown & Sales Tax Liability -->
      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
          <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            📋 Import Taxes Breakdown
          </h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Customs Duty</span>
              <span>PKR {{ (Number(consolidation.customs_duty) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Sales Tax</span>
              <span>PKR {{ (Number(consolidation.sales_tax) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Income Tax</span>
              <span>PKR {{ (Number(consolidation.income_tax) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">CAA Charges</span>
              <span>PKR {{ (Number(consolidation.caa_charges) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between font-bold pt-2 border-t border-gray-200 dark:border-gray-600">
              <span>Total Import Taxes</span>
              <span>PKR {{ (Number(consolidation.import_taxes) || 0).toLocaleString() }}</span>
            </div>
          </div>
        </div>
        <!-- <div class="rounded-xl border border-gray-200 p-5 dark:border-gray-700">
          <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            🧾 Sales Tax Liability
          </h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Output Sales Tax</span>
              <span>PKR {{ (Number(consolidation.output_sales_tax) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1 dark:border-gray-700/50">
              <span class="font-medium text-gray-500 dark:text-gray-400">Less: Input Tax</span>
              <span>PKR {{ (Number(consolidation.sales_tax) || 0).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between font-bold pt-2 border-t border-gray-200 dark:border-gray-600">
              <span>Net ST Payable</span>
              <span :class="(Number(consolidation.net_st_payable) || 0) >= 0 ? 'text-red-600' : 'text-green-600'">
                PKR {{ (Number(consolidation.net_st_payable) || 0).toLocaleString() }}
              </span>
            </div>
          </div>
        </div> -->
      </div>

      <!-- Shipments Table -->
      <div class="mt-6">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">📦 Shipments</h3>
          <span class="text-sm text-gray-500">{{ consolidation.shipments?.length || 0 }} items</span>
        </div>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800">
              <tr>
                <th
                  class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Code</th>
                <th
                  class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Customer</th>
                <th
                  class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  City</th>
                <th
                  class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Arrival</th>
                <th
                  class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Item</th>
                <th
                  class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  KGs</th>
                <th
                  class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  US2PK</th>
                <th
                  class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Amount Due</th>
                <th
                  class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Receivable COD</th>
                <th
                  class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Local Courier</th>
                <th
                  class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Del. Charges</th>
                <th
                  class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Output Tax</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900/50">
              <tr v-for="s in consolidation.shipments" :key="s.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <td class="px-3 py-2 font-mono text-sm">{{ s.shipment_code || '—' }}</td>
                <td class="px-3 py-2 text-sm">{{ s.user?.name || '—' }}</td>
                <td class="px-3 py-2 text-sm">{{ s.user?.city?.city_name || '—' }}</td>
                <td class="px-3 py-2 text-sm">{{ formatDate(s.arrival_date) || '—' }}</td>
                <td class="px-3 py-2 text-sm max-w-xs truncate" :title="s.description">
                  {{ truncateWords(s.description, 10) || '—' }}</td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(s.weight_kgs) || 0).toFixed(2) }}</td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(s.company_charges) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(s.amount_due) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-right text-sm font-medium">
                  {{ (Number(s.receivable_cod) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-sm">{{ s.local_courier?.name || '—' }}</td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(s.delivery_charges) || 0).toFixed(2) }}</td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(s.output_tax) || 0).toFixed(2) }}</td>
              </tr>
              <!-- Totals Row -->
              <tr class="bg-gray-100 dark:bg-gray-800 font-bold">
                <td colspan="5" class="px-3 py-2 text-right text-sm">Totals</td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(consolidation.total_weight_kg) || 0).toFixed(2) }}
                </td>
                <td class="px-3 py-2 text-right text-sm">
                  {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.company_charges) || 0), 0).toLocaleString()}}
                </td>
                <td class="px-3 py-2 text-right text-sm">
                  {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.amount_due) || 0), 0).toLocaleString()}}
                </td>
                <td class="px-3 py-2 text-right text-sm">
                  {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.receivable_cod) || 0), 0).toLocaleString()}}
                </td>
                <td class="px-3 py-2 text-sm"></td> <!-- Local Courier placeholder -->
                <td class="px-3 py-2 text-right text-sm">{{ (Number(consolidation.courier_charges) || 0).toFixed(2) }}
                </td>
                <td class="px-3 py-2 text-right text-sm">{{ (Number(consolidation.output_sales_tax) || 0).toFixed(2) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const consolidation = ref<any>(null)
const pageTitle = ref('Consolidation Details')

async function fetchConsolidation() {
  loading.value = true
  try {
    const res = await api.get(`/admin/consolidations/${route.params.id}`)
    consolidation.value = res.data
    pageTitle.value = `${consolidation.value.consol_id} - Details`
  } catch (err) {
    console.error(err)
    consolidation.value = null
  } finally {
    loading.value = false
  }
}

function goBack() {
  router.push('/admin/consolidations')
}

onMounted(fetchConsolidation)
</script>
