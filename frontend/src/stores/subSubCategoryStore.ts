// stores/subSubCategoryStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { SubSubCategory, PaginatedResponse } from '@/types'

export const useSubSubCategoryStore = defineStore('subSubCategory', {
  state: () => ({
    items: [] as SubSubCategory[],
    pagination: null as PaginatedResponse<SubSubCategory> | null,
    loading: false,
    error: null as string | null,

    // Table controls
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    activeSubSubCategories: (state) => state.items.filter(s => s.status === 'Active'),
  },

  actions: {
    async fetchItems(page = 1, options: {
      search?: string
      perPage?: number
      sortBy?: string
      sortOrder?: 'asc' | 'desc'
    } = {}) {
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

        const res = await api.get<PaginatedResponse<SubSubCategory>>('/admin/sub-sub-categories', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch sub-sub-categories'
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

    async create(data: Partial<SubSubCategory>): Promise<SubSubCategory> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<SubSubCategory>('/admin/sub-sub-categories', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create sub-sub-category'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<SubSubCategory>): Promise<SubSubCategory> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<SubSubCategory>(`/admin/sub-sub-categories/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update sub-sub-category'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/sub-sub-categories/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete sub-sub-category'
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
