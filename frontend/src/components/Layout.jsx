import { Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import Header from './Header';
import Footer from './Footer';

export default function Layout() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900 transition-colors">
      <Header userName={user?.nome_completo} onLogout={handleLogout} />
      <main className="flex-1 w-full max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Outlet />
      </main>
      <Footer />
    </div>
  );
}
