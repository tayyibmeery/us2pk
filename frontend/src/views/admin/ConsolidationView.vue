<template>
  <PageBreadcrumb :pageTitle="pageTitle" />
  <div
    class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
    <div v-if="loading" class="text-center py-10 text-gray-400">Loading...</div>
    <div v-else-if="!consolidation" class="text-center py-10 text-gray-400">Consolidation not found.</div>
    <div v-else>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ consolidation.consol_id }}</h2>
        <div class="flex gap-2">
          <router-link :to="`/admin/consolidations/${consolidation.id}/edit`"
            class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">Edit</router-link>
          <button @click="goBack"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back</button>
        </div>
      </div>

      <!-- Details & Costs -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
          <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Consolidation Details</h3>
          <div class="space-y-2 text-sm">
            <div><span class="font-medium">Warehouse:</span> {{ consolidation.warehouse?.name || '—' }}</div>
            <div><span class="font-medium">Courier:</span> {{ consolidation.international_courier?.name || '—' }}</div>
            <div><span class="font-medium">Date Departed:</span> {{ consolidation.date_departed || '—' }}</div>
            <div><span class="font-medium">Date Reached:</span> {{ consolidation.date_reached || '—' }}</div>
            <div><span class="font-medium">AWB Number:</span> {{ consolidation.awb_number || '—' }}</div>
            <div><span class="font-medium">Date Dispatched:</span> {{ consolidation.date_dispatched || '—' }}</div>
            <div><span class="font-medium">Receivable from Courier:</span>
              {{ Number(consolidation.receivable_from_courier || 0).toLocaleString() }}
            </div>
          </div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
          <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Costs</h3>
          <div class="space-y-2 text-sm">
            <div><span class="font-medium">Total US2PK Charges:</span>
              {{ Number(consolidation.total_us2pk_charges || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">100%</span>
            </div>
            <div><span class="font-medium">Courier Charges:</span>
              {{ Number(consolidation.courier_charges || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">
                {{ ((Number(consolidation.courier_charges) / Number(consolidation.total_us2pk_charges || 1)) * 100).toFixed(2) }}%
              </span>
            </div>
            <div><span class="font-medium">Warehouse Charges:</span>
              {{ Number(consolidation.ware_house_charges || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">
                {{ ((Number(consolidation.ware_house_charges) / Number(consolidation.total_us2pk_charges || 1)) * 100).toFixed(2) }}%
              </span>
            </div>
            <div><span class="font-medium">Import Taxes:</span>
              {{ Number(consolidation.import_taxes || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">
                {{ ((Number(consolidation.import_taxes) / Number(consolidation.total_us2pk_charges || 1)) * 100).toFixed(2) }}%
              </span>
            </div>
            <div><span class="font-medium">Net ST Payable:</span>
              {{ Number(consolidation.net_st_payable || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">
                {{ ((Number(consolidation.net_st_payable) / Number(consolidation.total_us2pk_charges || 1)) * 100).toFixed(2) }}%
              </span>
            </div>
            <div><span class="font-medium">Direct Cost:</span>
              {{ Number(consolidation.direct_cost || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">
                {{ ((Number(consolidation.direct_cost) / Number(consolidation.total_us2pk_charges || 1)) * 100).toFixed(2) }}%
              </span>
            </div>
            <div><span class="font-medium">Gross Income:</span>
              {{ Number(consolidation.gross_income || 0).toLocaleString() }}
              <span class="text-xs text-gray-400">
                {{ ((Number(consolidation.gross_income) / Number(consolidation.total_us2pk_charges || 1)) * 100).toFixed(2) }}%
              </span>
            </div>
            <div><span class="font-medium">ROI:</span>
              {{ (Number(consolidation.roi_percent) || 0).toFixed(0) }}%
            </div>
          </div>
        </div>
      </div>

      <!-- Import Taxes Breakdown & Sales Tax Liability -->
      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
          <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Break-Up of Import Taxes</h3>
          <div class="space-y-2 text-sm">
            <div><span class="font-medium">Customs Duty:</span>
              {{ Number(consolidation.customs_duty || 0).toLocaleString() }}
            </div>
            <div><span class="font-medium">Sales Tax:</span>
              {{ Number(consolidation.sales_tax || 0).toLocaleString() }}
            </div>
            <div><span class="font-medium">Income Tax:</span>
              {{ Number(consolidation.income_tax || 0).toLocaleString() }}
            </div>
            <div><span class="font-medium">CAA Charges:</span>
              {{ Number(consolidation.caa_charges || 0).toLocaleString() }}
            </div>
            <div class="border-t pt-2 font-medium">Total:
              {{ Number(consolidation.import_taxes || 0).toLocaleString() }}
            </div>
          </div>
        </div>
        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
          <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Sales Tax Liability</h3>
          <div class="space-y-2 text-sm">
            <div><span class="font-medium">Output Sales Tax:</span>
              {{ Number(consolidation.output_sales_tax || 0).toLocaleString() }}
            </div>
            <div><span class="font-medium">Less: Input Tax:</span>
              {{ Number(consolidation.sales_tax || 0).toLocaleString() }}
            </div>
            <div class="border-t pt-2 font-medium">Net ST Payable:
              {{ Number(consolidation.net_st_payable || 0).toLocaleString() }}
            </div>
          </div>
        </div>
      </div>

      <!-- Shipments -->
      <div class="mt-6">
        <h3 class="mb-3 font-semibold text-gray-800 dark:text-white/90">Shipments</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-800">
              <tr>
                <th class="px-3 py-2 text-left">Code</th>
                <th class="px-3 py-2 text-left">Customer</th>
                <th class="px-3 py-2 text-left">City</th>
                <th class="px-3 py-2 text-left">Arrival Date</th>
                <th class="px-3 py-2 text-left">Item</th>
                <th class="px-3 py-2 text-right">KGs</th>
                <th class="px-3 py-2 text-right">PS Charges</th>
                <th class="px-3 py-2 text-right">Amount Due</th>
                <th class="px-3 py-2 text-right">COD</th>
                <th class="px-3 py-2 text-left">Local Courier</th>
                <th class="px-3 py-2 text-right">Delivery Charges</th>
                <th class="px-3 py-2 text-right">Receivable COD</th>
                <th class="px-3 py-2 text-right">Output Tax</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="s in consolidation.shipments" :key="s.id"
                class="border-b hover:bg-gray-50 dark:border-gray-700">
                <td class="px-3 py-2 font-mono">{{ s.shipment_code || '—' }}</td>
                <td class="px-3 py-2">{{ s.user?.name || '—' }}</td>
                <td class="px-3 py-2">{{ s.user?.city?.city_name || '—' }}</td>
                <td class="px-3 py-2">{{ s.arrival_date || '—' }}</td>
                <td class="px-3 py-2 max-w-xs truncate" :title="s.description">{{ s.description || '—' }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.weight_kgs) || 0).toFixed(2) }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.company_charges) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.amount_due) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.receivable_cod) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2">{{ s.local_courier?.name || '—' }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.delivery_charges) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.receivable_cod) || 0).toLocaleString() }}</td>
                <td class="px-3 py-2 text-right">{{ (Number(s.output_tax) || 0).toFixed(2) }}</td>
              </tr>
              <tr class="font-bold border-t-2">
                <td colspan="5" class="px-3 py-2 text-right">Totals</td>
                <td class="px-3 py-2 text-right">{{ (Number(consolidation.total_weight_kg) || 0).toFixed(2) }}</td>
                <td class="px-3 py-2 text-right">
                  {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.company_charges) || 0), 0).toLocaleString()}}
                </td>
                <td class="px-3 py-2 text-right">
                  {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.amount_due) || 0), 0).toLocaleString()}}
                </td>
                <td class="px-3 py-2 text-right">
                  {{consolidation.shipments?.reduce((sum, s) => sum + (Number(s.receivable_cod) || 0), 0).toLocaleString()}}
                </td>
                <td colspan="2"></td>
                <td class="px-3 py-2 text-right">{{ (Number(consolidation.courier_charges) || 0).toLocaleString() }}
                </td>
                <td class="px-3 py-2 text-right">{{ (Number(consolidation.output_sales_tax) || 0).toLocaleString() }}
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
