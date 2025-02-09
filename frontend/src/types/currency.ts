export type CurrencyCode = 'USD' | 'EUR' | 'MGA';

export interface CurrencyRate {
  USD: number;
  EUR: number;
  MGA: number;
}

export interface CurrencySymbol {
  USD: string;
  EUR: string;
  MGA: string;
}

export interface CurrencyData {
  id: number;
  nom: string;
  valeur: number;
}