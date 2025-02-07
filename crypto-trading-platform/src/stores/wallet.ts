import { defineStore, storeToRefs } from 'pinia';
import { ref, computed, onMounted } from 'vue';
import type { WalletBalance, Transaction } from '../types/wallet';
import { useCryptoStore } from './crypto';
import api from '../services/api';
import { useAuthStore } from './auth';
import { useCurrencyStore } from './currency';

export const useWalletStore = defineStore('wallet', () => {
  const balance = ref<WalletBalance>({
    usdBalance: 0, // Start with $10,000 EUR
    cryptoHoldings: {}
  });

  const { user } = useAuthStore();
  const cryptoStore = useCryptoStore();
  const { transactions } = storeToRefs(cryptoStore);
  const currency = useCurrencyStore();

  const transactionsMoney = ref<Transaction[]>([]);

  const totalPortfolioValue = computed(() => {
    const { cryptos } = useCryptoStore();
    let total = balance.value.usdBalance;

    Object.entries(balance.value.cryptoHoldings).forEach(([cryptoId, amount]) => {
      const crypto = cryptos.find(c => c.id === cryptoId);
      if (crypto) {
        total += crypto.currentPrice * amount;
      }
    });

    return total;
  });

  const canExecuteTrade = (cryptoId: string, type: 'buy' | 'sell', amount: number, price: number) => {
    const total = amount * price;
    
    if (type === 'buy') {
      return balance.value.usdBalance >= total;
    } else {
      return (balance.value.cryptoHoldings[cryptoId] || 0) >= amount;
    }
  };



  const executeTrade  = async (cryptoId: string, type: 'buy' | 'sell', amount: number, price: number) => {
    const total = amount * price;

    if (!canExecuteTrade(cryptoId, type, amount, price)) {
      throw new Error(type === 'buy' ? 'Insufficient USD balance' : 'Insufficient crypto balance');
    }

    // Update USD balance
    balance.value.usdBalance += type === 'sell' ? total : -total;

    // Update crypto holdings
    const currentHolding = balance.value.cryptoHoldings[cryptoId] || 0;
    balance.value.cryptoHoldings[cryptoId] = type === 'buy' 
      ? currentHolding + amount 
      : currentHolding - amount;

    // Record transaction
    const transaction = {
      id: Math.random().toString(36).substr(2, 9),
      userId: user?.id || "", // Replace with actual user ID
      cryptoId,
      type,
      amount,
      price,
      total,
      timestamp: new Date().getTime()
    };

    const response = await api.apiClient.post('/CryptoTransaction', transaction);
    if (response.status !== 200) {
      throw new Error(`Erreur ${response.status}: Échec de l'envoi`);
    } else {
      transactions.value.push(transaction);
    }
  };

  const deposit = async (amount: number) => {
    try {
      const payload = {
        userId: user?.id,
        deviseId: currency.getDeviseSelected()?.id,
        entre: amount,
        sortie: 0,
        timestamp: new Date().getTime()
      };

      console.log(payload);
  
      const response = await api.apiClient.post('/UserTransaction', payload);
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Échec de l'envoi`);
      }
      const trs: Transaction = {
        id: "",
        userId: payload.userId,
        deviseId: payload.deviseId,
        amount: payload.entre,
        type: 'deposit',
        etat: 1,
        timestamp: new Date(payload.timestamp)
      };
      transactionsMoney.value.push(trs);

      console.log('✅ Réponse reçue:', response.data);
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };

  const withdraw = async (amount: number) => {
    try {
      const payload = {
        userId: user?.id,
        deviseId: currency.getDeviseSelected()?.id,
        entre: 0,
        sortie: amount,
        timestamp: new Date().getTime()
      };
  
      const response = await api.apiClient.post('/UserTransaction', payload);
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Échec de l'envoi`);
      }
      const trs: Transaction = {
        id: "",
        userId: payload.userId,
        deviseId: payload.deviseId,
        amount: payload.sortie,
        etat: 1,
        type: 'withdraw',
        timestamp: new Date(payload.timestamp)
      };
      transactionsMoney.value.push(trs);
      console.log('✅ Réponse reçue:', response.data);
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };

  const fetchCompte = async () => {
    try {
      const response = await api.apiClient.get(`user/compte?userId=${user?.id}&idDevise=1`);
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Impossible de charger le compte`);
      }
      balance.value = response.data;
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };

  const fetchHistoTrs = async () => {
    try {
      const response = await api.apiClient.get(`user/${user?.id}/transactions`);
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Impossible de charger le compte`);
      }
      transactionsMoney.value = response.data;
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };
    
  
  setInterval(() => {
    fetchCompte();
    fetchHistoTrs();
  },10000);

  onMounted(() => {
    fetchHistoTrs();
    fetchCompte();
  });
  return {
    balance,
    transactions,
    transactionsMoney,
    totalPortfolioValue,
    canExecuteTrade,
    executeTrade,
    deposit,
    withdraw
  };
});