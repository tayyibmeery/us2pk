import { defineStore } from 'pinia';
import api from '@/services/api';
import router from '@/router';

interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  avatar: string;
  address: string;
  city_id: number;
  city?: { id: number; city_name: string; city_code: string };
  pcode: string;
  role: 'user' | 'admin';
  status: 'pending' | 'verified' | 'approved';
  bio?: string;
  country?: string;
  postal_code?: string;
  tax_id?: string;
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },
  actions: {
    async login(email: string, password: string) {
      const response = await api.post('/login', { email, password });
      this.token = response.data.access_token;
      localStorage.setItem('token', this.token);
      await this.fetchUser();
      return response.data;
    },
    async register(userData: any) {
      const response = await api.post('/register', userData);
      return response.data;
    },
    async fetchUser() {
      if (!this.token) return;
      const response = await api.get('/user/profile');
      this.user = response.data;
    },
    async logout() {
      await api.post('/logout').catch(() => { });
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      // router.push('/signin');
      router.push('/');
    },
    async updateProfile(data: Partial<User>) {
      const response = await api.put('/user/profile', data);
      this.user = response.data;
      return response.data;
    },
    async updateAvatar(file: File) {
      const formData = new FormData();
      formData.append('avatar', file);
      formData.append('_method', 'PUT');
      const response = await api.post('/user/avatar', formData, {
        headers: { 'Content-Type': undefined },
      });
      await this.fetchUser();
      return response.data;
    },

    // ✅ ADD THIS
    async changePassword(currentPassword: string, newPassword: string, newPasswordConfirmation: string) {
      const response = await api.post('/user/change-password', {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: newPasswordConfirmation,
      });
      return response.data;
    },
  },
});
