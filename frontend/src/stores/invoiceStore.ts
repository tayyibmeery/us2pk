import { defineStore } from 'pinia';
import api from '@/services/api';

export interface Invoice {
  id: number;
  invoice_no: string;
  shipment_id: number;
  shipment?: {
    id: number;
    shipment_code: string;
    description?: string;
    user?: any;
    consolidation?: any;
  };
  date: string;
  amount_due: number;
  cod: number;
  cod_date: string | null;
  output_tax: number;
  status: 'pending' | 'paid' | 'overdue' | 'cancelled';
  paid_date: string | null;
  payment_method: string | null;
  notes: string | null;
  created_at?: string;
  updated_at?: string;
}

interface PaginationData {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

interface SummaryData {
  total_amount: number;
  total_cod: number;
  total_tax: number;
  total_paid: number;
  total_pending: number;
}

export const useInvoiceStore = defineStore('invoice', {
  state: () => ({
    items: [] as Invoice[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 0,
      from: 0,
      to: 0,
    } as PaginationData,
    summary: {
      total_amount: 0,
      total_cod: 0,
      total_tax: 0,
      total_paid: 0,
      total_pending: 0,
    } as SummaryData,
    loading: false,
    error: null as string | null,
    selected: null as Invoice | null,
    filters: {} as Record<string, any>,
  }),

  getters: {
    totalAmount: (state) => {
      return state.items.reduce((sum, invoice) => sum + invoice.amount_due, 0);
    },
    totalCOD: (state) => {
      return state.items.reduce((sum, invoice) => sum + invoice.cod, 0);
    },
    totalTax: (state) => {
      return state.items.reduce((sum, invoice) => sum + invoice.output_tax, 0);
    },
    totalWithTax: (state) => {
      return state.items.reduce((sum, invoice) => sum + invoice.amount_due + invoice.output_tax, 0);
    },
    pendingCount: (state) => {
      return state.items.filter(invoice => invoice.status === 'pending').length;
    },
    paidCount: (state) => {
      return state.items.filter(invoice => invoice.status === 'paid').length;
    },
  },

  actions: {
    async fetchItems(page = 1, params = {}) {
      this.loading = true;
      this.error = null;

      try {
        const allParams = { page, ...this.filters, ...params };
        const res = await api.get('/admin/invoices', { params: allParams });

        this.items = res.data.data || [];
        this.pagination = {
          current_page: res.data.pagination?.current_page || 1,
          last_page: res.data.pagination?.last_page || 1,
          per_page: res.data.pagination?.per_page || 20,
          total: res.data.pagination?.total || 0,
          from: res.data.pagination?.from || 0,
          to: res.data.pagination?.to || 0,
        };
        this.summary = res.data.summary || this.summary;

        return this.items;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch invoices';
        console.error('Fetch invoices error:', err);
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async fetchInvoice(id: number) {
      this.loading = true;
      this.error = null;

      try {
        const res = await api.get(`/admin/invoices/${id}`);
        this.selected = res.data;
        return res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch invoice';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async create(data: Partial<Invoice>) {
      this.loading = true;
      this.error = null;

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
      this.error = null;

      try {
        const res = await api.put(`/admin/invoices/${id}`, data);
        const idx = this.items.findIndex(i => i.id === id);
        if (idx !== -1) {
          this.items[idx] = res.data;
        }
        if (this.selected?.id === id) {
          this.selected = res.data;
        }
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
      this.error = null;

      try {
        await api.delete(`/admin/invoices/${id}`);
        this.items = this.items.filter(i => i.id !== id);
        this.pagination.total -= 1;
        if (this.selected?.id === id) {
          this.selected = null;
        }
        return true;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Delete failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async markAsPaid(id: number, data: { payment_method?: string; notes?: string }) {
      this.loading = true;
      this.error = null;

      try {
        const res = await api.post(`/admin/invoices/${id}/mark-as-paid`, data);
        const idx = this.items.findIndex(i => i.id === id);
        if (idx !== -1) {
          this.items[idx] = res.data;
        }
        if (this.selected?.id === id) {
          this.selected = res.data;
        }
        return res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to mark as paid';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    setFilters(filters: Record<string, any>) {
      this.filters = filters;
    },

    reset() {
      this.items = [];
      this.pagination = { current_page: 1, last_page: 1, per_page: 20, total: 0, from: 0, to: 0 };
      this.summary = { total_amount: 0, total_cod: 0, total_tax: 0, total_paid: 0, total_pending: 0 };
      this.loading = false;
      this.error = null;
      this.selected = null;
      this.filters = {};
    },
  },
});
