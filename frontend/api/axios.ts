// frontend/src/api/axios.ts
import axios from 'axios';

// Get the API base URL from environment variables
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api';

console.log('API Base URL:', API_BASE_URL); // For debugging

// Create axios instance with default configuration
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Required for cookies/sessions
});

// Request Interceptor: Add authorization token to every request
api.interceptors.request.use(
  (config) => {
    // Get token from localStorage
    const token = localStorage.getItem('token');

    // If token exists, add it to the Authorization header
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // You can also add other headers here if needed
    // config.headers['X-Requested-With'] = 'XMLHttpRequest';

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response Interceptor: Handle common errors
api.interceptors.response.use(
  (response) => {
    // Any status code within 2xx triggers this function
    return response;
  },
  (error) => {
    // Any status codes outside 2xx triggers this function

    // Handle 401 Unauthorized
    if (error.response?.status === 401) {
      // Clear token and user data
      localStorage.removeItem('token');
      localStorage.removeItem('user');

      // Redirect to login page if not already there
      if (!window.location.pathname.includes('/signin') &&
        !window.location.pathname.includes('/signup') &&
        !window.location.pathname.includes('/')) {
        // Only redirect if not on public pages
        window.location.href = '/signin';
      }
    }

    // Handle 403 Forbidden
    if (error.response?.status === 403) {
      console.error('Access denied:', error.response?.data?.message || 'You do not have permission');
    }

    // Handle 404 Not Found
    if (error.response?.status === 404) {
      console.error('Resource not found:', error.config?.url);
    }

    // Handle 500 Server Error
    if (error.response?.status === 500) {
      console.error('Server error:', error.response?.data?.message || 'Something went wrong on the server');
    }

    // Handle network errors (no response)
    if (error.code === 'ECONNABORTED') {
      console.error('Request timeout:', error.message);
    } else if (!error.response) {
      console.error('Network error:', error.message);
    }

    return Promise.reject(error);
  }
);

// Helper function to handle API errors
export const handleApiError = (error: any): string => {
  if (error.response?.data?.message) {
    return error.response.data.message;
  }
  if (error.response?.data?.errors) {
    // Handle validation errors
    const errors = error.response.data.errors;
    const firstError = Object.values(errors)[0];
    if (Array.isArray(firstError) && firstError.length > 0) {
      return firstError[0];
    }
    return 'Validation error occurred';
  }
  if (error.message) {
    return error.message;
  }
  return 'An unexpected error occurred';
};

export default api;
