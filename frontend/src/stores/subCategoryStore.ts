import { defineStore } from 'pinia'
import api from '@/services/api'
import type { SubCategory, PaginatedResponse } from '@/types'

export const useSubCategoryStore = defineStore('subCategory', {
  state: () => ({
    items: [] as SubCategory[],
    pagination: null as PaginatedResponse<SubCategory> | null,
    loading: false,
    error: null as string | null,
  }),
  getters: {
    activeSubCategories: (state) => state.items.filter(s => s.status === 'Active'),
  },
  actions: {
    async fetchItems(page = 1, perPage = 10) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get<PaginatedResponse<SubCategory>>(
          `/admin/sub-categories?page=${page}&per_page=${perPage}`
        )
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch sub-categories'
        throw e
      } finally {
        this.loading = false
      }
    },

    async fetchAll() {
      await this.fetchItems(1, 1000)
    },

    async create(data: Partial<SubCategory>): Promise<SubCategory> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<SubCategory>('/admin/sub-categories', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create sub-category'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<SubCategory>): Promise<SubCategory> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<SubCategory>(`/admin/sub-categories/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update sub-category'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/sub-categories/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete sub-category'
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
