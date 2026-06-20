// frontend/src/stores/shipmentStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Shipment, PaginatedResponse } from '@/types'

export const useShipmentStore = defineStore('shipment', {
  state: () => ({
    items: [] as Shipment[],
    pagination: null as PaginatedResponse<Shipment> | null,
    loading: false,
    error: null as string | null,

    // Table controls
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
    statusFilter: '', // optional filter by status
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
        status?: string
      } = {}
    ) {
      const search = options.search ?? this.search
      const perPage = options.perPage ?? this.perPage
      const sortBy = options.sortBy ?? this.sortBy
      const sortOrder = options.sortOrder ?? this.sortOrder
      const status = options.status ?? this.statusFilter

      if (options.search !== undefined) this.search = options.search
      if (options.perPage !== undefined) this.perPage = options.perPage
      if (options.sortBy !== undefined) this.sortBy = options.sortBy
      if (options.sortOrder !== undefined) this.sortOrder = options.sortOrder
      if (options.status !== undefined) this.statusFilter = options.status

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
        if (status) params.status = status

        const res = await api.get<PaginatedResponse<Shipment>>('/admin/shipments', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch shipments'
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

    async setStatusFilter(status: string) {
      this.statusFilter = status
      await this.fetchItems(1)
    },

    async create(data: Partial<Shipment>): Promise<Shipment> {
      this.loading = true
      this.error = null
      try {
        // Remove generated column
        delete data.total
        const res = await api.post<Shipment>('/admin/shipments', data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create shipment'
        throw e
      } finally {
        this.loading = false
      }
    },
    async updateStatus(id: number, status: string): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post(`/admin/shipments/${id}/status`, { status })
        await this.fetchItems(this.pagination?.current_page || 1)
      } catch (e: any) {
        this.error = e.message || 'Failed to update status'
        throw e
      } finally {
        this.loading = false
      }
    },
    async update(id: number, data: Partial<Shipment>): Promise<Shipment> {
      this.loading = true
      this.error = null
      try {
        delete data.total
        const res = await api.put<Shipment>(`/admin/shipments/${id}`, data)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update shipment'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/shipments/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete shipment'
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
      this.statusFilter = ''
    },
  },
})
