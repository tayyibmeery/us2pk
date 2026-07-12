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
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch debtors'
        throw err
      } finally {
        this.loading = false
      }
    },
    // ❌ REMOVED: create() - debtors are auto-generated
    async update(id: number, data: Partial<Debtor>) {
      this.loading = true
      this.error = null
      try {
        const res = await api.put(`/admin/debtors/${id}`, data)
        const idx = this.items.findIndex(i => i.id === id)
        if (idx !== -1) this.items[idx] = res.data
        return res.data
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Update failed'
        throw err
      } finally {
        this.loading = false
      }
    },
    async delete(id: number) {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/debtors/${id}`)
        this.items = this.items.filter(i => i.id !== id)
        this.pagination.total -= 1
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Delete failed'
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
