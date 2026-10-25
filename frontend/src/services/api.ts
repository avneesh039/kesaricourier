// frontend/src/services/api.ts
import axios from 'axios';
import { handleMockRequest } from './mockData';

const api = axios.create({
  baseURL: (import.meta as any).env?.VITE_API_URL || '/api/v1',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
  },
});

let isRefreshing = false;
let failedQueue: Array<{
  resolve: (value?: unknown) => void;
  reject: (reason?: any) => void;
}> = [];

const processQueue = (error: any, token: string | null = null) => {
  failedQueue.forEach((prom) => {
    if (error) {
      prom.reject(error);
    } else {
      prom.resolve(token);
    }
  });
  failedQueue = [];
};

// Request Interceptor: Attach Access Token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('kesari_token');
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response Interceptor: Handle Token Refresh on 401 & Demo Fallback on 404/405/Network Error
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // 1. Check for Vercel Static Deployments / Unreachable Backend Fallback
    const isOfflineOrStatic = !error.response || error.response.status === 404 || error.response.status === 405 || error.code === 'ERR_NETWORK';
    if (isOfflineOrStatic) {
      const mockResult = handleMockRequest(originalRequest?.url, originalRequest?.method, originalRequest?.data);
      if (mockResult) {
        return Promise.resolve({
          data: mockResult,
          status: 200,
          statusText: 'OK',
          headers: {},
          config: originalRequest,
        });
      }
    }

    // 2. Refresh Token Handling
    if (error.response?.status === 401 && !originalRequest._retry) {
      if (originalRequest.url?.includes('/auth/login') || originalRequest.url?.includes('/auth/refresh-token')) {
        return Promise.reject(error);
      }

      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({ resolve, reject });
        })
          .then((token) => {
            originalRequest.headers.Authorization = `Bearer ${token}`;
            return api(originalRequest);
          })
          .catch((err) => Promise.reject(err));
      }

      originalRequest._retry = true;
      isRefreshing = true;

      try {
        const { data } = await axios.post(
          '/api/v1/auth/refresh-token',
          {},
          { withCredentials: true }
        );

        const newAccessToken = data.data.accessToken;
        localStorage.setItem('kesari_token', newAccessToken);
        api.defaults.headers.common.Authorization = `Bearer ${newAccessToken}`;
        originalRequest.headers.Authorization = `Bearer ${newAccessToken}`;

        processQueue(null, newAccessToken);
        return api(originalRequest);
      } catch (refreshError) {
        processQueue(refreshError, null);
        localStorage.removeItem('kesari_token');
        localStorage.removeItem('kesari_user');
        window.location.href = '/login?expired=1';
        return Promise.reject(refreshError);
      } finally {
        isRefreshing = false;
      }
    }

    return Promise.reject(error);
  }
);

export default api;
