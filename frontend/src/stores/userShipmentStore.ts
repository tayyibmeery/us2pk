import { defineStore } from 'pinia'
import api from '@/services/api'

export interface Shipment {
  id: number
  shipment_code: string
  description: string
  weight: number
  weight_unit: string
  item_value_pkr: number
  company_charges: number
  total: number
  received_amount: number
  bought_by: string
  shipment_status?: {
    id: number
    name: string
  }
  local_courier?: {
    id: number
    name: string
  }
  payment_method?: {
    id: number
    name: string
  }
  site?: {
    id: number
    name: string
  }
  consolidation?: {
    id: number
    consol_id: string
  }
  created_at: string
  updated_at: string
}

export interface DashboardStats {
  total: number
  inTransit: number
  delivered: number
  pending: number
  change: string
}

export const useUserShipmentStore = defineStore('userShipment', {
  state: () => ({
    shipments: [] as Shipment[],
    currentShipment: null as Shipment | null,
    stats: {
      total: 0,
      inTransit: 0,
      delivered: 0,
      pending: 0,
      change: '+0%'
    } as DashboardStats,
    recentShipments: [] as Shipment[],
    timeline: [] as any[],
    loading: false,
    error: null as string | null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0
    }
  }),

  getters: {
    hasShipments: (state) => state.shipments.length > 0,
    shipmentCount: (state) => state.pagination.total,
    isLoading: (state) => state.loading,
  },

  actions: {
    // Fetch dashboard stats
    async fetchDashboardStats() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/user/dashboard/stats')
        this.stats = response.data.stats
        this.recentShipments = response.data.recent_shipments
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to fetch dashboard stats'
        console.error('Dashboard stats error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // Fetch user shipments with pagination
    async fetchShipments(page = 1, perPage = 10, filters = {}) {
      this.loading = true
      this.error = null
      try {
        const params = { page, per_page: perPage, ...filters }
        const response = await api.get('/user/shipments', { params })
        this.shipments = response.data.data
        this.pagination = {
          current_page: response.data.current_page || 1,
          last_page: response.data.last_page || 1,
          per_page: response.data.per_page || 10,
          total: response.data.total || 0,
          from: response.data.from || 0,
          to: response.data.to || 0
        }
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to fetch shipments'
        console.error('Fetch shipments error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // Fetch single shipment details
    async fetchShipment(id: number) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/user/shipments/${id}`)
        this.currentShipment = response.data
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to fetch shipment details'
        console.error('Fetch shipment error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // Track shipment by tracking number
    async trackShipment(trackingNumber: string) {
      this.loading = true
      this.error = null
      try {
        const response = await api.get(`/user/track/${trackingNumber}`)
        this.currentShipment = response.data.shipment
        this.timeline = response.data.timeline || []
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to track shipment'
        console.error('Track shipment error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // Reset store
    reset() {
      this.shipments = []
      this.currentShipment = null
      this.stats = {
        total: 0,
        inTransit: 0,
        delivered: 0,
        pending: 0,
        change: '+0%'
      }
      this.recentShipments = []
      this.timeline = []
      this.loading = false
      this.error = null
      this.pagination = {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      }
    }
  }
})
