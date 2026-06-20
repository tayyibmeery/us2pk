import { defineStore } from 'pinia'
import api from '@/services/api'
import type { User, PaginatedResponse } from '@/types'

export const useUserStore = defineStore('user', {
  state: () => ({
    items: [] as User[],
    pagination: null as PaginatedResponse<User> | null,
    loading: false,
    error: null as string | null,

    // Table controls
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    // none
  },

  actions: {
    async fetchItems(
      page = 1,
      options: {
        search?: string
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

        const res = await api.get<PaginatedResponse<User>>('/admin/users', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch users'
        throw e
      } finally {
        this.loading = false
      }
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

    async updateStatus(userId: number, status: string): Promise<void> {
      this.error = null
      try {
        await api.post(`/admin/users/${userId}/status`, { status })
        // Refresh the current page
        await this.fetchItems(this.pagination?.current_page || 1)
      } catch (e: any) {
        this.error = e.message || 'Failed to update status'
        throw e
      }
    },

    async create(data: Partial<User>): Promise<User> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<User>('/admin/users', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create user'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<User>): Promise<User> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<User>(`/admin/users/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update user'
        throw e
      } finally {
        this.loading = false
      }
    },

    // We don't need create/update/delete for this version,
    // but you can add them later if needed.
  },
})
