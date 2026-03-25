import axios from 'axios';
import { useToast } from 'vue-toastification';

const host = window.location.hostname;

const api = axios.create({
  baseURL: `http://${host}:8000/api/v1`,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

api.interceptors.response.use(
  (response) => response,
  (error) => {
    try {
      const toast = useToast();
      if (error.response && error.response.status >= 500) {
        toast.error("Erreur serveur interne.");
      } else if (error.response && error.response.status === 403) {
        toast.error("Accès refusé.");
      }
    } catch (e) {
      // Ignore if outside Vue setup context without global injection
    }

    if (error.response && error.response.status === 401) {
      localStorage.removeItem('isLoggedIn');
      // Redirect to login if unauthorized, preserving the current location
      if (window.location.pathname !== '/login') {
        const currentPath = window.location.pathname + window.location.search;
        window.location.href = `/login?redirect=${encodeURIComponent(currentPath)}`;
      }
    }

    return Promise.reject(error);
  }
);

export default api;
