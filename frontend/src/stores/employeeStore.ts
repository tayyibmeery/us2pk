import { defineStore } from 'pinia';
import api from '@/services/api';

export interface Employee {
  id: number;
  name: string;
  position: string | null;
  monthly_salary: number;
  joining_date: string | null;
  status: boolean;
  created_at?: string;
  updated_at?: string;
}

export const useEmployeeStore = defineStore('employee', {
  state: () => ({
    items: [] as Employee[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
    },
    loading: false,
    error: null as string | null,
    search: '',
    sortBy: 'name',
    sortOrder: 'asc' as 'asc' | 'desc',
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/employees');
        let items = res.data;
        if (this.search) {
          const s = this.search.toLowerCase();
          items = items.filter((emp: Employee) =>
            emp.name.toLowerCase().includes(s) ||
            (emp.position && emp.position.toLowerCase().includes(s))
          );
        }
        if (this.sortBy) {
          items.sort((a: any, b: any) => {
            let va = a[this.sortBy] ?? '';
            let vb = b[this.sortBy] ?? '';
            if (typeof va === 'string') va = va.toLowerCase();
            if (typeof vb === 'string') vb = vb.toLowerCase();
            if (va < vb) return this.sortOrder === 'asc' ? -1 : 1;
            if (va > vb) return this.sortOrder === 'asc' ? 1 : -1;
            return 0;
          });
        }
        this.items = items;
        this.pagination = {
          current_page: 1,
          last_page: 1,
          per_page: Math.max(1, items.length || 1),
          total: items.length,
        };
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch employees';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data: Partial<Employee>) {
      const res = await api.post('/admin/employees', data);
      this.items.push(res.data);
      this.pagination.total += 1;
      return res.data;
    },
    async update(id: number, data: Partial<Employee>) {
      const res = await api.put(`/admin/employees/${id}`, data);
      const idx = this.items.findIndex(i => i.id === id);
      if (idx !== -1) this.items[idx] = res.data;
      return res.data;
    },
    async delete(id: number) {
      await api.delete(`/admin/employees/${id}`);
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
    setStatusFilter(status: string) {
      // Not used
    },
    reset() {
      this.items = [];
      this.pagination = { current_page: 1, last_page: 1, per_page: 20, total: 0 };
      this.loading = false;
      this.error = null;
      this.search = '';
    },
  },
});
