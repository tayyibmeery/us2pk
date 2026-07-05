import { defineStore } from 'pinia'
import api from '@/services/api'

export const useVoucherStore = defineStore('voucher', {
  state: () => ({
    items: [],
    pagination: null,
    loading: false,
    error: null,
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true
      try {
        const res = await api.get('/admin/vouchers', { params: { page, ...params } })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message
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
    },
    async approve(id: number) {
      await api.post(`/admin/vouchers/${id}/approve`)
    },
  },
})
