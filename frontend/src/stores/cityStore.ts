// frontend/src/stores/cityStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { City, PaginatedResponse } from '@/types'

export const useCityStore = defineStore('city', {
  state: () => ({
    // Data & pagination
    items: [] as City[],
    pagination: null as PaginatedResponse<City> | null,
    loading: false,
    error: null as string | null,

    // Table controls
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    activeCities: (state) => state.items.filter(city => city.status === true),
  },

  actions: {
    /**
     * Public endpoint – no authentication required
     * Used for dropdowns in signup / profile edit
     */
    async fetchPublicCities() {
      this.loading = true
      this.error = null
      try {
        const res = await api.get<City[]>('/public/cities')
        this.items = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch cities'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Admin endpoint – requires authentication, paginated, with search & sort
     * @param page - page number (default 1)
     * @param options - optional overrides for search, perPage, sortBy, sortOrder
     */
    async fetchItems(
      page = 1,
      options: {
        search?: string
        perPage?: number
        sortBy?: string
        sortOrder?: 'asc' | 'desc'
      } = {}
    ) {
      // Merge options with current state
      const search = options.search ?? this.search
      const perPage = options.perPage ?? this.perPage
      const sortBy = options.sortBy ?? this.sortBy
      const sortOrder = options.sortOrder ?? this.sortOrder

      // Update state with new parameters if provided
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
        // Only send search if not empty
        if (search) params.search = search

        const res = await api.get<PaginatedResponse<City>>('/admin/cities', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch cities'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Convenience: fetch all cities using admin endpoint (e.g., for dropdowns)
     */
    async fetchAll() {
      await this.fetchItems(1, { perPage: 1000 })
    },

    /**
     * Set search term and refresh (go to page 1)
     */
    async setSearch(search: string) {
      this.search = search
      await this.fetchItems(1)
    },

    /**
     * Set items per page and refresh (go to page 1)
     */
    async setPerPage(perPage: number) {
      this.perPage = perPage
      await this.fetchItems(1)
    },

    /**
     * Set sorting column and order. Toggle order if same column.
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
     * Create a new city – does NOT auto‑refresh.
     * The component should call fetchItems after a successful creation.
     */
    async create(data: Partial<City>): Promise<City> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<City>('/admin/cities', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create city'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Update an existing city – does NOT auto‑refresh.
     */
    async update(id: number, data: Partial<City>): Promise<City> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<City>(`/admin/cities/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update city'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Delete a city – does NOT auto‑refresh.
     */
    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/cities/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete city'
        throw e
      } finally {
        this.loading = false
      }
    },

    /**
     * Reset the store state (e.g., on logout)
     */
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
