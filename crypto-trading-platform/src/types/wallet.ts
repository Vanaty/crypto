export interface WalletBalance {
    usdBalance: number;
    cryptoHoldings: {
        [cryptoId: string]: number; // Amount of each cryptocurrency held
    };
}

export interface Transaction {
    id: string;
    userId: string;
    cryptoId: string;
    type: 'buy' | 'sell';
    amount: number;
    price: number;
    total: number;
    timestamp: Date;
}