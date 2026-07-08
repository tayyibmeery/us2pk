import { defineStore } from 'pinia'
import api from '@/services/api'

export const useVoucherStore = defineStore('voucher', {
  state: () => ({
    items: [],
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
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/vouchers', { params: { page, ...params } })
        this.items = res.data.data || []
        this.pagination = res.data || {
          current_page: 1,
          last_page: 1,
          per_page: 20,
          total: 0,
          from: 0,
          to: 0,
        }
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch vouchers'
        this.items = []
        this.pagination = {
          current_page: 1,
          last_page: 1,
          per_page: 20,
          total: 0,
          from: 0,
          to: 0,
        }
      } finally {
        this.loading = false
      }
    },
    async create(data: any) {
      const res = await api.post('/admin/vouchers', data)
      return res.data
    },
    async update(id: number, data: any) {
      const res = await api.put(`/admin/vouchers/${id}`, data)
      return res.data
    },
    async delete(id: number) {
      await api.delete(`/admin/vouchers/${id}`)
      // Refresh the list
      await this.fetchItems(this.pagination?.current_page || 1)
    },
    async approve(id: number) {
      await api.post(`/admin/vouchers/${id}/approve`)
    },
  },
})
