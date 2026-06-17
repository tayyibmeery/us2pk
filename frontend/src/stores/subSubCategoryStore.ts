import { defineStore } from 'pinia'
import api from '@/services/api'
import type { SubSubCategory, PaginatedResponse } from '@/types'

export const useSubSubCategoryStore = defineStore('subSubCategory', {
  state: () => ({
    items: [] as SubSubCategory[],
    pagination: null as PaginatedResponse<SubSubCategory> | null,
    loading: false,
    error: null as string | null,
  }),
  getters: {
    activeSubSubCategories: (state) => state.items.filter(s => s.status === 'Active'),
  },
  actions: {
    async fetchItems(page = 1, perPage = 10) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get<PaginatedResponse<SubSubCategory>>(
          `/admin/sub-sub-categories?page=${page}&per_page=${perPage}`
        )
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
      await this.fetchItems(1, 1000)
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
    },
  },
})
