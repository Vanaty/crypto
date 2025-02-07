import axios from 'axios';

const apiClient = axios.create({
  baseURL: new URL(import.meta.env.VITE_API_CRYPTO_URL).origin,
  headers: {
    'Content-Type': 'application/json'
  }
});

export default {
  apiClient
};
