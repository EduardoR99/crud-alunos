import { Link } from 'react-router-dom';
import ThemeSwitch from './ThemeSwitch';

export default function Header({ userName, onLogout }) {
  return (
    <nav className="bg-white dark:bg-gray-800 shadow-md">
      <div className="px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16 items-center gap-4">
          <div className="flex items-center gap-4 sm:gap-6 min-w-0">
            <Link
              to="/"
              className="text-lg sm:text-xl font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap"
            >
              Gestão de Alunos
            </Link>
            <Link
              to="/students"
              className="text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors whitespace-nowrap hidden sm:block"
            >
              Alunos
            </Link>
          </div>

          <div className="flex items-center gap-2 sm:gap-4 shrink-0">
            <ThemeSwitch />

            <span className="text-sm text-gray-600 dark:text-gray-400 truncate max-w-[120px] sm:max-w-[200px] hidden sm:block">
              {userName}
            </span>

            <button
              onClick={onLogout}
              className="px-3 sm:px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors whitespace-nowrap"
            >
              Sair
            </button>
          </div>
        </div>
      </div>
    </nav>
  );
}
