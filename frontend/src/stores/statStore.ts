// frontend/src/stores/statStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface Stat {
  id: number
  happy_clients: number
  complete_shipments: number
  customer_reviews: number
  active_services: number
  section_title: string | null
  section_content: string | null
  phone: string | null
  status: boolean
  created_at: string
  updated_at: string
}

export const useStatStore = defineStore('stat', {
  state: () => ({
    items: [] as Stat[],
    pagination: null as PaginatedResponse<Stat> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    activeItem: (state) => state.items.find(item => item.status === true) || null,
    totalStats: (state) => {
      const item = state.items.find(i => i.status === true)
      return item || null
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

        const res = await api.get<PaginatedResponse<Stat>>('/admin/stats', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch stats'
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

    async create(data: Partial<Stat>): Promise<Stat> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<Stat>('/admin/stats', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create stat'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<Stat>): Promise<Stat> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<Stat>(`/admin/stats/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update stat'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/stats/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete stat'
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
