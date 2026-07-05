import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface Account {
  id: number
  name: string
  code: string
  acc_class: string
  nature: string
  ownership: string
  pandlcategory: string
  is_active: boolean
}

export const useAccountStore = defineStore('account', {
  state: () => ({
    items: [] as Account[],
    pagination: null as PaginatedResponse<Account> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),
  actions: {
    async fetchItems(page = 1, options: any = {}) {
      this.loading = true
      try {
        const params = { page, per_page: this.perPage, ...options }
        const res = await api.get('/admin/accounts', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message
      } finally {
        this.loading = false
      }
    },
    async create(data: Partial<Account>) {
      const res = await api.post('/admin/accounts', data)
      return res.data
    },
    async update(id: number, data: Partial<Account>) {
      const res = await api.put(`/admin/accounts/${id}`, data)
      return res.data
    },
    async delete(id: number) {
      await api.delete(`/admin/accounts/${id}`)
    },
    async toggleStatus(id: number) {
      await api.post(`/admin/accounts/${id}/toggle-status`)
    },
  },
})
