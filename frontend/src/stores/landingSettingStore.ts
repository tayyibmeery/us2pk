// frontend/src/stores/landingSettingStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'

export interface LandingSetting {
  id: number
  section_key: string
  section_name: string
  component_name: string
  icon: string | null
  enabled: boolean
  order: number
  section_title: string | null
  section_subtitle: string | null
  route_path: string | null
  nav_label: string | null
  show_in_navbar: boolean
  show_in_footer: boolean
  display_options: any
  created_at: string
  updated_at: string
}

export const useLandingSettingStore = defineStore('landingSetting', {
  state: () => ({
    items: [] as LandingSetting[],
    availableSections: {} as Record<string, any>,
    loading: false,
    error: null as string | null,
  }),

  getters: {
    enabledItems: (state) => state.items.filter(item => item.enabled === true),
    orderedItems: (state) => [...state.items].sort((a, b) => a.order - b.order),
    navbarItems: (state) => state.items
      .filter(item => item.enabled && item.show_in_navbar)
      .sort((a, b) => a.order - b.order),
    footerItems: (state) => state.items
      .filter(item => item.enabled && item.show_in_footer)
      .sort((a, b) => a.order - b.order),
    getByKey: (state) => (key: string) => state.items.find(item => item.section_key === key),
  },

  actions: {
    async fetchItems() {
      this.loading = true
      this.error = null
      try {
        const res = await api.get('/admin/landing-settings')
        this.items = res.data.data
        this.availableSections = res.data.available_sections || {}
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch landing settings'
        throw e
      } finally {
        this.loading = false
      }
    },

    async updateItem(id: number, data: Partial<LandingSetting>): Promise<LandingSetting> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<LandingSetting>(`/admin/landing-settings/${id}`, data)
        const index = this.items.findIndex(item => item.id === id)
        if (index !== -1) {
          this.items[index] = res.data.data
        }
        return res.data.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update landing setting'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkUpdate(settings: Partial<LandingSetting>[]): Promise<LandingSetting[]> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post('/admin/landing-settings/bulk-update', { settings })
        res.data.data.forEach((updatedItem: LandingSetting) => {
          const index = this.items.findIndex(item => item.id === updatedItem.id)
          if (index !== -1) {
            this.items[index] = updatedItem
          }
        })
        return res.data.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update landing settings'
        throw e
      } finally {
        this.loading = false
      }
    },

    async resetSettings(): Promise<LandingSetting[]> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post('/admin/landing-settings/reset')
        this.items = res.data.data
        return res.data.data
      } catch (e: any) {
        this.error = e.message || 'Failed to reset landing settings'
        throw e
      } finally {
        this.loading = false
      }
    },

    async toggleItem(id: number, enabled: boolean): Promise<LandingSetting> {
      return this.updateItem(id, { enabled })
    },

    async toggleNavbar(id: number, show_in_navbar: boolean): Promise<LandingSetting> {
      return this.updateItem(id, { show_in_navbar })
    },

    async toggleFooter(id: number, show_in_footer: boolean): Promise<LandingSetting> {
      return this.updateItem(id, { show_in_footer })
    },

    async reorderItems(items: { id: number; order: number }[]): Promise<void> {
      const settings = items.map(item => {
        const existing = this.items.find(i => i.id === item.id)
        return {
          ...existing,
          id: item.id,
          order: item.order
        }
      })
      await this.bulkUpdate(settings)
    },

    // Public endpoints
    async fetchPublicSettings() {
      try {
        const res = await api.get('/landing-settings/enabled')
        return res.data.data
      } catch (e: any) {
        console.error('Failed to fetch public settings:', e)
        return []
      }
    },

    async fetchNavbarItems() {
      try {
        const res = await api.get('/landing-settings/navbar')
        return res.data.data
      } catch (e: any) {
        console.error('Failed to fetch navbar items:', e)
        return []
      }
    },

    async fetchFooterItems() {
      try {
        const res = await api.get('/landing-settings/footer')
        return res.data.data
      } catch (e: any) {
        console.error('Failed to fetch footer items:', e)
        return []
      }
    }
  }
})
