import { defineStore } from 'pinia';
import api from '@/services/api';

export interface AccountingSummary {
  period: {
    start: string;
    end: string;
    year: number;
    month: number | null;
  };
  income: number;
  expenses: {
    consolidation: number;
    manual: number;
    salary: number;
    total: number;
  };
  profit: number;
}

export interface MonthlyBreakdown {
  month: number;
  month_name: string;
  income: number;
  expenses: {
    consolidation: number;
    manual: number;
    salary: number;
    total: number;
  };
  profit: number;
}

export const useAccountingStore = defineStore('accounting', {
  state: () => ({
    summary: null as AccountingSummary | null,
    monthlyData: [] as MonthlyBreakdown[],
    loading: false,
    error: null as string | null,
  }),
  actions: {
    async fetchSummary(year: number, month: number | null = null) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/accounting/summary', {
          params: { year, month }
        });
        this.summary = res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch summary';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async fetchMonthlyBreakdown(year: number) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/admin/accounting/monthly-breakdown', {
          params: { year }
        });
        this.monthlyData = res.data;
      } catch (err: any) {
        this.error = err.response?.data?.message || 'Failed to fetch monthly breakdown';
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async fetchAllData(year: number, month: number | null = null) {
      await Promise.all([
        this.fetchSummary(year, month),
        this.fetchMonthlyBreakdown(year),
      ]);
    },
  },
});
