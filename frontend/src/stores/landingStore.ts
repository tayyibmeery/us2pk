// src/stores/landingStore.ts
import { defineStore } from 'pinia';
import axios from 'axios';

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

export interface LandingData {
  sections: {
    hero: PageSection[];
    service: PageSection[];
    testimonial: PageSection[];
    team: PageSection[];
    pricing: PageSection[];
    faq: PageSection[];
    blog: PageSection[];
    about: PageSection[];
    whyus: PageSection[];
    contact: PageSection[];
  };
  stats: StatsData;
}

export const useLandingStore = defineStore('landing', {
  state: () => ({
    data: null as LandingData | null,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchLandingData() {
      this.loading = true;
      this.error = null;

      try {
        // Use the clean /api/landing endpoint
        const response = await axios.get('/api/landing');
        if (response.data.success) {
          this.data = response.data.data;
        } else {
          this.error = 'Failed to fetch landing data';
        }
      } catch (error: any) {
        this.error = error.message || 'Error fetching landing data';
        console.error('Error fetching landing data:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchSection(type: string) {
      try {
        const response = await axios.get(`/api/landing/section/${type}`);
        if (response.data.success) {
          return response.data.data;
        }
        return null;
      } catch (error) {
        console.error(`Error fetching ${type} section:`, error);
        return null;
      }
    },

    async fetchHero() {
      try {
        const response = await axios.get('/api/landing/hero');
        return response.data.success ? response.data.data : [];
      } catch (error) {
        console.error('Error fetching hero sections:', error);
        return [];
      }
    },

    async fetchServices() {
      try {
        const response = await axios.get('/api/landing/services');
        return response.data.success ? response.data.data : [];
      } catch (error) {
        console.error('Error fetching services:', error);
        return [];
      }
    },

    async fetchTestimonials() {
      try {
        const response = await axios.get('/api/landing/testimonials');
        return response.data.success ? response.data.data : [];
      } catch (error) {
        console.error('Error fetching testimonials:', error);
        return [];
      }
    },

    async fetchTeam() {
      try {
        const response = await axios.get('/api/landing/team');
        return response.data.success ? response.data.data : [];
      } catch (error) {
        console.error('Error fetching team members:', error);
        return [];
      }
    },

    async fetchPricing() {
      try {
        const response = await axios.get('/api/landing/pricing');
        return response.data.success ? response.data.data : [];
      } catch (error) {
        console.error('Error fetching pricing:', error);
        return [];
      }
    },

    async fetchStats() {
      try {
        const response = await axios.get('/api/landing/stats');
        return response.data.success ? response.data.data : null;
      } catch (error) {
        console.error('Error fetching stats:', error);
        return null;
      }
    },
  },

  getters: {
    isLoading: (state) => state.loading,
    hasError: (state) => state.error !== null,
    getHero: (state) => state.data?.sections?.hero || [],
    getServices: (state) => state.data?.sections?.service || [],
    getTestimonials: (state) => state.data?.sections?.testimonial || [],
    getTeam: (state) => state.data?.sections?.team || [],
    getPricing: (state) => state.data?.sections?.pricing || [],
    getFaq: (state) => state.data?.sections?.faq || [],
    getAbout: (state) => state.data?.sections?.about?.[0] || null,
    getStats: (state) => state.data?.stats || null,
  },
});
