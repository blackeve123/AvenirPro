import api from './api';

export const login = (credentials) => api.post('/login', credentials);
export const register = (userData) => api.post('/register', userData);
export const logout = () => api.post('/logout');
export const fetchCurrentUser = () => api.get('/me');
export const updateProfile = (data) => api.put('/me', data);
