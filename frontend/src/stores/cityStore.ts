import { defineStore } from 'pinia'
import api from '@/services/api'
import type { City, PaginatedResponse } from '@/types'

export const useCityStore = defineStore('city', {
  state: () => ({
    items: [] as City[],
    pagination: null as PaginatedResponse<City> | null,
    loading: false,
    error: null as string | null,
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
     * Admin endpoint – requires authentication, paginated
     */
    async fetchItems(page = 1, perPage = 10) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get<PaginatedResponse<City>>(
          `/admin/cities?page=${page}&per_page=${perPage}`
        )
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
     * Convenience method to get all cities (uses admin endpoint with high per_page)
     * Useful if you prefer admin endpoint for dropdowns, but public is recommended.
     */
    async fetchAll() {
      await this.fetchItems(1, 1000)
    },

    /**
     * Create a new city – does NOT auto‑refresh.
     * The component should call `fetchItems` after a successful creation if needed.
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
    },
  },
})
