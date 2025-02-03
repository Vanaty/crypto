import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { CryptoData, Transaction } from '../types/crypto';

const INITIAL_CRYPTOS: CryptoData[] = [
  { id: 'bitcoin', name: 'Bitcoin', symbol: 'BTC', currentPrice: 50000, priceHistory: [{ date: new Date(), price:50000 }], change24h: 0 },
  { id: 'ethereum', name: 'Ethereum', symbol: 'ETH', currentPrice: 3000, priceHistory: [{ date: new Date(), price:3000 }], change24h: 0 },
  { id: 'cardano', name: 'Cardano', symbol: 'ADA', currentPrice: 1.2, priceHistory: [{ date: new Date(), price:1.2 }], change24h: 0 },
  { id: 'solana', name: 'Solana', symbol: 'SOL', currentPrice: 100, priceHistory: [{ date: new Date(), price:100 }], change24h: 0 },
  { id: 'polkadot', name: 'Polkadot', symbol: 'DOT', currentPrice: 20, priceHistory: [{ date: new Date(), price:20 }], change24h: 0 },
  { id: 'dogecoin', name: 'Dogecoin', symbol: 'DOGE', currentPrice: 0.15, priceHistory: [{ date: new Date(), price:0.15 }], change24h: 0 },
  { id: 'ripple', name: 'Ripple', symbol: 'XRP', currentPrice: 0.75, priceHistory: [{ date: new Date(), price:0.75 }], change24h: 0 },
  { id: 'chainlink', name: 'Chainlink', symbol: 'LINK', currentPrice: 15, priceHistory: [{ date: new Date(), price:15 }], change24h: 0 },
  { id: 'litecoin', name: 'Litecoin', symbol: 'LTC', currentPrice: 150, priceHistory: [{ date: new Date(), price:150 }], change24h: 0 },
  { id: 'uniswap', name: 'Uniswap', symbol: 'UNI', currentPrice: 10, priceHistory: [{ date: new Date(), price:10 }], change24h: 0 },
];

export const useCryptoStore = defineStore('crypto', () => {
  const cryptos = ref<CryptoData[]>(INITIAL_CRYPTOS);
  const transactions = ref<Transaction[]>([]);

  // Update crypto prices every 10 seconds
  setInterval(() => {
    cryptos.value = cryptos.value.map(crypto => {
      const volatility = crypto.currentPrice * 0.02; // 2% volatility
      const change = (Math.random() - 0.5) * volatility;
      const newPrice = Math.max(crypto.currentPrice + change, 0.000001);
      const newPrices = {date:new Date(), price: newPrice};
      const change24h = ((newPrice - crypto.priceHistory[0].price) / crypto.priceHistory[0].price) * 100;
      
      return {
        ...crypto,
        currentPrice: newPrice,
        priceHistory: [...crypto.priceHistory, newPrices].slice(-144), // Keep last 24h (144 * 10 seconds)
        change24h
      };
    });
  }, 10000);

  const addTransaction = (transaction: Omit<Transaction, 'id'>) => {
    transactions.value.push({
      ...transaction,
      id: Math.random().toString(36).substr(2, 9)
    });
  };

  return {
    cryptos,
    transactions,
    addTransaction
  };
});