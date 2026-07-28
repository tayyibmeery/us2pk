// frontend/src/services/api.ts
import axios from 'axios';
import { useToast } from '@/composables/useToast';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'https://us2pk.com/api',
  headers: {
    'Accept': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;

  // Only set Content-Type to application/json if it's NOT FormData
  if (!(config.data instanceof FormData)) {
    config.headers['Content-Type'] = 'application/json';
  }

  return config;
});

// ✅ Response interceptor with Toast notifications
api.interceptors.response.use(
  (res) => res,
  (err) => {
    // Check if it's an auth endpoint (login/register/logout)
    const isAuthEndpoint = err.config?.url?.includes('/login') ||
      err.config?.url?.includes('/register') ||
      err.config?.url?.includes('/logout');

    // ✅ Skip toast for CRUD operations (DataTable handles them)
    const isCrudOperation = err.config?.method === 'delete' ||
      err.config?.method === 'put' ||
      err.config?.method === 'post';

    // ✅ Also skip if custom header is set to skip toast
    const skipToast = err.config?.headers?.['X-Skip-Error-Toast'] === 'true';

    // Don't show toast for auth endpoints, CRUD operations, or when skipToast is true
    if (!isAuthEndpoint && !isCrudOperation && !skipToast) {
      const toast = useToast();

      // Extract error message
      let message = err.response?.data?.message || err.message || 'Something went wrong. Please try again.';

      // Handle specific status codes
      if (err.response?.status === 422) {
        // Validation errors - show first error
        const errors = err.response?.data?.errors;
        if (errors) {
          const firstError = Object.values(errors)[0];
          message = Array.isArray(firstError) ? firstError[0] : firstError || message;
        }
      } else if (err.response?.status === 404) {
        message = 'Resource not found.';
      } else if (err.response?.status === 500) {
        message = 'Server error. Please try again later.';
      } else if (err.code === 'ECONNABORTED' || err.message === 'Network Error') {
        message = 'Network error. Please check your connection.';
      }

      // Don't show toast for 401 (handled below)
      if (err.response?.status !== 401) {
        toast.error(message);
      }
    }

    // Handle 401 Unauthorized
    if (err.response?.status === 401) {
      // Don't redirect if already on login/register pages
      const currentPath = window.location.pathname;
      if (!currentPath.includes('/signin') &&
        !currentPath.includes('/signup') &&
        !currentPath.includes('/landing') &&
        currentPath !== '/') {
        localStorage.removeItem('token');
        window.location.href = '/signin';
      }
    }

    return Promise.reject(err);
  }
);

export default api;
