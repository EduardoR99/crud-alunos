import api from './api';
import { PAGINATION } from '../config/constants';

export const studentService = {
  async list(page = 1, search = '', perPage = PAGINATION.PER_PAGE) {
    const response = await api.get('/students', {
      params: { page, search, per_page: perPage },
    });
    return response.data;
  },

  async getById(id) {
    const response = await api.get(`/students/${id}`);
    return response.data;
  },

  async create(data) {
    const response = await api.post('/students', data);
    return response.data;
  },

  async update(id, data) {
    const response = await api.put(`/students/${id}`, data);
    return response.data;
  },

  async delete(id) {
    const response = await api.delete(`/students/${id}`);
    return response.data;
  },
};
