<template>
    <div class="container py-4">
      <div class="row g-4">
        <!-- Currency Selection -->
        <div class="col-12">
          <div class="card shadow">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h2 class="card-title h4 mb-0">Portfolio Summary</h2>
                <div class="btn-group">
                  <button 
                    v-for="currency in ['USD', 'EUR', 'MGA']" 
                    :key="currency"
                    class="btn btn-outline-primary"
                    :class="{ active: selectedCurrency === currency }"
                    @click="selectedCurrency = currency as CurrencyCode"
                  >
                    {{ currency }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Portfolio Summary -->
        <div class="col-12">
          <div class="card shadow">
            <div class="card-body">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="border rounded p-3">
                    <h3 class="h6 text-muted mb-2">Balance</h3>
                    <p class="h3 mb-0">{{ formatCurrency(balance.usdBalance) }}</p>
                    <button class="btn btn-sm btn-outline-success mt-2" @click="openDepositModal">Deposit Funds</button>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="border rounded p-3">
                    <h3 class="h6 text-muted mb-2">Total Portfolio Value</h3>
                    <p class="h3 mb-0">{{ formatCurrency(totalPortfolioValue) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Deposit Modal -->
        <div class="modal fade" id="depositModal" tabindex="-1" ref="depositModalRef">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Deposit Funds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="depositFunds">
                  <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input 
                      type="number" 
                      class="form-control" 
                      v-model="depositAmount"
                      min="1"
                      required
                    />
                  </div>
                  <div class="d-grid">
                    <button type="submit" class="btn btn-success">Deposit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Crypto Holdings -->
        <div class="col-12">
          <div class="card shadow">
            <div class="card-body">
              <h2 class="card-title h4 mb-4">Cryptocurrency Holdings</h2>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Cryptocurrency</th>
                      <th>Amount</th>
                      <th>Current Value</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="crypto in cryptos" :key="crypto.id">
                      <td>{{ crypto.name }}</td>
                      <td>{{ (balance.cryptoHoldings[crypto.id] || 0).toFixed(8) }}</td>
                      <td>{{ formatCurrency((balance.cryptoHoldings[crypto.id] || 0) * crypto.currentPrice) }}</td>
                      <td>
                        <button 
                          class="btn btn-sm btn-outline-primary me-2"
                          @click="openTradeModal(crypto.id, 'buy')"
                        >
                          Buy
                        </button>
                        <button 
                          class="btn btn-sm btn-outline-danger"
                          @click="openTradeModal(crypto.id, 'sell')"
                          :disabled="!balance.cryptoHoldings[crypto.id]"
                        >
                          Sell
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Trade Modal -->
      <div class="modal fade" id="tradeModal" tabindex="-1" ref="modalRef">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                {{ tradeType === 'buy' ? 'Buy' : 'Sell' }} 
                {{ selectedCrypto?.name }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="executeTrade">
                <div class="mb-3">
                  <label class="form-label">Amount</label>
                  <input 
                    type="number" 
                    class="form-control" 
                    v-model="tradeAmount"
                    step="0.00000001"
                    min="0"
                    required
                  />
                </div>
                <div class="mb-3">
                  <label class="form-label">Price per unit</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    :value="selectedCrypto ? formatCurrency(selectedCrypto.currentPrice) : ''"
                    disabled
                  />
                </div>
                <div class="mb-3">
                  <label class="form-label">Total</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    :value="formatCurrency(tradeTotal)"
                    disabled
                  />
                </div>
                <div class="d-grid">
                  <button 
                    type="submit" 
                    class="btn"
                    :class="tradeType === 'buy' ? 'btn-primary' : 'btn-danger'"
                    :disabled="!canTrade"
                  >
                    {{ tradeType === 'buy' ? 'Buy' : 'Sell' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue';
  import { storeToRefs } from 'pinia';
  import { Modal } from 'bootstrap';
  import { useCryptoStore } from '../stores/crypto';
  import { useWalletStore } from '../stores/wallet';
  import { useCurrencyStore } from '../stores/currency';
  import type { CurrencyCode } from '../types/currency';
  
  const cryptoStore = useCryptoStore();
  const walletStore = useWalletStore();
  const currencyStore = useCurrencyStore();
  
  const { cryptos } = storeToRefs(cryptoStore);
  const { balance, totalPortfolioValue } = storeToRefs(walletStore);
  const { selectedCurrency } = storeToRefs(currencyStore);
  
  const modalRef = ref<HTMLElement | null>(null);
  const modal = ref<Modal | null>(null);
  const depositModalRef = ref<HTMLElement | null>(null);
  const depositModal = ref<Modal | null>(null);
  const selectedCryptoId = ref<string | null>(null);
  const tradeType = ref<'buy' | 'sell'>('buy');
  const tradeAmount = ref(0);
  const depositAmount = ref(0);
  
  const selectedCrypto = computed(() => 
    cryptos.value.find(c => c.id === selectedCryptoId.value)
  );
  
  const tradeTotal = computed(() => 
    tradeAmount.value * (selectedCrypto.value?.currentPrice || 0)
  );
  
  const formatCurrency = (amount: number) => {
    return currencyStore.format(amount);
  };
  
  const canTrade = computed(() => {
    if (!selectedCryptoId.value || !selectedCrypto.value) return false;
    return walletStore.canExecuteTrade(
      selectedCryptoId.value,
      tradeType.value,
      tradeAmount.value,
      selectedCrypto.value.currentPrice
    );
  });
  
  const openTradeModal = (cryptoId: string, type: 'buy' | 'sell') => {
    selectedCryptoId.value = cryptoId;
    tradeType.value = type;
    tradeAmount.value = 0;
    
    if (!modal.value && modalRef.value) {
      modal.value = new Modal(modalRef.value);
    }
    modal.value?.show();
  };
  
  const executeTrade = async () => {
    if (!selectedCryptoId.value || !selectedCrypto.value) return;
  
    try {
      await walletStore.executeTrade(
        selectedCryptoId.value,
        tradeType.value,
        tradeAmount.value,
        selectedCrypto.value.currentPrice
      );
      modal.value?.hide();
    } catch (error) {
      alert(error instanceof Error ? error.message : 'Trade failed');
    }
  };


  const openDepositModal = () => {
    depositAmount.value = 0;
    if (!depositModal.value && depositModalRef.value) {
      depositModal.value = new Modal(depositModalRef.value);
    }
    depositModal.value?.show();
  };
  
  const depositFunds = async () => {
    if (depositAmount.value <= 0) return;
  
    try {
      await walletStore.deposit(depositAmount.value);
      depositModal.value?.hide();
    } catch (error) {
      alert(error instanceof Error ? error.message : 'Deposit failed');
    }
  };
  </script>