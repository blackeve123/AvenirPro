import { defineStore } from 'pinia';
import { login, register, logout, fetchCurrentUser } from '../services/auth';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: localStorage.getItem('isLoggedIn') === 'true',
  }),
  getters: {
    isAuthenticated: (state) => state.isLoggedIn || !!state.user,
    isAdmin: (state) => state.user?.role === 'admin' || state.user?.role?.name === 'admin' || state.user?.role_id === 1,
  },
  actions: {
    async fetchCsrfCookie() {
      // Fetch the CSRF cookie from the Sanctum endpoint dynamically based on current host
      const host = window.location.hostname;
      await axios.get(`http://${host}:8000/sanctum/csrf-cookie`, {
        withCredentials: true
      });
    },
    async loginUser(credentials) {
      await this.fetchCsrfCookie();
      const response = await login(credentials);
      const data = response.data.data || response.data;
      this.user = data.user;
      this.isLoggedIn = true;
      const token = data.token;
      if (token) {
        localStorage.setItem('token', token);
      }
      localStorage.setItem('isLoggedIn', 'true');
    },
    async registerUser(userData) {
      await this.fetchCsrfCookie();
      const response = await register(userData);
      const data = response.data.data || response.data;
      this.user = data.user;
      this.isLoggedIn = true;
      const token = data.token;
      if (token) {
        localStorage.setItem('token', token);
      }
      localStorage.setItem('isLoggedIn', 'true');
    },
    async fetchUser() {
      if (!this.isLoggedIn) return;
      try {
        const response = await fetchCurrentUser();
        this.user = response.data.data || response.data.user || response.data;
      } catch (error) {
        this.clearAuth();
      }
    },
    async logoutUser() {
      try {
        await logout();
      } catch (e) {
        // ignore error on logout
      } finally {
        this.clearAuth();
      }
    },
    clearAuth() {
      localStorage.removeItem('isLoggedIn');
      localStorage.removeItem('token');
    }
  }
});
