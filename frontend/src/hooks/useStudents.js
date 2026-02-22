import { useState, useCallback } from 'react';
import { studentService } from '../services/studentService';
import { MESSAGES, PAGINATION } from '../config/constants';

export function useStudents() {
  const [students, setStudents] = useState([]);
  const [meta, setMeta] = useState({
    current_page: 1,
    per_page: PAGINATION.PER_PAGE,
    total: 0,
    page_count: 1,
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchStudents = useCallback(async (page = 1, search = '') => {
    setLoading(true);
    setError(null);

    try {
      const response = await studentService.list(page, search, PAGINATION.PER_PAGE);
      setStudents(response.data);
      setMeta(response.meta);
    } catch {
      setError(MESSAGES.GENERIC_ERROR);
    } finally {
      setLoading(false);
    }
  }, []);

  const deleteStudent = useCallback(async (id) => {
    await studentService.delete(id);
  }, []);

  return {
    students,
    meta,
    loading,
    error,
    fetchStudents,
    deleteStudent,
  };
}

export function useStudentForm() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const getStudent = useCallback(async (id) => {
    setLoading(true);
    setError(null);

    try {
      const response = await studentService.getById(id);
      return response.data;
    } catch {
      setError(MESSAGES.GENERIC_ERROR);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  const saveStudent = useCallback(async (data, id = null) => {
    setLoading(true);
    setError(null);

    try {
      if (id) {
        await studentService.update(id, data);
      } else {
        await studentService.create(data);
      }
      return { success: true };
    } catch (err) {
      if (err.response?.status === 409 && err.response?.data?.status === 'conflict') {
        return {
          success: false,
          conflict: true,
          deletedStudentId: err.response.data.data?.deleted_student_id,
          cpf: err.response.data.data?.cpf,
        };
      }

      const message = err.response?.data?.message || MESSAGES.GENERIC_ERROR;
      setError(message);
      return { success: false };
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    loading,
    error,
    getStudent,
    saveStudent,
  };
}
