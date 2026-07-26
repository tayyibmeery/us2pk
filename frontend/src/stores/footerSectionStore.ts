// frontend/src/stores/footerSectionStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface SocialIcon {
  platform: string
  url: string
  icon: string
}

export interface LinkItem {
  title: string
  url: string
}

export interface FooterSection {
  id: number
  title: string | null
  address: string | null
  phone: string | null
  email: string | null
  whatsapp_number: string | null
  social_icons: SocialIcon[] | null
  twitter: string | null
  facebook: string | null
  youtube: string | null
  linkedin: string | null
  copyright: string | null
  newsletter_text: string | null
  service_links: LinkItem[] | null
  quick_links: LinkItem[] | null
  company_links: LinkItem[] | null
  status: boolean
  created_at: string
  updated_at: string
}

export const useFooterSectionStore = defineStore('footerSection', {
  state: () => ({
    items: [] as FooterSection[],
    pagination: null as PaginatedResponse<FooterSection> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),

  getters: {
    activeItem: (state) => state.items.find(item => item.status === true) || null,
  },

  actions: {
    /**
     * Admin endpoint – requires authentication, paginated, with search & sort
     */
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
        const params: any = {
          page,
          per_page: perPage,
          sort_by: sortBy,
          sort_order: sortOrder,
        }
        if (search) params.search = search
        if (options.status !== null && options.status !== undefined) {
          params.status = options.status
        }

        const res = await api.get<PaginatedResponse<FooterSection>>('/admin/footer-sections', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch footer sections'
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

    async create(data: Partial<FooterSection>): Promise<FooterSection> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<FooterSection>('/admin/footer-sections', data)
        // Refresh the list after creating
        await this.fetchItems(1)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create footer section'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: Partial<FooterSection>): Promise<FooterSection> {
      this.loading = true
      this.error = null
      try {
        const res = await api.put<FooterSection>(`/admin/footer-sections/${id}`, data)
        // Refresh the list after updating
        await this.fetchItems(1)
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update footer section'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/footer-sections/${id}`)
        // Refresh the list after deleting
        await this.fetchItems(1)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete footer section'
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
