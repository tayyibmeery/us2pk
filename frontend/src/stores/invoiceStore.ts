import { defineStore } from 'pinia';
import api from '@/services/api';

export interface Invoice {
  id: number;
  invoice_no: string;
  shipment_id: number;
  shipment?: any;
  date: string;
  amount_due: number;
  cod: number;
  cod_date: string | null;
  output_tax: number;
}

export const useInvoiceStore = defineStore('invoice', {
  state: () => ({
    items: [] as Invoice[],
    pagination: { current_page: 1, last_page: 1, per_page: 20, total: 0 },
    loading: false,
    error: null as string | null,
    selected: null as Invoice | null,
  }),
  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/invoices', { params: { page, ...params } });
        this.items = res.data.data;
        this.pagination = {
          current_page: res.data.current_page,
          last_page: res.data.last_page,
          per_page: res.data.per_page,
          total: res.data.total,
        };
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch invoices';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data: Partial<Invoice>) {
      this.loading = true;
      try {
        const res = await api.post('/admin/invoices', data);
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
    async update(id: number, data: Partial<Invoice>) {
      this.loading = true;
      try {
        const res = await api.put(`/admin/invoices/${id}`, data);
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
        await api.delete(`/admin/invoices/${id}`);
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
