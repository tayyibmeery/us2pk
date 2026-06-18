// stores/categoryStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Category, PaginatedResponse } from '@/types'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    items: [] as Category[],
    pagination: null as PaginatedResponse<Category> | null,
    loading: false,
    error: null as string | null,

    // Table controls
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    activeCategories: (state) => state.items.filter(c => c.status === 'Active'),
  },

  actions: {
    async fetchItems(page = 1, options: {
      search?: string
      perPage?: number
      sortBy?: string
      sortOrder?: 'asc' | 'desc'
    } = {}) {
      // Merge options with current state
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
        const params: any = {
          page,
          per_page: perPage,
          sort_by: sortBy,
          sort_order: sortOrder,
        }
        if (search) params.search = search

        const res = await api.get<PaginatedResponse<Category>>('/admin/categories', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch categories'
        throw e
      } finally {
        this.loading = false
      }
    },

    async fetchAll() {
      await this.fetchItems(1, { perPage: 1000 })
    },

    async setSearch(value: string) {
      this.search = value
      await this.fetchItems(1)
    },

    async setPerPage(value: number) {
      this.perPage = value
      await this.fetchItems(1)
    },

    async setSort(column: string) {
      if (this.sortBy === column) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortBy = column
        this.sortOrder = 'asc'
      }
      await this.fetchItems(1)
    },

    async create(data: Partial<Category>): Promise<Category> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<Category>('/admin/categories', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create category'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<Category>): Promise<Category> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<Category>(`/admin/categories/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update category'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/categories/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete category'
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
      this.sortBy = 'id'
      this.sortOrder = 'asc'
    },
  },
})
