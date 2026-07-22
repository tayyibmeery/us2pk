// frontend/src/stores/pageStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Page, PaginatedResponse } from '@/types'

export const usePageStore = defineStore('page', {
  state: () => ({
    // Data & pagination
    items: [] as Page[],
    pagination: null as PaginatedResponse<Page> | null,
    loading: false,
    error: null as string | null,

    // Table controls
    search: '',
    perPage: 10,
    sortBy: 'order',
    sortOrder: 'asc' as 'asc' | 'desc',

    // Types for dropdown
    types: {} as Record<string, string>,
  }),

  getters: {
    activePages: (state) => state.items.filter(page => page.status === true),
    getByType: (state) => (type: string) => state.items.filter(page => page.type === type),
  },

  actions: {
    /**
     * Fetch page types for dropdown
     */
    async fetchTypes() {
      try {
        const res = await api.get<Record<string, string>>('/admin/pages/types')
        this.types = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch page types'
        throw e
      }
    },

    /**
     * Admin endpoint – requires authentication, paginated, with search & sort
     */
    async fetchItems(
      page = 1,
      options: {
        search?: string
        type?: string
        status?: boolean | null
        perPage?: number
        sortBy?: string
        sortOrder?: 'asc' | 'desc'
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
        const params: any = {
          page,
          per_page: perPage,
          sort_by: sortBy,
          sort_order: sortOrder,
        }

        if (search) params.search = search
        if (options.type) params.type = options.type
        if (options.status !== null && options.status !== undefined) {
          params.status = options.status
        }

        const res = await api.get<PaginatedResponse<Page>>('/admin/pages', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch pages'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Convenience: fetch all pages
     */
    async fetchAll() {
      await this.fetchItems(1, { perPage: 1000 })
    },

    /**
     * Set search term and refresh
     */
    async setSearch(search: string) {
      this.search = search
      await this.fetchItems(1)
    },

    /**
     * Set items per page and refresh
     */
    async setPerPage(perPage: number) {
      this.perPage = perPage
      await this.fetchItems(1)
    },

    /**
     * Set sorting column and order
     */
    async setSort(sortBy: string) {
      if (this.sortBy === sortBy) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortBy = sortBy
        this.sortOrder = 'asc'
      }
      await this.fetchItems(1)
    },

    /**
     * Create a new page
     */
    async create(data: FormData): Promise<Page> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<Page>('/admin/pages', data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create page'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Update an existing page
     */
    async update(id: number, data: FormData): Promise<Page> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<Page>(`/admin/pages/${id}?_method=PUT`, data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update page'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Delete a page
     */
    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/pages/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete page'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Upload image only
     */
    async uploadImage(file: File): Promise<string> {
      this.loading = true
      this.error = null
      try {
        const formData = new FormData()
        formData.append('image', file)

        const res = await api.post<{ data: { url: string } }>('/admin/pages/upload-image', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data.data.url
      } catch (e: any) {
        this.error = e.message || 'Failed to upload image'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Delete image
     */
    async deleteImage(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/pages/${id}/image`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete image'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Reorder pages
     */
    async reorder(pages: { id: number; order: number }[]): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post('/admin/pages/reorder', { pages })
      } catch (e: any) {
        this.error = e.message || 'Failed to reorder pages'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Reset the store state
     */
    reset() {
      this.items = []
      this.pagination = null
      this.loading = false
      this.error = null
      this.search = ''
      this.perPage = 10
      this.sortBy = 'order'
      this.sortOrder = 'asc'
      this.types = {}
    },
  },
})
