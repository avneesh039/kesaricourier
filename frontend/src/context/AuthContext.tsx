// frontend/src/context/AuthContext.tsx
import React, { createContext, useContext, useState, useEffect } from 'react';
import api from '../services/api';
import { User, Role } from '../types';

interface AuthContextType {
  user: User | null;
  role: Role | null;
  accessToken: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (email: string, password: string, tenantSlug?: string) => Promise<User>;
  logout: () => Promise<void>;
  refreshMe: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [accessToken, setAccessToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const refreshMe = async () => {
    try {
      const token = localStorage.getItem('kesari_token');
      const savedUserStr = localStorage.getItem('kesari_user');
      if (!token) {
        setIsLoading(false);
        return;
      }
      setAccessToken(token);

      if (savedUserStr) {
        try {
          setUser(JSON.parse(savedUserStr));
        } catch (e) {}
      }

      const res = await api.get('/auth/me');
      if (res.data?.success && res.data.data) {
        const u = res.data.data;
        const mappedUser: User = {
          id: u.id,
          name: u.name,
          email: u.email,
          phone: u.phone,
          role: u.role,
          status: u.status,
          tenantId: u.tenant?.id || u.tenantId,
          branchId: u.branch?.id || u.branchId,
          branchName: u.branch?.name || u.branchName,
          tenantName: u.tenant?.name || u.tenantName,
          tradeName: u.tenant?.tradeName || u.tradeName,
          agencyCode: u.tenant?.agencyCode || u.agencyCode,
          brandColor: u.tenant?.brandColor || u.brandColor,
        };
        setUser(mappedUser);
        localStorage.setItem('kesari_user', JSON.stringify(mappedUser));
      }
    } catch (err) {
      console.warn('Backend offline, keeping cached user session:', err);
      const savedUserStr = localStorage.getItem('kesari_user');
      if (savedUserStr) {
        try {
          setUser(JSON.parse(savedUserStr));
        } catch (e) {
          setUser(null);
          setAccessToken(null);
        }
      }
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    refreshMe();
  }, []);

  const login = async (email: string, password: string, tenantSlug?: string): Promise<User> => {
    setIsLoading(true);
    try {
      const res = await api.post('/auth/login', { email, password, tenantSlug });
      const { accessToken: token, user: loggedUser } = res.data.data;

      setAccessToken(token);
      setUser(loggedUser);
      localStorage.setItem('kesari_token', token);
      localStorage.setItem('kesari_user', JSON.stringify(loggedUser));
      return loggedUser;
    } finally {
      setIsLoading(false);
    }
  };

  const logout = async () => {
    try {
      await api.post('/auth/logout');
    } catch (e) {
      // Ignore network errors on logout
    } finally {
      setUser(null);
      setAccessToken(null);
      localStorage.removeItem('kesari_token');
      localStorage.removeItem('kesari_user');
      window.location.href = '/login';
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        role: user?.role || null,
        accessToken,
        isAuthenticated: !!user,
        isLoading,
        login,
        logout,
        refreshMe,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
