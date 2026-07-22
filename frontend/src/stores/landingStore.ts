// src/stores/landingStore.ts
import { defineStore } from 'pinia';
import axios from 'axios';

// Create axios instance with correct base URL
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

export interface PageSection {
  id: number;
  title: string;
  slug: string;
  type: string;
  content: string;
  status: boolean;
  order: number;
  image: string | null;
  icon: string | null;
  meta: any;
  created_at: string;
  updated_at: string;
}

export interface StatsData {
  happy_clients: number;
  complete_shipments: number;
  customer_reviews: number;
  active_services: number;
}

export const useLandingStore = defineStore('landing', {
  state: () => ({
    data: null as any | null,
    loading: false,
    error: null as string | null,
    sections: {
      hero: [] as PageSection[],
      service: [] as PageSection[],
      testimonial: [] as PageSection[],
      team: [] as PageSection[],
      pricing: [] as PageSection[],
      faq: [] as PageSection[],
      blog: [] as PageSection[],
      about: [] as PageSection[],
      whyus: [] as PageSection[],
      contact: [] as PageSection[],
      stats: null as StatsData | null,
    }
  }),

  actions: {
    async fetchLandingData() {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.get('/landing');
        console.log('✅ Landing API Response:', response.data);

        if (response.data.success) {
          this.data = response.data.data;

          if (response.data.data.sections) {
            Object.keys(this.sections).forEach(key => {
              if (key !== 'stats') {
                this.sections[key] = [];
              }
            });

            Object.keys(this.sections).forEach(key => {
              if (key !== 'stats' && response.data.data.sections[key]) {
                this.sections[key] = response.data.data.sections[key];
                console.log(`✅ Section ${key} loaded:`, this.sections[key]);
              }
            });
          }

          if (response.data.data.stats) {
            this.sections.stats = response.data.data.stats;
          }
        } else {
          this.error = 'Failed to fetch landing data';
        }
      } catch (error: any) {
        this.error = error.message || 'Error fetching landing data';
        console.error('❌ Error fetching landing data:', error);
      } finally {
        this.loading = false;
      }
    },
  },

  getters: {
    isLoading: (state) => state.loading,
    hasError: (state) => state.error !== null,
    getHero: (state) => state.sections.hero || [],
    getServices: (state) => state.sections.service || [],
    getTestimonials: (state) => state.sections.testimonial || [],
    getTeam: (state) => state.sections.team || [],
    getPricing: (state) => state.sections.pricing || [],
    getFaq: (state) => state.sections.faq || [],
    getBlog: (state) => state.sections.blog || [],
    getAbout: (state) => state.sections.about?.[0] || null,
    getWhyUs: (state) => state.sections.whyus?.[0] || null,
    getContact: (state) => state.sections.contact?.[0] || null,
    getStats: (state) => state.sections.stats || null,
  },
});
