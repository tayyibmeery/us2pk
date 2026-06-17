import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Category, PaginatedResponse } from '@/types'

export const useCategoryStore = defineStore('category', {
  state: () => ({
    items: [] as Category[],
    pagination: null as PaginatedResponse<Category> | null,
    loading: false,
    error: null as string | null,
  }),
  getters: {
    activeCategories: (state) => state.items.filter(c => c.status === 'Active'),
  },
  actions: {
    async fetchItems(page = 1, perPage = 10) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get<PaginatedResponse<Category>>(
          `/admin/categories?page=${page}&per_page=${perPage}`
        )
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
      await this.fetchItems(1, 1000)
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
    },
  },
})
