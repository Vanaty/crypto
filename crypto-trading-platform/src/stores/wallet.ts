import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { WalletBalance, Transaction } from '../types/wallet';
import { useCryptoStore } from './crypto';

export const useWalletStore = defineStore('wallet', () => {
  const balance = ref<WalletBalance>({
    usdBalance: 10000, // Start with $10,000 USD
    cryptoHoldings: {}
  });

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

  return {
    balance,
    transactions,
    totalPortfolioValue,
    canExecuteTrade,
    executeTrade
  };
});