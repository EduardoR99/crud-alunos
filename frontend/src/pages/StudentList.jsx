import { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import toast from 'react-hot-toast';
import { studentService } from '../services/studentService';
import { MESSAGES, PAGINATION } from '../config/constants';
import ConfirmModal from '../components/ConfirmModal';

function maskCpf(cpf) {
  if (!cpf || cpf.length < 11) return cpf;
  return `***.${cpf.slice(4, 7)}.***-${cpf.slice(-2)}`;
}

export default function StudentList() {
  const [students, setStudents] = useState([]);
  const [meta, setMeta] = useState({ current_page: 1, page_count: 1, total: 0 });
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [confirmDelete, setConfirmDelete] = useState({ isOpen: false, studentId: null });

  const fetchStudents = useCallback(async (page = 1, searchTerm = '') => {
    setLoading(true);
    try {
      const response = await studentService.list(page, searchTerm, PAGINATION.PER_PAGE);
      setStudents(response.data);
      setMeta(response.meta);
    } catch {
      toast.error(MESSAGES.GENERIC_ERROR);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchStudents();
  }, [fetchStudents]);

  const handleSearch = (e) => {
    e.preventDefault();
    fetchStudents(1, search);
  };

  const openDeleteConfirm = (id) => {
    setConfirmDelete({ isOpen: true, studentId: id });
  };

  const closeDeleteConfirm = () => {
    setConfirmDelete({ isOpen: false, studentId: null });
  };

  const handleDelete = async () => {
    try {
      await studentService.delete(confirmDelete.studentId);
      toast.success(MESSAGES.STUDENT_DELETED);
      fetchStudents(meta.current_page, search);
    } catch {
      toast.error(MESSAGES.GENERIC_ERROR);
    }
  };

  const goToPage = (page) => {
    fetchStudents(page, search);
  };

  return (
    <div>
      <ConfirmModal
        isOpen={confirmDelete.isOpen}
        onClose={closeDeleteConfirm}
        onConfirm={handleDelete}
        title="Excluir Aluno"
        message={MESSAGES.CONFIRM_DELETE}
        confirmText="Excluir"
        cancelText="Cancelar"
        variant="danger"
      />

      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Alunos</h1>
        <Link
          to="/students/new"
          className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors"
        >
          + Novo Aluno
        </Link>
      </div>

      <form onSubmit={handleSearch} className="mb-6 flex gap-2">
        <input
          type="text"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Buscar por nome ou CPF..."
          className="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-colors"
        />
        <button
          type="submit"
          className="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors"
        >
          Buscar
        </button>
      </form>

      <div className="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead className="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Nome
                </th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  CPF
                </th>
                <th className="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
              {loading ? (
                <tr>
                  <td colSpan={3} className="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    Carregando...
                  </td>
                </tr>
              ) : students.length === 0 ? (
                <tr>
                  <td colSpan={3} className="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    Nenhum aluno encontrado.
                  </td>
                </tr>
              ) : (
                students.map((student) => (
                  <tr key={student.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td className="px-6 py-4 text-sm text-gray-900 dark:text-white">
                      {student.nome_completo}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                      {maskCpf(student.cpf)}
                    </td>
                    <td className="px-6 py-4 text-sm text-right space-x-2">
                      <Link
                        to={`/students/${student.id}/edit`}
                        className="text-emerald-600 dark:text-emerald-400 hover:underline"
                      >
                        Editar
                      </Link>
                      <button
                        onClick={() => openDeleteConfirm(student.id)}
                        className="text-red-600 dark:text-red-400 hover:underline"
                      >
                        Excluir
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {meta.page_count > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <span className="text-sm text-gray-600 dark:text-gray-400">
              {meta.total} aluno(s) encontrado(s)
            </span>
            <div className="flex gap-1">
              {Array.from({ length: meta.page_count }, (_, i) => i + 1).map((page) => (
                <button
                  key={page}
                  onClick={() => goToPage(page)}
                  className={`px-3 py-1 text-sm rounded ${
                    page === meta.current_page
                      ? 'bg-emerald-600 text-white'
                      : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
                  } transition-colors`}
                >
                  {page}
                </button>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
