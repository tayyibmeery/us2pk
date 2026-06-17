// stores/cityStore.ts
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
    // optionally get active cities
    activeCities: (state) => state.items.filter(city => city.status === true),
  },
  actions: {
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
    async fetchItems(page = 1, perPage = 10) {
      this.loading = true
      this.error = null
      try {
        const res = await api.get<PaginatedResponse<City>>(`/admin/cities?page=${page}&per_page=${perPage}`)
        this.items = res.data.data


        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch cities'
        throw e
      } finally {
        this.loading = false
      }
    },
    async fetchAll() {
      // fetch all cities (useful for dropdowns) - adjust per_page to a large number
      await this.fetchItems(1, 1000)
    },
    async create(data: Partial<City>) {
      const res = await api.post<City>('/admin/cities', data)
      // Optionally refresh list
      await this.fetchItems(this.pagination?.current_page || 1)
      return res.data
    },
    async update(id: number, data: Partial<City>) {
      const res = await api.put<City>(`/admin/cities/${id}`, data)
      await this.fetchItems(this.pagination?.current_page || 1)
      return res.data
    },
    async delete(id: number) {
      await api.delete(`/admin/cities/${id}`)
      await this.fetchItems(this.pagination?.current_page || 1)
    },
  },
})
