<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="pageTitle" />

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-24">
      <div class="w-12 h-12 border-4 border-gray-200 dark:border-gray-700 border-t-brand-600 rounded-full animate-spin">
      </div>
      <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Loading consolidation details…</p>
    </div>

    <!-- Not Found -->
    <div v-else-if="!consolidation"
      class="flex flex-col items-center justify-center py-24 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
      <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
          stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Consolidation not found</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">It may have been removed or the link is incorrect.</p>
      <button @click="goBack"
        class="mt-4 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
        ← Back to Consolidations
      </button>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6">

      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-3 flex-wrap">
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ consolidation.consol_id }}</h2>
              <span
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                {{ consolidation.shipments?.length || 0 }} shipments
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-2 text-sm text-gray-500 dark:text-gray-400">
              <span
                class="inline-flex items-center gap-1.5 font-mono text-xs bg-gray-50 dark:bg-gray-700/50 rounded-md px-2 py-0.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                {{ consolidation.awb_number || 'No AWB' }}
              </span>
              <span class="text-gray-300 dark:text-gray-600">•</span>
              <span>{{ consolidation.warehouse?.name || 'Unassigned warehouse' }}</span>
              <span class="text-gray-300 dark:text-gray-600">•</span>
              <span>{{ consolidation.international_courier?.name || 'No courier' }}</span>
              <span class="text-gray-300 dark:text-gray-600">•</span>
              <span>{{ formatDate(consolidation.date_departed) }} → {{ formatDate(consolidation.date_reached) }}</span>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <router-link :to="`/admin/consolidations/${consolidation.id}/edit`"
              class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition shadow-sm">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit
            </router-link>
            <button @click="goBack"
              class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back
            </button>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <SummaryCard label="Total Shipments" :value="consolidation.shipments?.length || 0" icon="box" tone="blue" />
        <SummaryCard label="Total Weight" :value="`${fmt(consolidation.total_weight_kg, 2)} kg`" icon="scale"
          tone="indigo" />
        <SummaryCard label="Total Revenue" :value="`PKR ${fmt(consolidation.total_us2pk_charges)}`" icon="cash"
          tone="emerald" />
        <SummaryCard label="Net Receivable" :value="`PKR ${fmt(consolidation.receivable_from_courier)}`" icon="wallet"
          :tone="isPositive(consolidation.receivable_from_courier) ? 'emerald' : 'red'" />
      </div>


      <!-- Two Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Details + Courier Receivable -->
        <div class="space-y-6">
          <DetailCard title="Consolidation Details" icon="box" tone="gray">
            <DetailRow label="Warehouse" :value="consolidation.warehouse?.name" />
            <DetailRow label="International Courier" :value="consolidation.international_courier?.name" />
            <DetailRow label="AWB Number" :value="consolidation.awb_number" mono />
            <DetailRow label="Date Departed" :value="formatDate(consolidation.date_departed)" />
            <DetailRow label="Date Reached" :value="formatDate(consolidation.date_reached)" last />
          </DetailCard>

          <DetailCard title="Courier Receivable" icon="wallet" tone="blue">
            <DetailRow label="Gross COD Collected" :value="`PKR ${fmt(codTotal)}`" />
            <DetailRow label="Less: Local Delivery Charges" :value="`- PKR ${fmt(consolidation.courier_charges)}`"
              negative />
            <div class="flex justify-between items-center pt-3 mt-1 border-t border-gray-100 dark:border-gray-700/50">
              <span class="text-sm font-semibold text-gray-800 dark:text-white/90">Net Receivable</span>
              <span class="text-base font-bold"
                :class="isPositive(consolidation.receivable_from_courier) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                PKR {{ fmt(consolidation.receivable_from_courier) }}
              </span>
            </div>
          </DetailCard>
        </div>

        <!-- Right: Import Taxes -->
        <DetailCard title="Import Taxes Breakdown" icon="tax" tone="purple">
          <div class="grid grid-cols-2 gap-3">
            <TaxTile label="Customs Duty" :value="consolidation.customs_duty" />
            <TaxTile label="Sales Tax" :value="consolidation.sales_tax" />
            <TaxTile label="Income Tax" :value="consolidation.income_tax" />
            <TaxTile label="CAA Charges" :value="consolidation.caa_charges" />
          </div>
          <div
            class="mt-4 flex items-center justify-between rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/30 px-4 py-3">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Import Taxes</span>
            <span class="text-base font-bold text-purple-700 dark:text-purple-400">PKR
              {{ fmt(consolidation.import_taxes) }}</span>
          </div>


          <!-- Profitability Banner -->
           <div>
            <br><hr>
          <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gross Income</p>
                <p class="text-2xl font-bold mt-0.5"
                  :class="isPositive(consolidation.gross_income) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                  PKR {{ fmt(consolidation.gross_income - consolidation.courier_charges) }}
                </p>
              </div>
              <div class="h-10 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>
              <div class="flex-1 min-w-[200px]">
                <div class="flex items-center justify-between mb-1.5">
                  <span class="text-sm text-gray-500 dark:text-gray-400">Return on Investment</span>
                  <span class="text-sm font-bold"
                    :class="isPositive(consolidation.roi_percent) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                    {{ fmt(consolidation.roi_percent, 1) }}%
                  </span>
                </div>
                <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-500"
                    :class="isPositive(consolidation.roi_percent) ? 'bg-emerald-500' : 'bg-red-500'"
                    :style="{ width: roiBarWidth + '%' }"></div>
                </div>
              </div>
              <div class="h-10 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>
              <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">Direct Costs</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white/90 mt-0.5">PKR
                  {{ fmt(consolidation.direct_cost) }}
                </p>
              </div>
            </div>
          </div>

          </div>
        </DetailCard>
      </div>

      <!-- Shipments Table -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div
          class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-2">
          <h3
            class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Shipments
          </h3>
          <span class="text-xs font-medium text-gray-400">{{ consolidation.shipments?.length || 0 }} items</span>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/70 sticky top-0 z-10">
              <tr>
                <th
                  class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Code</th>
                <th
                  class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Customer</th>
                <th
                  class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  City</th>
                <th
                  class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Item</th>
                <th
                  class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  KGs</th>
                <th
                  class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  US2PK</th>
                <th
                  class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  COD</th>
                <th
                  class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Local Courier</th>
                <th
                  class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Del. Charges</th>
                <th
                  class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Output Tax</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr v-for="s in consolidation.shipments" :key="s.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                  {{ s.shipment_code || '—' }}</td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ s.user?.name || '—' }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                  {{ s.user?.city?.city_name || '—' }}</td>
                <td class="px-4 py-3 max-w-[180px]">
                  <TruncateTooltip :text="s.description" :limit="24" />
                </td>
                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-200">
                  {{ fmt(s.weight_kgs, 2) }}</td>
                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-200">
                  {{ fmt(s.company_charges) }}</td>
                <td class="px-4 py-3 text-right tabular-nums font-medium text-gray-800 dark:text-white/90">
                  {{ fmt(s.receivable_cod) }}</td>
                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                  {{ s.local_courier?.name || '—' }}</td>
                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-200">
                  {{ fmt(s.delivery_charges, 2) }}</td>
                <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-200">
                  {{ fmt(s.output_tax, 2) }}</td>
              </tr>
              <tr v-if="!consolidation.shipments?.length">
                <td colspan="10" class="px-4 py-10 text-center text-sm text-gray-400">No shipments in this
                  consolidation.</td>
              </tr>
            </tbody>
            <tfoot v-if="consolidation.shipments?.length"
              class="bg-gray-50 dark:bg-gray-800/70 border-t-2 border-gray-200 dark:border-gray-700">
              <tr class="font-semibold">
                <td colspan="4"
                  class="px-4 py-3 text-right text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Totals
                </td>
                <td class="px-4 py-3 text-right tabular-nums">{{ fmt(consolidation.total_weight_kg, 2) }}</td>
                <td class="px-4 py-3 text-right tabular-nums">{{ fmt(companyChargesTotal) }}</td>
                <td class="px-4 py-3 text-right tabular-nums">{{ fmt(codTotal) }}</td>
                <td></td>
                <td class="px-4 py-3 text-right tabular-nums">{{ fmt(consolidation.courier_charges, 2) }}</td>
                <td class="px-4 py-3 text-right tabular-nums">{{ fmt(consolidation.output_sales_tax, 2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, h, defineComponent, type PropType } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import TruncateTooltip from '@/components/common/TruncateTooltip.vue'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const consolidation = ref<any>(null)
const pageTitle = ref('Consolidation Details')

// ---------- helpers ----------
const num = (v: any) => Number(v) || 0
const fmt = (v: any, decimals = 0) =>
  decimals > 0 ? num(v).toFixed(decimals) : num(v).toLocaleString()
const isPositive = (v: any) => num(v) >= 0

const codTotal = computed(() =>
  consolidation.value?.shipments?.reduce((sum: number, s: any) => sum + num(s.receivable_cod), 0) || 0
)
const companyChargesTotal = computed(() =>
  consolidation.value?.shipments?.reduce((sum: number, s: any) => sum + num(s.company_charges), 0) || 0
)
const roiBarWidth = computed(() => {
  const roi = num(consolidation.value?.roi_percent)
  return Math.max(4, Math.min(100, Math.abs(roi)))
})

function formatDate(date: string | null) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

async function fetchConsolidation() {
  loading.value = true
  try {
    const res = await api.get(`/admin/consolidations/${route.params.id}`)
    consolidation.value = res.data
    pageTitle.value = `${consolidation.value.consol_id} · Details`
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

// ---------- small local presentational components ----------
const ICONS: Record<string, string> = {
  box: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
  scale: 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
  cash: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 3v1m0-1c-1.11 0-2.08-.402-2.599-1',
  wallet: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
  tax: 'M9 14l6-6m-5.5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm6 6a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
}

const TONES: Record<string, { bg: string; text: string; header: string; headerText: string }> = {
  blue: { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-600 dark:text-blue-400', header: 'bg-blue-50/70 dark:bg-blue-900/10', headerText: 'text-blue-700 dark:text-blue-400' },
  indigo: { bg: 'bg-indigo-50 dark:bg-indigo-900/20', text: 'text-indigo-600 dark:text-indigo-400', header: '', headerText: '' },
  emerald: { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-600 dark:text-emerald-400', header: '', headerText: '' },
  red: { bg: 'bg-red-50 dark:bg-red-900/20', text: 'text-red-600 dark:text-red-400', header: '', headerText: '' },
  purple: { bg: 'bg-purple-50 dark:bg-purple-900/20', text: 'text-purple-600 dark:text-purple-400', header: 'bg-purple-50/70 dark:bg-purple-900/10', headerText: 'text-purple-700 dark:text-purple-400' },
  gray: { bg: 'bg-gray-50 dark:bg-gray-800/50', text: 'text-gray-600 dark:text-gray-400', header: 'bg-gray-50 dark:bg-gray-800/50', headerText: 'text-gray-700 dark:text-gray-300' },
}

const SummaryCard = defineComponent({
  props: { label: String, value: [String, Number], icon: String, tone: { type: String, default: 'gray' } },
  setup(props) {
    return () => {
      const tone = TONES[props.tone as string] ?? TONES.gray
      return h('div', { class: 'bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm hover:shadow-md transition-shadow' }, [
        h('div', { class: 'flex items-start justify-between' }, [
          h('div', {}, [
            h('p', { class: 'text-xs font-medium text-gray-500 dark:text-gray-400' }, props.label),
            h('p', { class: `text-xl font-bold mt-1 ${tone.text}` }, String(props.value)),
          ]),
          h('div', { class: `p-2.5 rounded-xl ${tone.bg}` }, [
            h('svg', { class: `w-5 h-5 ${tone.text}`, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
              h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: ICONS[props.icon as string] }),
            ]),
          ]),
        ]),
      ])
    }
  },
})

const DetailCard = defineComponent({
  props: { title: String, icon: String, tone: { type: String, default: 'gray' } },
  setup(props, { slots }) {
    return () => {
      const tone = TONES[props.tone as string] ?? TONES.gray
      return h('div', { class: 'bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm' }, [
        h('div', { class: `px-6 py-4 border-b border-gray-200 dark:border-gray-700 ${tone.header}` }, [
          h('h3', { class: `text-xs font-semibold uppercase tracking-wider flex items-center gap-2 ${tone.headerText}` }, [
            h('svg', { class: 'w-4 h-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'stroke-width': '2' }, [
              h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: ICONS[props.icon as string] }),
            ]),
            props.title,
          ]),
        ]),
        h('div', { class: 'p-6' }, slots.default?.()),
      ])
    }
  },
})

const DetailRow = defineComponent({
  props: { label: String, value: [String, Number], mono: Boolean, negative: Boolean, last: Boolean },
  setup(props) {
    return () =>
      h('div', { class: `flex justify-between items-center py-2.5 ${props.last ? '' : 'border-b border-gray-100 dark:border-gray-700/50'}` }, [
        h('span', { class: 'text-sm text-gray-500 dark:text-gray-400' }, props.label),
        h('span', {
          class: `text-sm font-medium ${props.mono ? 'font-mono' : ''} ${props.negative ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-white/90'
            }`,
        }, props.value || '—'),
      ])
  },
})

const TaxTile = defineComponent({
  props: { label: String, value: [String, Number] as PropType<any> },
  setup(props) {
    return () =>
      h('div', { class: 'text-center p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700' }, [
        h('p', { class: 'text-xs text-gray-500 dark:text-gray-400' }, props.label),
        h('p', { class: 'text-base font-bold text-gray-800 dark:text-white/90 mt-0.5' }, `PKR ${num(props.value).toLocaleString()}`),
      ])
  },
})
</script>
