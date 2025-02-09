<template>
  <div class="container py-4" v-if="crypto">
    <div class="card shadow">
      <div class="card-body">
        <h1 class="display-5 mb-4">{{ crypto.name }} ({{ crypto.symbol }})</h1>
        
        <div class="mb-4">
          <Line :data="chartData" :options="chartOptions" class="h-64" />
        </div>

        <div class="row g-4">
          <div class="col-12">
            <div class="p-3 border rounded bg-light text-center">
              <h5 class="text-muted">Your Balance</h5>
              <p class="display-6">{{ formatCurrency(balance.usdBalance) }}</p>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="p-3 border rounded bg-light">
              <p class="display-6">{{ formatCurrency(crypto.currentPrice) }}</p>
              <p :class="crypto.change24h >= 0 ? 'text-success' : 'text-danger'">
                <i :class="crypto.change24h >= 0 ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
                {{ Math.abs(crypto.change24h).toFixed(2) }}%
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Trade</h5>
                <div v-if="isLoading" class="text-center my-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <p>Processing trade...</p>
                </div>
                <div v-else>
                  <div class="mb-3 d-flex">
                    <button class="btn btn-outline-secondary me-2" @click="setMinTrade">Min</button>
                    <input type="number" v-model="amount" class="form-control"
                           :max="getMaxTrade()"
                           placeholder="Amount to trade" />
                    <button class="btn btn-outline-secondary ms-2" @click="setMaxTrade">Max</button>
                  </div>
                  <div class="d-grid gap-2 d-md-flex">
                    <button @click="executeTrade('buy')" 
                            class="btn btn-success flex-grow-1">
                      Buy
                    </button>
                    <button @click="executeTrade('sell')" 
                            class="btn btn-danger flex-grow-1">
                      Sell
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useCryptoStore } from '../stores/crypto';
import { useWalletStore } from '../stores/wallet';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';
import { useCurrencyStore } from '../stores/currency';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const route = useRoute();
const cryptoStore = useCryptoStore();
const walletStore = useWalletStore();
const currencyStore = useCurrencyStore();
const { cryptos } = storeToRefs(cryptoStore);
const { balance } = storeToRefs(walletStore);
const amount = ref(0);
const isLoading = ref(false);

const crypto = computed(() => 
  cryptos.value.find(c => c.id == route.params.id)
);

const chartData = computed(() => {
  const history = crypto.value?.priceHistory || [];
  return {
    labels: history.map(entry => 
      new Date(entry.date).toLocaleDateString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit' 
      })
    ),
    datasets: [{
      label: 'Price',
      data: history.map(entry => entry.price),
      borderColor: '#198754',
      tension: 0.1
    }]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

const formatCurrency = (amount: number) => {
  return currencyStore.format(amount);
};

const setMinTrade = () => {
  amount.value = 0.01;
};

const setMaxTrade = () => {
  if (crypto.value) {
    amount.value = parseFloat((balance.value.usdBalance / crypto.value.currentPrice).toFixed(6));
  }
};
const getMaxTrade = () => {
  if (crypto.value) {
    return parseFloat((balance.value.usdBalance / crypto.value.currentPrice).toFixed(6));    
  }
  return 0;
}
const executeTrade = async (type: 'buy' | 'sell') => {
  if (!crypto.value || amount.value <= 0 || amount.value > getMaxTrade()) return;
  
  isLoading.value = true;
  try {
    await walletStore.executeTrade(
      crypto.value.id,
      type,
      parseFloat(amount.value.toFixed(6)),
      crypto.value.currentPrice
    );
  } catch (error) {
    alert(error instanceof Error ? error.message : 'Trade failed');
  }
  isLoading.value = false;
  amount.value = 0;
};
</script>