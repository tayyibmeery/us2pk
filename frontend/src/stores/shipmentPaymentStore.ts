import { defineStore } from 'pinia';
import api from '@/services/api';

export interface ShipmentPayment {
  id: number;
  shipment_id: number;
  amount: number;
  payment_date: string;
  payment_method: string | null;
  reference_number: string | null;
  notes: string | null;
  created_at?: string;
  updated_at?: string;
}

export const useShipmentPaymentStore = defineStore('shipmentPayment', {
  state: () => ({
    items: [] as ShipmentPayment[],
    loading: false,
    error: null as string | null,
  }),
  actions: {
    async fetchPayments(shipmentId: number) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get(`/admin/shipments/${shipmentId}/payments`);
        this.items = res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch payments';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async createPayment(shipmentId: number, data: Partial<ShipmentPayment>) {
      this.loading = true;
      try {
        const res = await api.post(`/admin/shipments/${shipmentId}/payments`, data);
        this.items.unshift(res.data); // add to top
        return res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Create payment failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async updatePayment(paymentId: number, data: Partial<ShipmentPayment>) {
      this.loading = true;
      try {
        const res = await api.put(`/admin/shipment-payments/${paymentId}`, data);
        const idx = this.items.findIndex(p => p.id === paymentId);
        if (idx !== -1) this.items[idx] = res.data;
        return res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Update payment failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async deletePayment(paymentId: number) {
      this.loading = true;
      try {
        await api.delete(`/admin/shipment-payments/${paymentId}`);
        this.items = this.items.filter(p => p.id !== paymentId);
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Delete payment failed';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    reset() {
      this.items = [];
      this.loading = false;
      this.error = null;
    }
  },
});
