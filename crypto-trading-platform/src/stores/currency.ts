import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { CurrencyCode, CurrencyRate } from '../types/currency';

export const useCurrencyStore = defineStore('currency', () => {
  const selectedCurrency = ref<CurrencyCode>('USD');
  
  // Exchange rates relative to USD
  const rates = ref<CurrencyRate>({
    USD: 1,
    EUR: 0.92, // 1 USD = 0.92 EUR
    MGA: 4500 // 1 USD = 4500 MGA
  });

  const symbols: Record<CurrencyCode, string> = {
    USD: '$',
    EUR: '€',
    MGA: 'Ar'
  };

  const convert = (amount: number, from: CurrencyCode = 'USD', to: CurrencyCode = selectedCurrency.value) => {
    if (from === to) return amount;
    const inUSD = amount / rates.value[from];
    return inUSD * rates.value[to];
  };

  const format = (amount: number, currency: CurrencyCode = selectedCurrency.value) => {
    const value = convert(amount, 'USD', currency);
    return `${symbols[currency]}${value.toFixed(currency === 'MGA' ? 0 : 2)}`;
  };

  return {
    selectedCurrency,
    rates,
    symbols,
    convert,
    format
  };
});