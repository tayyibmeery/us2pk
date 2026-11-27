// frontend/src/stores/quoteRequestStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface QuoteRequest {
  id: number
  name: string
  email: string
  mobile: string | null
  service: string | null
  note: string | null
  status: 'pending' | 'contacted' | 'completed'
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export const useQuoteRequestStore = defineStore('quoteRequest', {
  state: () => ({
    items: [] as QuoteRequest[],
    pagination: null as PaginatedResponse<QuoteRequest> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'created_at',
    sortOrder: 'desc' as 'asc' | 'desc',
    stats: null as {
      total: number
      pending: number
      contacted: number
      completed: number
      today: number
      this_week: number
      this_month: number
    } | null,
  }),

  getters: {
    pendingItems: (state) => state.items.filter(item => item.status === 'pending'),
    contactedItems: (state) => state.items.filter(item => item.status === 'contacted'),
    completedItems: (state) => state.items.filter(item => item.status === 'completed'),
  },

  actions: {
    async fetchItems(
      page = 1,
      options: {
        search?: string
        perPage?: number
        sortBy?: string
        sortOrder?: 'asc' | 'desc'
        status?: string
        date_from?: string
        date_to?: string
      } = {}
    ) {
      const search = options.search ?? this.search
      const perPage = options.perPage ?? this.perPage
      const sortBy = options.sortBy ?? this.sortBy
      const sortOrder = options.sortOrder ?? this.sortOrder

      if (options.search !== undefined) this.search = options.search
      if (options.perPage !== undefined) this.perPage = options.perPage
      if (options.sortBy !== undefined) this.sortBy = options.sortBy
      if (options.sortOrder !== undefined) this.sortOrder = options.sortOrder

      this.loading = true
      this.error = null

      try {
        const params: any = { page, per_page: perPage, sort_by: sortBy, sort_order: sortOrder }
        if (search) params.search = search
        if (options.status) params.status = options.status
        if (options.date_from) params.date_from = options.date_from
        if (options.date_to) params.date_to = options.date_to

        const res = await api.get<PaginatedResponse<QuoteRequest>>('/admin/quote-requests', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch quote requests'
        throw e
      } finally {
        this.loading = false
      }
    },

    async fetchAll() {
      await this.fetchItems(1, { perPage: 1000 })
    },

    async fetchStats() {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/quote-requests/stats')
        this.stats = res.data.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch stats'
        throw e
      } finally {
        this.loading = false
      }
    },

    async setSearch(search: string) {
      this.search = search
      await this.fetchItems(1)
    },

    async setPerPage(perPage: number) {
      this.perPage = perPage
      await this.fetchItems(1)
    },

    async setSort(sortBy: string) {
      if (this.sortBy === sortBy) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortBy = sortBy
        this.sortOrder = 'asc'
      }
      await this.fetchItems(1)
    },

    async updateStatus(id: number, status: string): Promise<QuoteRequest> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<QuoteRequest>(`/admin/quote-requests/${id}/update-status`, { status })
        // Update local state
        const index = this.items.findIndex(item => item.id === id)
        if (index !== -1) {
          this.items[index] = res.data.data
        }
        return res.data.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update status'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/quote-requests/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete quote request'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkDelete(ids: number[]): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete('/admin/quote-requests/bulk-delete', { data: { ids } })
      } catch (e: any) {
        this.error = e.message || 'Failed to delete quote requests'
        throw e
      } finally {
        this.loading = false
      }
    },

    async exportCSV(params: {
      status?: string
      date_from?: string
      date_to?: string
    } = {}): Promise<Blob> {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/quote-requests/export', {
          params,
          responseType: 'blob',
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to export quote requests'
        throw e
      } finally {
        this.loading = false
      }
    },

    reset() {
      this.items = []
      this.pagination = null
      this.loading = false
      this.error = null
      this.search = ''
      this.perPage = 10
      this.sortBy = 'created_at'
      this.sortOrder = 'desc'
      this.stats = null
    },
  },
})
