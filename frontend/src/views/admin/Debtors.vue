<template>
  <div class="space-y-6">
    <PageBreadcrumb :pageTitle="'Debtors'" />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Debtors</div>
        <div class="text-2xl font-bold">{{ pagination.total }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Amount Due</div>
        <div class="text-2xl font-bold text-green-600">{{ formatCurrency(totalAmountDue) }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total COD</div>
        <div class="text-2xl font-bold text-blue-600">{{ formatCurrency(totalCOD) }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Due COD</div>
        <div class="text-2xl font-bold text-orange-600">{{ formatCurrency(totalDueCOD) }}</div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
        <div class="text-sm text-gray-500">Total Balance</div>
        <div class="text-2xl font-bold text-red-600">{{ formatCurrency(totalBalance) }}</div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border p-4">
      <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
          <input v-model="search" @input="onSearch" type="text" placeholder="Search by invoice, customer, or pcode..."
            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
        </div>
        <div>
          <select v-model="statusFilter" @change="fetchDebtors"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="">All Statuses</option>
            <option value="Delivered">Delivered</option>
            <option value="In Transit">In Transit</option>
            <option value="Pending">Pending</option>
          </select>
        </div>
        <div>
          <input v-model="dateFrom" @change="fetchDebtors" type="date"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
        </div>
        <div>
          <input v-model="dateTo" @change="fetchDebtors" type="date"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
        </div>
        <button @click="exportToExcel"
          class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export Excel
        </button>
      </div>
    </div>

    <!-- Debtors Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 dark:bg-gray-700/50">
              <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Invoice #</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Customer</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">PCode</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Amount Due</th>
              <th colspan="7" class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Amount paid
                as advance in:</th>
              <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">COD</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">COD Date</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Due COD</th>
              <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Balance</th>
              <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Actions</th>
            </tr>
            <tr class="bg-gray-50/50 dark:bg-gray-700/30">
              <th colspan="5"></th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">ABL</th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">SCB</th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">BAFL</th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">FAISAL</th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">CASH</th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">JAZZCASH</th>
              <th class="px-2 py-2 text-center text-xs text-gray-500">EASYPAISA</th>
              <th colspan="3"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading" class="border-b dark:border-gray-700">
              <td colspan="16" class="px-4 py-8 text-center">
                <div
                  class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-500 border-r-transparent">
                </div>
                <p class="mt-2 text-sm text-gray-500">Loading debtors...</p>
              </td>
            </tr>
            <tr v-else-if="debtors.length === 0" class="border-b dark:border-gray-700">
              <td colspan="16" class="px-4 py-8 text-center text-gray-500">
                No debtors found
              </td>
            </tr>
            <tr v-for="debtor in debtors" :key="debtor.id"
              class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30">
              <td class="px-4 py-3">
                <a :href="`/admin/shipments/history/id/${debtor.invoice_no}`"
                  class="text-blue-600 hover:underline dark:text-blue-400">
                  {{ debtor.invoice_no }}
                </a>
              </td>
              <td class="px-4 py-3">
                <span :class="getStatusBadgeClass(debtor.status)">
                  {{ debtor.status || 'N/A' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ debtor.customer_name || 'N/A' }}</td>
              <td class="px-4 py-3">{{ debtor.pcode || 'N/A' }}</td>
              <td class="px-4 py-3 text-right font-medium">{{ formatCurrency(debtor.amount_due) }}</td>

              <!-- Payment Methods -->
              <td class="px-2 py-3 text-center">{{ debtor.abl || '-' }}</td>
              <td class="px-2 py-3 text-center">{{ debtor.scb || '-' }}</td>
              <td class="px-2 py-3 text-center">{{ debtor.bafl || '-' }}</td>
              <td class="px-2 py-3 text-center">{{ debtor.faisal || '-' }}</td>
              <td class="px-2 py-3 text-center">{{ debtor.cash || '-' }}</td>
              <td class="px-2 py-3 text-center">{{ debtor.jazzcash || '-' }}</td>
              <td class="px-2 py-3 text-center">{{ debtor.easypaisa || '-' }}</td>

              <td class="px-4 py-3 text-center">{{ formatCurrency(debtor.cod) }}</td>
              <td class="px-4 py-3">{{ debtor.cod_date ? formatDate(debtor.cod_date) : '-' }}</td>
              <td class="px-4 py-3 text-right text-red-600">{{ formatCurrency(debtor.due_cod) }}</td>
              <td class="px-4 py-3 text-right font-bold">{{ formatCurrency(debtor.balance) }}</td>
              <td class="px-4 py-3 text-center">
                <div class="relative inline-block">
                  <button @click="toggleDropdown(debtor.id)"
                    class="rounded-lg bg-blue-600 px-3 py-1 text-xs text-white hover:bg-blue-700">
                    Action <span class="ml-1">▼</span>
                  </button>
                  <div v-if="activeDropdown === debtor.id"
                    class="absolute right-0 mt-1 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 z-10">
                    <div class="py-1">
                      <a :href="`/report/invoice/${debtor.id}`"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                      </a>
                      <a :href="`/admin/shipments/history/id/${debtor.invoice_no}`"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="fas fa-eye mr-2"></i> View Details
                      </a>
                      <button @click="recordPayment(debtor)"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="fas fa-money-bill-wave mr-2"></i> Record Payment
                      </button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="border-t dark:border-gray-700 px-4 py-3 flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Showing {{ pagination.from || 0 }} - {{ pagination.to || 0 }} of {{ pagination.total || 0 }} results
        </div>
        <div class="flex items-center gap-1">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
            class="rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:hover:bg-gray-800">
            Prev
          </button>
          <button v-for="page in pageNumbers" :key="page" @click="changePage(page)" :class="[
            'px-3 py-1 text-sm rounded-lg',
            page === pagination.current_page
              ? 'bg-blue-600 text-white'
              : 'hover:bg-gray-50 dark:hover:bg-gray-800'
          ]">
            {{ page }}
          </button>
          <button @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="rounded-lg border border-gray-300 px-3 py-1 text-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:hover:bg-gray-800">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Record Payment Modal -->
    <PaymentModal :isOpen="paymentModalOpen" :debtor="selectedDebtor" @close="paymentModalOpen = false"
      @payment-recorded="onPaymentRecorded" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import PaymentModal from '@/components/admin/PaymentModal.vue'
import api from '@/services/api'

interface Debtor {
  id: number
  invoice_no: string
  status: string
  customer_name: string
  pcode: string
  amount_due: number
  abl: number | null
  scb: number | null
  bafl: number | null
  faisal: number | null
  cash: number | null
  jazzcash: number | null
  easypaisa: number | null
  cod: number
  cod_date: string | null
  due_cod: number
  balance: number
}

const debtors = ref<Debtor[]>([])
const loading = ref(false)
const search = ref('')
const statusFilter = ref('')
const dateFrom = ref('')
const dateTo = ref('')
const activeDropdown = ref<number | null>(null)
const paymentModalOpen = ref(false)
const selectedDebtor = ref<Debtor | null>(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0
})

const totalAmountDue = computed(() => {
  return debtors.value.reduce((sum, d) => sum + (d.amount_due || 0), 0)
})

const totalCOD = computed(() => {
  return debtors.value.reduce((sum, d) => sum + (d.cod || 0), 0)
})

const totalDueCOD = computed(() => {
  return debtors.value.reduce((sum, d) => sum + (d.due_cod || 0), 0)
})

const totalBalance = computed(() => {
  return debtors.value.reduce((sum, d) => sum + (d.balance || 0), 0)
})

const pageNumbers = computed(() => {
  const total = pagination.value.last_page || 1
  const current = pagination.value.current_page || 1
  const delta = 2
  const start = Math.max(1, current - delta)
  const end = Math.min(total, current + delta)
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value || 0)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const getStatusBadgeClass = (status: string): string => {
  const classes: Record<string, string> = {
    'Delivered': 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'In Transit': 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'Pending': 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  }
  return classes[status] || 'px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
}

const fetchDebtors = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page,
      search: search.value,
      status: statusFilter.value,
      date_from: dateFrom.value,
      date_to: dateTo.value
    }

    const response = await api.get('/admin/debtors', { params })
    debtors.value = response.data.data || []
    pagination.value = {
      current_page: response.data.current_page || 1,
      last_page: response.data.last_page || 1,
      per_page: response.data.per_page || 20,
      total: response.data.total || 0,
      from: response.data.from || 0,
      to: response.data.to || 0
    }
  } catch (error) {
    console.error('Failed to fetch debtors:', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page: number) => {
  if (page < 1 || page > pagination.value.last_page) return
  pagination.value.current_page = page
  fetchDebtors()
}

const onSearch = () => {
  pagination.value.current_page = 1
  fetchDebtors()
}

const toggleDropdown = (id: number) => {
  activeDropdown.value = activeDropdown.value === id ? null : id
}

const recordPayment = (debtor: Debtor) => {
  selectedDebtor.value = debtor
  paymentModalOpen.value = true
  activeDropdown.value = null
}

const onPaymentRecorded = () => {
  paymentModalOpen.value = false
  fetchDebtors()
}

const exportToExcel = async () => {
  try {
    const response = await api.get('/admin/debtors/export', {
      params: {
        search: search.value,
        status: statusFilter.value,
        date_from: dateFrom.value,
        date_to: dateTo.value
      },
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `debtors_${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Failed to export:', error)
    alert('Failed to export debtors')
  }
}

onMounted(() => {
  fetchDebtors()
})
</script>
