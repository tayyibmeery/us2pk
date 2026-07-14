import { defineStore } from 'pinia';
import api from '@/services/api';

export interface PaymentMethod {
  id: number;
  name: string;
  account_id: number | null;  // ← ADD THIS
  status: boolean;
  account?: {                 // ← ADD THIS for nested relation
    id: number;
    name: string;
    code: string;
  };
  created_at?: string;
  updated_at?: string;
}

export const usePaymentMethodStore = defineStore('paymentMethod', {
  state: () => ({
    items: [] as PaymentMethod[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
    },
    loading: false,
    error: null as string | null,
    selected: null as PaymentMethod | null,
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/payment-methods', {
          params: { page, ...params }
        });
        this.items = res.data.data;
        this.pagination = {
          current_page: res.data.current_page,
          last_page: res.data.last_page,
          per_page: res.data.per_page,
          total: res.data.total,
        };
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch payment methods';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data: Partial<PaymentMethod>) {
      this.loading = true;
      try {
        const res = await api.post('/admin/payment-methods', data);
        this.items.unshift(res.data);
        this.pagination.total += 1;
        return res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Create failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async update(id: number, data: Partial<PaymentMethod>) {
      this.loading = true;
      try {
        const res = await api.put(`/admin/payment-methods/${id}`, data);
        const idx = this.items.findIndex(i => i.id === id);
        if (idx !== -1) this.items[idx] = res.data;
        return res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Update failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async delete(id: number) {
      this.loading = true;
      try {
        await api.delete(`/admin/payment-methods/${id}`);
        this.items = this.items.filter(i => i.id !== id);
        this.pagination.total -= 1;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Delete failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    reset() {
      this.items = [];
      this.pagination = { current_page: 1, last_page: 1, per_page: 20, total: 0 };
      this.loading = false;
      this.error = null;
      this.selected = null;
    },
  },
});
