import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import ThemeSwitch from '../components/ThemeSwitch';
import PasswordInput from '../components/PasswordInput';
import PasswordStrength, { isPasswordValid } from '../components/PasswordStrength';
import { MESSAGES } from '../config/constants';

export default function Register() {
  const [nomeCompleto, setNomeCompleto] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirm, setPasswordConfirm] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const { register } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!isPasswordValid(password)) {
      setError('A senha não atende todos os requisitos.');
      return;
    }

    if (password !== passwordConfirm) {
      setError('As senhas não coincidem.');
      return;
    }

    setLoading(true);

    try {
      await register(nomeCompleto, email, password);
      navigate('/students');
    } catch (err) {
      const message = err.response?.data?.message
        || err.response?.data?.errors?.password
        || err.response?.data?.errors?.email
        || MESSAGES.GENERIC_ERROR;
      setError(message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 transition-colors">
      <div className="absolute top-4 right-4">
        <ThemeSwitch />
      </div>

      <div className="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg w-full max-w-md">
        <h1 className="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">
          Criar Conta
        </h1>

        {error && (
          <div className="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg text-sm">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label
              htmlFor="nome_completo"
              className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Nome Completo
            </label>
            <input
              id="nome_completo"
              type="text"
              required
              minLength={3}
              value={nomeCompleto}
              onChange={(e) => setNomeCompleto(e.target.value)}
              className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-colors"
              placeholder="Seu nome completo"
            />
          </div>

          <div>
            <label
              htmlFor="email"
              className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              E-mail
            </label>
            <input
              id="email"
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-colors"
              placeholder="seu@email.com"
            />
          </div>

          <div>
            <label
              htmlFor="password"
              className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Senha
            </label>
            <PasswordInput
              id="password"
              required
              minLength={8}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
            <PasswordStrength password={password} />
          </div>

          <div>
            <label
              htmlFor="password_confirm"
              className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
              Confirmar Senha
            </label>
            <PasswordInput
              id="password_confirm"
              required
              minLength={8}
              value={passwordConfirm}
              onChange={(e) => setPasswordConfirm(e.target.value)}
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white font-medium rounded-lg transition-colors"
          >
            {loading ? 'Criando conta...' : 'Registrar'}
          </button>
        </form>

        <p className="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
          Já tem uma conta?{' '}
          <Link to="/login" className="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
            Entrar
          </Link>
        </p>
      </div>
    </div>
  );
}
