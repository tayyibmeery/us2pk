import { defineStore } from 'pinia';
import api from '@/services/api';

export interface SalaryPayment {
  id: number;
  employee_id: number;
  employee?: { id: number; name: string };
  month: number;
  year: number;
  amount: number;
  paid_date: string;
  notes: string | null;
  created_at?: string;
  updated_at?: string;
}

export const useSalaryPaymentStore = defineStore('salaryPayment', {
  state: () => ({
    items: [] as SalaryPayment[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
    },
    loading: false,
    error: null as string | null,
    search: '',
    sortBy: 'paid_date',
    sortOrder: 'desc' as 'asc' | 'desc',
    filters: { employee_id: null } as { employee_id: number | null },
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/salary-payments', {
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
        this.error = err.response?.data?.message || 'Failed to fetch salary payments';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data: Partial<SalaryPayment>) {
      const res = await api.post('/admin/salary-payments', data);
      this.items.unshift(res.data);
      this.pagination.total += 1;
      return res.data;
    },
    async update(id: number, data: Partial<SalaryPayment>) {
      const res = await api.put(`/admin/salary-payments/${id}`, data);
      const idx = this.items.findIndex(i => i.id === id);
      if (idx !== -1) this.items[idx] = res.data;
      return res.data;
    },
    async delete(id: number) {
      await api.delete(`/admin/salary-payments/${id}`);
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
    setEmployeeFilter(employeeId: number | null) {
      this.filters.employee_id = employeeId;
      this.fetchItems(1);
    },
    reset() {
      this.items = [];
      this.pagination = { current_page: 1, last_page: 1, per_page: 20, total: 0 };
      this.loading = false;
      this.error = null;
      this.search = '';
      this.filters = { employee_id: null };
    },
  },
});
