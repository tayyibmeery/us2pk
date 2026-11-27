// frontend/src/stores/blogPostStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import type { PaginatedResponse } from '@/types'

export interface BlogPost {
  id: number
  title: string
  slug: string
  excerpt: string | null
  content: string
  image: string | null
  section_title: string | null
  section_content: string | null
  published_at: string | null
  author: string | null
  order: number
  status: boolean
  created_at: string
  updated_at: string
}

export const useBlogPostStore = defineStore('blogPost', {
  state: () => ({
    items: [] as BlogPost[],
    pagination: null as PaginatedResponse<BlogPost> | null,
    loading: false,
    error: null as string | null,
    search: '',
    perPage: 10,
    sortBy: 'created_at',
    sortOrder: 'desc' as 'asc' | 'desc',
  }),

  getters: {
    activeItems: (state) => state.items.filter(item => item.status === true),
    publishedItems: (state) => state.items.filter(item => item.published_at !== null),
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

        const res = await api.get<PaginatedResponse<BlogPost>>('/admin/blog-posts', { params })
        this.items = res.data.data
        this.pagination = res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to fetch blog posts'
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

    async create(data: FormData): Promise<BlogPost> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<BlogPost>('/admin/blog-posts', data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to create blog post'
        throw e
      } finally {
        this.loading = false
      }
    },

    async update(id: number, data: FormData): Promise<BlogPost> {
      this.loading = true
      this.error = null
      try {
        const res = await api.post<BlogPost>(`/admin/blog-posts/${id}?_method=PUT`, data, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        return res.data
      } catch (e: any) {
        this.error = e.message || 'Failed to update blog post'
        throw e
      } finally {
        this.loading = false
      }
    },

    async delete(id: number): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete(`/admin/blog-posts/${id}`)
      } catch (e: any) {
        this.error = e.message || 'Failed to delete blog post'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkStatus(ids: number[], status: boolean): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.post('/admin/blog-posts/bulk-status', { ids, status })
      } catch (e: any) {
        this.error = e.message || 'Failed to update status'
        throw e
      } finally {
        this.loading = false
      }
    },

    async bulkDelete(ids: number[]): Promise<void> {
      this.loading = true
      this.error = null
      try {
        await api.delete('/admin/blog-posts/bulk-delete', { data: { ids } })
      } catch (e: any) {
        this.error = e.message || 'Failed to delete blog posts'
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
      this.sortBy = 'created_at'
      this.sortOrder = 'desc'
    },
  },
})
