import api from './api';

export const authService = {
  async login(email, password) {
    const response = await api.post('/auth/login', { email, password });
    return response.data;
  },

  async register(nome_completo, email, password) {
    const response = await api.post('/auth/register', { nome_completo, email, password });
    return response.data;
  },

  async me() {
    const response = await api.get('/auth/me');
    return response.data;
  },

  async logout() {
    const response = await api.post('/auth/logout');
    return response.data;
  },
};
