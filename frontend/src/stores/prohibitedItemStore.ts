// frontend/src/stores/prohibitedItemStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface ProhibitedItem {
  id: number
  item_name: string
  category: string | null
  description: string | null
  reason: string | null
  severity: 'high' | 'medium' | 'low'
  icon: string | null
  is_active: boolean
  order: number
  created_at: string
  updated_at: string
  deleted_at: string | null
  severity_color?: string
  severity_label?: string
}

export const useProhibitedItemStore = defineStore('prohibitedItem', {
  state: () => ({
    items: [] as ProhibitedItem[],
    pagination: null as PaginatedResponse<ProhibitedItem> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'order',
    sortOrder: 'asc' as 'asc' | 'desc',
    categories: [] as string[],
    severityOptions: {} as Record<string, string>,
  }),

  getters: {
    activeItems: (state) => state.items.filter(item => item.is_active === true),
    orderedItems: (state) => [...state.items].sort((a, b) => a.order - b.order),
    getByCategory: (state) => (category: string) =>
      state.items.filter(item => item.category === category),
    getBySeverity: (state) => (severity: string) =>
      state.items.filter(item => item.severity === severity),
    getSeverityLabel: () => (severity: string) => {
      const labels: Record<string, string> = {
        high: 'High Risk',
        medium: 'Medium Risk',
        low: 'Low Risk',
      }
      return labels[severity] || severity
    },
    getSeverityColor: () => (severity: string) => {
      const colors: Record<string, string> = {
        high: 'danger',
        medium: 'warning',
        low: 'success',
      }
      return colors[severity] || 'secondary'
    },
  },

  actions: {
    async fetchItems(
      page = 1,
      options: {
        search?: string
        perPage?: number
        sortBy?: string
        sortOrder?: 'asc' | 'desc'
        category?: string
        severity?: string
        is_active?: boolean | null
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
        if (options.category) params.category = options.category
        if (options.severity) params.severity = options.severity
        if (options.is_active !== null && options.is_active !== undefined) {
          params.is_active = options.is_active
        }

        const res = await api.get('/admin/prohibited-items', { params })

        if (res.data.success) {
          // Items are in res.data.data.data (nested)
          const responseData = res.data.data
          this.items = responseData.data || []
          this.pagination = responseData

          if (res.data.meta) {
            this.categories = res.data.meta.categories || []
            this.severityOptions = res.data.meta.severity_options || {}
          }
        } else {
          this.error = 'Failed to fetch prohibited items'
        }
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch prohibited items'
        throw e
      } finally {
        this.loading = false
      }
    },

    async fetchAll() {
      await this.fetchItems(1, { perPage: 1000 })
    },

    async fetchCategories() {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/prohibited-items/categories')
        this.categories = res.data.data || []
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch categories'
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

    async create(data: Partial<ProhibitedItem>): Promise<ProhibitedItem> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<ProhibitedItem>('/admin/prohibited-items', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create prohibited item'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<ProhibitedItem>): Promise<ProhibitedItem> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<ProhibitedItem>(`/admin/prohibited-items/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update prohibited item'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/prohibited-items/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete prohibited item'
        throw e
      } finally {
        this.loading = false
      }
    },

    async reorder(items: { id: number; order: number }[]): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post('/admin/prohibited-items/reorder', { items })
      } catch (e: any) {
        this.error = e.message || 'Failed to reorder prohibited items'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkStatus(ids: number[], is_active: boolean): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post('/admin/prohibited-items/bulk-status', { ids, is_active })
      } catch (e: any) {
        this.error = e.message || 'Failed to update status'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkDelete(ids: number[]): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete('/admin/prohibited-items/bulk-delete', { data: { ids } })
      } catch (e: any) {
        this.error = e.message || 'Failed to delete prohibited items'
        throw e
      } finally {
        this.loading = false
      }
    },

    async exportCSV(params: {
      category?: string
      severity?: string
      is_active?: boolean
    } = {}): Promise<Blob> {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/prohibited-items/export', {
          params,
          responseType: 'blob',
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to export prohibited items'
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
      this.sortBy = 'order'
      this.sortOrder = 'asc'
      this.categories = []
      this.severityOptions = {}
    },
  },
})
