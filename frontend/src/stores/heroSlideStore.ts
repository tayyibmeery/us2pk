// frontend/src/stores/heroSlideStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface HeroSlide {
  id: number
  title: string
  subtitle: string | null
  content: string | null
  image: string | null
  button1_text: string | null
  button1_link: string | null
  button2_text: string | null
  button2_link: string | null
  description: string | null
  order: number
  status: boolean
  created_at: string
  updated_at: string
}

export const useHeroSlideStore = defineStore('heroSlide', {
  state: () => ({
    items: [] as HeroSlide[],
    pagination: null as PaginatedResponse<HeroSlide> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'order',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    activeItems: (state) => state.items.filter(item => item.status === true),
    orderedItems: (state) => [...state.items].sort((a, b) => a.order - b.order),
  },

  actions: {
    async fetchItems(
      page = 1,
      options: {
        search?: string
        perPage?: number
        sortBy?: string
        sortOrder?: 'asc' | 'desc'
        status?: boolean | null
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
        if (options.status !== null && options.status !== undefined) {
          params.status = options.status
        }

        const res = await api.get<PaginatedResponse<HeroSlide>>('/admin/hero-slides', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch hero slides'
        throw e
      } finally {
        this.loading = false
      }
    },

    async fetchAll() {
      await this.fetchItems(1, { perPage: 1000 })
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

    async create(data: FormData): Promise<HeroSlide> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<HeroSlide>('/admin/hero-slides', data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create hero slide'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: FormData): Promise<HeroSlide> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<HeroSlide>(`/admin/hero-slides/${id}?_method=PUT`, data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update hero slide'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/hero-slides/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete hero slide'
        throw e
      } finally {
        this.loading = false
      }
    },

    async reorder(items: { id: number; order: number }[]): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post('/admin/hero-slides/reorder', { items })
      } catch (e: any) {
        this.error = e.message || 'Failed to reorder hero slides'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkStatus(ids: number[], status: boolean): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post('/admin/hero-slides/bulk-status', { ids, status })
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
        await api.delete('/admin/hero-slides/bulk-delete', { data: { ids } })
      } catch (e: any) {
        this.error = e.message || 'Failed to delete hero slides'
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
    },
  },
})
