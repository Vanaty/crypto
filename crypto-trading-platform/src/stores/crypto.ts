import { defineStore } from 'pinia';
import { ref, onMounted } from 'vue';
import type { CryptoData, Transaction } from '../types/crypto';

const API_URL = new URL(import.meta.env.VITE_API_CRYPTO_URL).origin; // Replace with your actual API URL

export const useCryptoStore = defineStore('crypto', () => {
  const cryptos = ref<CryptoData[]>([]);
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
  });

  return {
    cryptos,
    transactions,
    addTransaction
  };
});
