import { defineStore } from 'pinia';
import { ref, computed} from 'vue';
import axios from 'axios';
import type { User, LoginCredentials, RegisterCredentials, AuthResponse } from '../types/auth';
import { jwtDecode } from 'jwt-decode';

const API_URL = 'http://localhost:8080'; // Replace with your actual API URL

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isOtpPending = ref(false);
  const isTfaPending = ref(false);
  const tempEmail = ref<string | null>(null);

  const isAuthenticated = computed(() => !!token.value);
  // const isAuthenticated = computed(() => validateToken());

  const setAuthData = (authResponse: AuthResponse) => {
    if (authResponse.token) {
      token.value = authResponse.token;
      user.value = authResponse.user;
      axios.defaults.headers.common['Authorization'] = `Bearer ${authResponse.token}`;
      localStorage.setItem('token', authResponse.token);
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

  // const isTokenValid = async (token: string | null) => {
  //   try {
  //     const response = await axios.post(`${API_URL}/api/token/checked/${token}`);
  //     if (response.data.success == true) {
  //         return true; 
  //     }
  //     return false;
  //   } catch (error) {
  //     throw error;
  //   }
  // };

  const register = async (credentials: RegisterCredentials) => {
    try {
      const response = await axios.post(`${API_URL}/api/users/register`, credentials);
      tempEmail.value = credentials.email;
      if (response.data.success == true) {
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
      // setAuthData(response.data);
      if (response.data.success) {
        isOtpPending.value = false; 
      }
      return response.data;
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

  const logout = () => {
    user.value = null;
    token.value = null;
    delete axios.defaults.headers.common['Authorization'];
    localStorage.removeItem('token');
  };

  // Initialize auth state from localStorage
  const initAuth = () => {
    const savedToken = localStorage.getItem('token');
    if (savedToken) {
      try {
        const decoded = jwtDecode(savedToken);
        if (decoded.exp && decoded.exp * 1000 > Date.now()) {
          token.value = savedToken;
          axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
        } else {
          localStorage.removeItem('token');
        }
      } catch {
        localStorage.removeItem('token');
      }
    }
  };

  return {
    user,
    isAuthenticated,
    isOtpPending,
    isTfaPending,
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