import axios from 'axios';

console.log('API module loaded, baseURL:', import.meta.env.VITE_API_URL || window.location.origin);

const api = axios.create({
  baseURL: window.location.origin, // includes /api once
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Add request interceptor for CSRF token
api.interceptors.request.use((config) => {
  console.log('API Request:', config.method?.toUpperCase(), config.url);
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token;
  }
  return config;
});

// Add response interceptor for error handling
api.interceptors.response.use(
  (response) => {
    console.log('API Response:', response.status, response.config.url);
    return response;
  },
  (error) => {
    console.error('API Error:', error);
    console.error('Error response:', error.response);
    return Promise.reject(error);
  }
);

export default api;