import { defineStore } from 'pinia';
import { onMounted, ref } from 'vue';
import type { CurrencyCode, CurrencyData, CurrencyRate } from '../types/currency';
import api from '../services/api';

export const useCurrencyStore = defineStore('currency', () => {
  const selectedCurrency = ref<CurrencyCode>('USD');
  
  // Exchange rates relative to USD
  const rates = ref<CurrencyRate>({
    USD: 1,
    EUR: 0.92,
    MGA: 4500
  });

  const devises = ref<CurrencyData[]>();

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
    const value = convert(amount, 'EUR', currency);
    return `${symbols[currency]}${value.toFixed(currency === 'MGA' ? 0 : 2)}`;
  };

  const getDeviseSelected = () => {
    return devises.value?.find(x => x.nom === selectedCurrency.value);
  };

  const fetchCurrency = async () => {
    try {
      const response = await api.apiClient.get<CurrencyData[]>("/devises");
      if (response.status !== 200) {
        throw new Error(`Erreur ${response.status}: Impossible de charger les devises`);
      }
  
      if (!response.data || !Array.isArray(response.data)) {
        throw new Error("Données de devises invalides");
      }
  
      devises.value = response.data;
  
      // Mise à jour des taux en fonction des données reçues
      for (const currency of devises.value) {
        if (currency.nom in rates.value) {
          rates.value[currency.nom as CurrencyCode] = currency.valeur;
        }
      }
    } catch (error) {
      console.error('❌ Erreur API:', error);
    }
  };
  


  onMounted(() => {
    fetchCurrency();
  });

  return {
    selectedCurrency,
    rates,
    symbols,
    convert,
    format,
    getDeviseSelected
  };
});