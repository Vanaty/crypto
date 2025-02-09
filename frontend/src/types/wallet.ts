export interface WalletBalance {
    usdBalance: number;
    cryptoHoldings: {
        [cryptoId: string]: number; // Amount of each cryptocurrency held
    };
}

export interface Transaction {
    id: string;
    userId: string | undefined | number;
    deviseId: string | undefined | number;
    type: 'deposit' | 'withdraw';
    amount: number;
    etat: number;
    timestamp: Date | number;
}