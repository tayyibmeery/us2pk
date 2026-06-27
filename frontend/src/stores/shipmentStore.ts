import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Shipment, PaginatedResponse } from '@/types'

// Fields to never send to the API — relations and computed values
const SKIP_KEYS = [
  'images', 'total', 'user', 'site', 'shipment_status',
  'payment_method', 'local_courier', 'consolidation',
  'created_at', 'updated_at', 'weight_kgs', 'amount_due',
]

// Date fields that arrive as ISO strings from the API and must be stripped to Y-m-d
const DATE_KEYS = ['purchase_date', 'arrival_date', 'expected_delivery_date', 'date_delivered']

function buildFormData(data: Partial<Shipment>): FormData {
  const formData = new FormData()

  if (!data || typeof data !== 'object') return formData

  for (const key of Object.keys(data)) {
    if (SKIP_KEYS.includes(key)) continue

    let value = (data as any)[key]

    if (value === null || value === undefined) continue
    if (typeof value === 'object') continue  // skip any remaining nested objects

    // Strip ISO timestamps to plain Y-m-d
    if (DATE_KEYS.includes(key) && typeof value === 'string' && value.includes('T')) {
      value = value.split('T')[0]
    }

    formData.append(key, String(value))
  }

  return formData
}

export const useShipmentStore = defineStore('shipment', {
  state: () => ({
    items: [] as Shipment[],
    pagination: null as PaginatedResponse<Shipment> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 20,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
    statusFilter: '',
  }),

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
          per_page: this.perPage,
          sort_by: this.sortBy,
          sort_order: this.sortOrder,
        }
        if (this.search) params.search = this.search
        if (this.statusFilter) params.status = this.statusFilter

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

    async create(data: Partial<Shipment>, images?: File[]): Promise<Shipment> {
      this.loading = true
      this.error = null
      try {
        const formData = buildFormData(data)

        if (images && images.length) {
          images.forEach(file => formData.append('images[]', file))
        }

        const res = await api.post<Shipment>('/admin/shipments', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create shipment'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(
      id: number,
      data: Partial<Shipment>,
      images?: File[],
      imagesToDelete?: number[]
    ): Promise<Shipment> {
      this.loading = true
      this.error = null
      try {
        const formData = buildFormData(data)
        formData.append('_method', 'PUT')

        if (imagesToDelete && imagesToDelete.length) {
          imagesToDelete.forEach(imgId => formData.append('images_to_delete[]', String(imgId)))
        }

        if (images && images.length) {
          images.forEach(file => formData.append('images[]', file))
        }

        const res = await api.post<Shipment>(`/admin/shipments/${id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update shipment'
        throw e
      } finally {
        this.loading = false
      }
    },

    async updateStatus(id: number, shipmentStatusId: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post(`/admin/shipments/${id}/status`, { shipment_status_id: shipmentStatusId })
        await this.fetchItems(this.pagination?.current_page || 1)
      } catch (e: any) {
        this.error = e.message || 'Failed to update status'
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
        // Remove from local list immediately
        this.items = this.items.filter(item => item.id !== id)
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
      this.perPage = 20
      this.sortBy = 'id'
      this.sortOrder = 'asc'
      this.statusFilter = ''
    },
  },
})
