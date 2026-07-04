import { defineStore } from 'pinia';
import api from '@/services/api';

export interface Expense {
  id: number;
  category_id: number | null;
  category?: { id: number; name: string; type: string };
  amount: number;
  date: string;
  description: string | null;
  reference: string | null;
  created_by?: number;
  creator?: { id: number; name: string };
  created_at?: string;
  updated_at?: string;
}

export const useExpenseStore = defineStore('expense', {
  state: () => ({
    items: [] as Expense[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
    },
    loading: false,
    error: null as string | null,
    search: '',
    sortBy: 'date',
    sortOrder: 'desc' as 'asc' | 'desc',
    filters: {} as { category?: number; start_date?: string; end_date?: string },
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/expenses', {
          params: { page, ...this.filters, search: this.search, sortBy: this.sortBy, sortOrder: this.sortOrder, ...params }
        });
        this.items = res.data.data;
        this.pagination = {
          current_page: res.data.current_page,
          last_page: res.data.last_page,
          per_page: res.data.per_page,
          total: res.data.total,
        };
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch expenses';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data: Partial<Expense>) {
      const res = await api.post('/admin/expenses', data);
      this.items.unshift(res.data);
      this.pagination.total += 1;
      return res.data;
    },
    async update(id: number, data: Partial<Expense>) {
      const res = await api.put(`/admin/expenses/${id}`, data);
      const idx = this.items.findIndex(i => i.id === id);
      if (idx !== -1) this.items[idx] = res.data;
      return res.data;
    },
    async delete(id: number) {
      await api.delete(`/admin/expenses/${id}`);
      this.items = this.items.filter(i => i.id !== id);
      this.pagination.total -= 1;
    },
    setSearch(search: string) {
      this.search = search;
      this.fetchItems(1);
    },
    setPerPage(perPage: number) {
      this.pagination.per_page = perPage;
      this.fetchItems(1);
    },
    setSort(sortBy: string, sortOrder?: 'asc' | 'desc') {
      this.sortBy = sortBy;
      if (sortOrder) this.sortOrder = sortOrder;
      this.fetchItems(1);
    },
    setFilters(filters: any) {
      this.filters = filters;
      this.fetchItems(1);
    },
    reset() {
      this.items = [];
      this.pagination = { current_page: 1, last_page: 1, per_page: 20, total: 0 };
      this.loading = false;
      this.error = null;
      this.search = '';
      this.filters = {};
    },
  },
});
