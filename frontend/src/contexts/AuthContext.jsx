import { createContext, useContext, useState, useCallback, useMemo, useEffect } from 'react';
import { authService } from '../services/authService';
import { setOnUnauthorized } from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setOnUnauthorized(() => setUser(null));

    authService.me()
      .then((response) => setUser(response.data.user))
      .catch(() => setUser(null))
      .finally(() => setLoading(false));
  }, []);

  const isAuthenticated = !!user;

  const login = useCallback(async (email, password) => {
    const response = await authService.login(email, password);
    const { user: userData } = response.data;
    setUser(userData);
    return response;
  }, []);

  const register = useCallback(async (nomeCompleto, email, password) => {
    const response = await authService.register(nomeCompleto, email, password);
    const { user: userData } = response.data;
    setUser(userData);
    return response;
  }, []);

  const logout = useCallback(async () => {
    try {
      await authService.logout();
    } catch {
      // Mesmo se falhar, limpa o estado local
    }
    setUser(null);
  }, []);

  const clearAuth = useCallback(() => {
    setUser(null);
  }, []);

  const value = useMemo(() => ({
    user,
    loading,
    isAuthenticated,
    login,
    register,
    logout,
    clearAuth,
  }), [user, loading, isAuthenticated, login, register, logout, clearAuth]);

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error('useAuth deve ser usado dentro de AuthProvider');
  }

  return context;
}
