export interface PriceHistory {
  date: Date;
  price: number;
}
export interface CryptoData {
  id: string;
  name: string;
  symbol: string;
  currentPrice: number;
  priceHistory: PriceHistory[];
  change24h: number;
}

export interface Transaction {
  id: string;
  userId: string;
  cryptoId: string;
  type: 'buy' | 'sell';
  amount: number;
  price: number;
  timestamp: Date;
}