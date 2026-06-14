import { defineStore } from 'pinia';
import api from '@/services/api';
import router from '@/router';

interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  address: string;
  city_id: number;
  city?: { id: number; city_name: string; city_code: string };
  pcode: string;
  role: 'user' | 'admin';
  status: 'pending' | 'verified' | 'approved';
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isVerified: (state) => state.user?.status === 'verified' || state.user?.status === 'approved',
  },
  actions: {
    async register(userData: any) {
      const response = await api.post('/register', userData);
      return response.data;
    },
    async login(email: string, password: string) {
      const response = await api.post('/login', { email, password });
      this.token = response.data.access_token;
      this.user = response.data.user;   // ✅ store user immediately
      localStorage.setItem('token', this.token);
      return response.data;
    },
    async fetchUser() {
      if (!this.token) return;
      try {
        const response = await api.get('/user/profile');
        this.user = response.data;
      } catch (error) {
        // Token invalid – clear everything and redirect
        this.logout(true);
        throw error;
      }
    },
    async logout(silent: boolean = false) {
      if (!silent && this.token) {
        try {
          await api.post('/logout');
        } catch (error) {
          console.error('Logout error', error);
        }
      }
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      router.push('/login');
    },
  },
});
