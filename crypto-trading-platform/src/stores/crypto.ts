import { defineStore } from 'pinia';
import { ref, onMounted } from 'vue';
import type { CryptoData, Transaction } from '../types/crypto';
import api from '../services/api';
import { useAuthStore } from './auth';

const API_URL = new URL(import.meta.env.VITE_API_CRYPTO_URL).origin;

export const useCryptoStore = defineStore('crypto', () => {
  const cryptos = ref<CryptoData[]>([]);
  const { user } = useAuthStore();
  const transactions = ref<Transaction[]>([]);
  // Fonction pour récupérer les cryptos depuis l'API
  const fetchCryptos = async () => {
    try {
      const response = await fetch(API_URL+"/cryptos");
      if (!response.ok) throw new Error('Erreur lors du chargement des cryptos');
      cryptos.value = await response.json();
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };

  const fetchTransactions = async () => {
    try {
      const response = await api.apiClient.get<Transaction[]>(`/ListeCryptoTransaction?userId=${user?.id}`);
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Impossible de charger les historiques cryptos`);
      }
  
      if (!response.data || !Array.isArray(response.data)) {
        throw new Error("Données de devises invalides");
      }
      transactions.value = response.data;
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };
  setInterval(() => {
    fetchCryptos();
  },5000);

  // Ajouter une transaction
  const addTransaction = (transaction: Omit<Transaction, 'id'>) => {
    transactions.value.push({
      ...transaction,
      id: Math.random().toString(36).substr(2, 9)
    });
  };

  // Charger les données initiales au montage
  onMounted(() => {
    fetchCryptos();
    fetchTransactions();
  });

  return {
    cryptos,
    transactions,
    addTransaction
  };
});
