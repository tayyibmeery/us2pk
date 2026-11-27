// src/stores/landingStore.ts
import { defineStore } from 'pinia';
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'https://us2pk.com/api',
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

export interface LandingSetting {
  id: number;
  section_key: string;
  section_name: string;
  component_name: string;
  icon: string | null;
  enabled: boolean;
  order: number;
  section_title: string | null;
  section_subtitle: string | null;
  route_path: string | null;
  nav_label: string | null;
  show_in_navbar: boolean;
  show_in_footer: boolean;
  display_options: any;
}

export interface ProhibitedItem {
  id: number;
  item_name: string;
  category: string | null;
  description: string | null;
  reason: string | null;
  severity: 'high' | 'medium' | 'low';
  icon: string | null;
  is_active: boolean;
  order: number;
}

export interface HeroSlide {
  id: number;
  title: string;
  subtitle: string | null;
  content: string | null;
  image: string | null;
  button1_text: string | null;
  button1_link: string | null;
  button2_text: string | null;
  button2_link: string | null;
  description: string | null;
  order: number;
  status: boolean;
  created_at: string;
  updated_at: string;
}

export interface FooterSection {
  id: number;
  title: string | null;
  address: string | null;
  phone: string | null;
  email: string | null;
  whatsapp_number: string | null;
  social_icons: Array<{ platform: string; url: string; icon: string }> | null;
  twitter: string | null;
  facebook: string | null;
  youtube: string | null;
  linkedin: string | null;
  copyright: string | null;
  newsletter_text: string | null;
  service_links: Array<{ title: string; url: string }> | null;
  quick_links: Array<{ title: string; url: string }> | null;
  company_links: Array<{ title: string; url: string }> | null;
  status: boolean;
}

export const useLandingStore = defineStore('landing', {
  state: () => ({
    data: null as any | null,
    loading: false,
    error: null as string | null,
    settings: [] as LandingSetting[],
    prohibitedItems: [] as ProhibitedItem[],
    publicFooter: null as FooterSection | null, // Add public footer
    sections: {
      hero: [] as HeroSlide[],
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

        if (response.data.success) {
          const d = response.data.data;
          this.data = d;

          this.sections.hero = d.hero || [];
          this.sections.service = d.services || [];
          this.sections.testimonial = d.testimonials || [];
          this.sections.team = d.team || [];
          this.sections.pricing = d.pricing || [];
          this.sections.faq = d.faq || [];
          this.sections.blog = d.blog || [];
          this.sections.about = d.about || [];
          this.sections.whyus = d.whyus || [];
          this.sections.contact = d.contact ? [d.contact] : [];
          this.sections.stats = d.stats || null;

          // Store footer data in publicFooter
          if (d.footer) {
            this.publicFooter = d.footer;
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

    /**
     * Fetch public footer data separately (for components that need it)
     */
    async fetchPublicFooter(): Promise<FooterSection | null> {
      try {
        const response = await api.get('/landing/footer');
        if (response.data) {
          this.publicFooter = response.data;
          return response.data;
        }
        return null;
      } catch (error) {
        console.error('Failed to fetch public footer:', error);
        // Return default footer data if API fails
        const defaultFooter: FooterSection = {
          id: 0,
          title: 'US2PK',
          address: 'Lahore, Pakistan',
          phone: '+92 123 4567890',
          email: 'info@us2pk.com',
          whatsapp_number: '923015579810',
          social_icons: [
            { platform: 'twitter', url: 'https://twitter.com/us2pk', icon: 'fab fa-twitter' },
            { platform: 'facebook', url: 'https://facebook.com/us2pk', icon: 'fab fa-facebook-f' },
            { platform: 'linkedin', url: 'https://linkedin.com/company/us2pk', icon: 'fab fa-linkedin-in' },
            { platform: 'youtube', url: 'https://youtube.com/us2pk', icon: 'fab fa-youtube' },
          ],
          twitter: '#',
          facebook: '#',
          youtube: '#',
          linkedin: '#',
          copyright: '© 2026 US2PK. All rights reserved.',
          newsletter_text: 'Connecting Pakistan to the world\'s best products through trusted shopping, shipping, and delivery.',
          service_links: [
            { title: 'Air Freight', url: '#services-section' },
            { title: 'Sea Freight', url: '#services-section' },
            { title: 'Road Freight', url: '#services-section' },
            { title: 'Customs Clearance', url: '#services-section' },
          ],
          company_links: [
            { title: 'About Us', url: '/about' },
            { title: 'Contact Us', url: '/contact' },
            { title: 'Our Services', url: '#services-section' },
            { title: 'Support', url: '/support' },
          ],
          quick_links: [
            { title: 'Terms & Conditions', url: '/terms' },
            { title: 'Privacy Policy', url: '/privacy' },
            { title: 'FAQs', url: '/faq' },
          ],
          status: true,
        };
        this.publicFooter = defaultFooter;
        return defaultFooter;
      }
    },

    async fetchSettings() {
      try {
        const response = await api.get('/landing-settings');
        if (response.data.success) {
          this.settings = response.data.data;
        }
      } catch (error) {
        console.error('Failed to fetch landing settings:', error);
      }
    },

    async fetchProhibitedItems() {
      try {
        const response = await api.get('/landing/prohibited-items');
        if (response.data.success) {
          this.prohibitedItems = response.data.data;
        }
      } catch (error) {
        console.error('Failed to fetch prohibited items:', error);
      }
    },

    isSectionEnabled(key: string): boolean {
      const setting = this.settings.find(s => s.section_key === key);
      return setting ? setting.enabled : true;
    },

    getSectionTitle(key: string): string {
      const setting = this.settings.find(s => s.section_key === key);
      return setting?.section_title || '';
    },

    getSectionSubtitle(key: string): string {
      const setting = this.settings.find(s => s.section_key === key);
      return setting?.section_subtitle || '';
    },

    getNavbarItems(): LandingSetting[] {
      return this.settings
        .filter(s => s.enabled && s.show_in_navbar)
        .sort((a, b) => a.order - b.order);
    },

    getFooterItems(): LandingSetting[] {
      return this.settings
        .filter(s => s.enabled && s.show_in_footer)
        .sort((a, b) => a.order - b.order);
    },

    getEnabledSections(): LandingSetting[] {
      return this.settings
        .filter(s => s.enabled)
        .sort((a, b) => a.order - b.order);
    }
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
    getWhyUsAll: (state) => state.sections.whyus || [],
    getContact: (state) => state.sections.contact?.[0] || null,
    getStats: (state) => state.sections.stats || null,
    getProhibitedItems: (state) => state.prohibitedItems || [],
    getAboutAll: (state) => state.sections.about || [],
    getPublicFooter: (state) => state.publicFooter || null,
  },
});
