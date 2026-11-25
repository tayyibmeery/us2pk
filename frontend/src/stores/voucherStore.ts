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
      try {
        const res = await api.post('/admin/vouchers', data)
        return res.data
      } catch (err: any) {
        throw err
      }
    },
    async update(id: number, data: any) {
      try {
        const res = await api.put(`/admin/vouchers/${id}`, data)
        return res.data
      } catch (err: any) {
        throw err
      }
    },
    // ✅ Password-protected delete
    async delete(id: number, password: string) {
      try {
        await api.delete(`/admin/vouchers/${id}`, { data: { password } })
      } catch (err: any) {
        throw err
      }
    },
    // ✅ Password-protected restore
    async restore(id: number, password: string) {
      try {
        await api.post(`/admin/vouchers/${id}/restore`, { password })
      } catch (err: any) {
        throw err
      }
    },
    async approve(id: number) {
      try {
        await api.post(`/admin/vouchers/${id}/approve`)
      } catch (err: any) {
        throw err
      }
    },
  },
})
