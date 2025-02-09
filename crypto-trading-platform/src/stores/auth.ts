import { defineStore } from 'pinia';
import { ref, computed, onMounted} from 'vue';
import api from '../services/api';
import axios from 'axios';
import type { User, LoginCredentials, RegisterCredentials, AuthResponse } from '../types/auth';

const API_URL = import.meta.env.VITE_API_URL; // Replace with your actual API URL

export const useAuthStore = defineStore('auth', () => {
  const loadUser= () => {
    let u = localStorage.getItem('user');
    if (u != null) {
      const x:User = JSON.parse(u);
      return x;
    }
    return null;
  };
  const user = ref<User | null>(loadUser());
  const token = ref<string | null>(localStorage.getItem('token'));
  const isOtpPending = ref(false);
  const isTfaPending = ref(false);
  const tempEmail = ref<string | null>(null);

  const isAuthenticated = computed(() => !!token.value && !!user.value);
  
  const getToken = () => {
    return token.value;
  }

  const setAuthData = (authResponse: AuthResponse) => {
    if (authResponse.token) {
      token.value = authResponse.token;
      user.value = authResponse.user;
      axios.defaults.headers.common['Authorization'] = `Bearer ${authResponse.token}`;
      api.apiClient.defaults.headers.common['Authorization'] = `Bearer ${authResponse.token}`;
      localStorage.setItem('token', authResponse.token);
      localStorage.setItem('user', JSON.stringify(authResponse.user));
    }
  };

  const validateToken = async () => {
    if (!token.value) return false;
    try {
      const response = await axios.post(`${API_URL}/api/token/checked/${token.value}`);
      if (response.data.success) {
        return true;
      }
      logout();
      return false;
    } catch (error) {
      logout();
      return false;
    }
  };

  const register = async (credentials: RegisterCredentials) => {
    try {
      const response = await axios.post(`${API_URL}/api/users/register`, credentials);
      tempEmail.value = credentials.email;
      if (response.data.success) {
        isOtpPending.value = true;
      }
      return response.data;
    } catch (error) {
      throw error;
    }
  };

  const verifyOtp = async (otp: string) => {
    try {
      const response = await axios.post(`${API_URL}/api/users/verify`, {
        email: tempEmail.value,
        pin: otp,
      });
      if (response.data.success) {
        isOtpPending.value = false;
      }
      return response.data;
    } catch (error) {
      throw error;
    }
  };

  const logout = () => {
    user.value = null;
    token.value = null;
    delete axios.defaults.headers.common['Authorization'];
    delete api.apiClient.defaults.headers.common['Authorization'];
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  };

  const initAuth = async () => {
    const savedToken = localStorage.getItem('token');
    const savedUser = localStorage.getItem('user');
    if (savedToken && savedUser) {
      try {
        if (await isTokenValid(savedToken)) {
          token.value = savedToken;
          user.value = JSON.parse(savedUser);
          axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
          api.apiClient.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
          await validateToken();
        } else {
          logout();
        }
      } catch {
        logout();
      }
    }
  };


  // const validateToken = async () => {
  //   if (!token.value) {
  //     return false;
  //   }

  //   try {
  //     return await isTokenValid(token.value);
  //   } catch (error) {
  //     return false;
  //   }
  // };

  const isTokenValid = async (token: string | null) => {
    try {
      const response = await axios.post(`${API_URL}/api/token/checked/${token}`);
      if (response.data.success == true) {
          return true; 
      }
      return false;
    } catch (error) {
      throw error;
    }
  };

  const verifyLoginOtp = async (otp: string) => {
    try {
      const response = await axios.post(`${API_URL}/api/users/login/otp`, {
        email: tempEmail.value,
        pin: otp,
      });
      // setAuthData(response.data);
      if (response.data.success) {
        isOtpPending.value = false;
        setAuthData(response.data.data); 
      }
      return response.data;
    } catch (error) {
      throw error;
    }
  };

  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await axios.post(`${API_URL}/api/users/login`, credentials);
      let authResponse: AuthResponse = response.data;
      authResponse.otpRequired = true;

      if (authResponse.otpRequired && response.data.success) {
        tempEmail.value = credentials.email;
        isOtpPending.value = true;
        return response.data;
      }

      if (authResponse.tfaRequired) {
        tempEmail.value = credentials.email;
        isTfaPending.value = true;
        return response.data;
      }
      return response.data;

      // setAuthData(authResponse);
    } catch (error) {
      throw error;
    }
  };

  const verifyTfa = async (code: string) => {
    try {
      const response = await axios.post(`${API_URL}/auth/verify-2fa`, {
        email: tempEmail.value,
        code
      });
      setAuthData(response.data);
      isTfaPending.value = false;
      return response.data;
    } catch (error) {
      throw error;
    }
  };

  const enableTfa = async () => {
    try {
      const response = await axios.post(`${API_URL}/auth/enable-2fa`, {}, {
        headers: { Authorization: `Bearer ${token.value}` }
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  };

  onMounted( async () => {
    await initAuth();
  });

  return {
    user,
    isAuthenticated,
    isOtpPending,
    isTfaPending,
    getToken,
    register,
    login,
    logout,
    verifyOtp,
    verifyLoginOtp,
    verifyTfa,
    enableTfa,
    initAuth
  };
});