import { defineStore } from 'pinia';
import { ref, computed, onMounted } from 'vue';
import type { WalletBalance, Transaction } from '../types/wallet';
import { useCryptoStore } from './crypto';
import api from '../services/api';
import { useAuthStore } from './auth';

export const useWalletStore = defineStore('wallet', () => {
  const balance = ref<WalletBalance>({
    usdBalance: 1, // Start with $10,000 EUR
    cryptoHoldings: {}
  });

  const { user } = useAuthStore();

  const transactions = ref<Transaction[]>([]);

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



  const executeTrade = (cryptoId: string, type: 'buy' | 'sell', amount: number, price: number) => {
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
    const transaction: Transaction = {
      id: Math.random().toString(36).substr(2, 9),
      userId: 'user1', // Replace with actual user ID
      cryptoId,
      type,
      amount,
      price,
      total,
      timestamp: new Date()
    };

    transactions.value.push(transaction);
  };

  const deposit = async (amount: number) => {
    try {
      const payload = {
        userId: 123,
        idDevise: 1
      };
  
      const response = await api.apiClient.post('/user/compte', payload, {
        headers: {
          'Content-Type': 'application/json'
        }
      });
  
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Échec de l'envoi`);
      }
  
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
      balance.value.usdBalance = response.data.compte;
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };
    
  
  
  onMounted(() => {
    fetchCompte();
  });
  return {
    balance,
    transactions,
    totalPortfolioValue,
    canExecuteTrade,
    executeTrade,
    deposit
  };
});