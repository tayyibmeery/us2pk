import { defineStore } from 'pinia'
import api from '@/services/api'

export interface Debtor {
  id: number
  invoice_no: string
  shipment_id: number
  user_id: number
  user?: any
  shipment?: any
  amount_due: number
  receivable_cod: number
  balance: number
  amount_received: number
  courier_deduction: number
  net_receivable: number
  cod: number
  total_payable: number
  last_payment_date: string | null
  created_at?: string
  updated_at?: string
}

export const useDebtorStore = defineStore('debtor', {
  state: () => ({
    items: [] as Debtor[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
      from: 0,
      to: 0,
    },
    loading: false,
    error: null as string | null,
    selected: null as Debtor | null,
  }),

  getters: {
    totalBalance: (state) => {
      return state.items.reduce((sum, d) => sum + (d.balance || 0), 0)
    },
    totalAmountDue: (state) => {
      return state.items.reduce((sum, d) => sum + (d.amount_due || 0), 0)
    },
    unpaidCount: (state) => {
      return state.items.filter(d => (d.balance || 0) > 0).length
    },
  },

  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/debtors', { params: { page, ...params } })
        this.items = res.data.data || []
        this.pagination = {
          current_page: res.data.current_page || 1,
          last_page: res.data.last_page || 1,
          per_page: res.data.per_page || 20,
          total: res.data.total || 0,
          from: res.data.from || 0,
          to: res.data.to || 0,
        }
        return this.items
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch debtors'
        throw err
      } finally {
        this.loading = false
      }
    },

    async getDebtor(id: number) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get(`/admin/debtors/${id}`)
        this.selected = res.data
        return res.data
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch debtor'
        throw err
      } finally {
        this.loading = false
      }
    },

    async recordPayment(id: number, data: any) {
      this.loading = true
      this.error = null
      try {
        const res = await api.post(`/admin/debtors/${id}/payment`, data)
        // Update local items
        const idx = this.items.findIndex(i => i.id === id)
        if (idx !== -1 && res.data.debtor) {
          this.items[idx] = { ...this.items[idx], ...res.data.debtor }
        }
        return res.data
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to record payment'
        throw err
      } finally {
        this.loading = false
      }
    },

    reset() {
      this.items = []
      this.pagination = { current_page: 1, last_page: 1, per_page: 20, total: 0, from: 0, to: 0 }
      this.loading = false
      this.error = null
      this.selected = null
    },
  },
})
