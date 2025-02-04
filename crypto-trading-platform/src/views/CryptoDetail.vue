<template>
  <div class="container py-4" v-if="crypto">
    <div class="card shadow">
      <div class="card-body">
        <h1 class="display-5 mb-4">{{ crypto.name }} ({{ crypto.symbol }})</h1>
        
        <div class="mb-4">
          <Line :data="chartData" :options="chartOptions" class="h-64" />
        </div>

        <div class="row g-4">
          <div class="col-12 col-md-6">
            <div class="p-3 border rounded bg-light">
              <p class="display-6">${{ crypto.currentPrice.toFixed(2) }}</p>
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
                <div class="mb-3">
                  <input type="number" v-model="amount" class="form-control" 
                         placeholder="Amount to trade" />
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
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useCryptoStore } from '../stores/crypto';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const route = useRoute();
const store = useCryptoStore();
const { cryptos } = storeToRefs(store);
const amount = ref(0);

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
      }) // Formater les dates
    ),
    datasets: [{
      label: 'Price',
      data: history.map(entry => entry.price), // Utiliser uniquement les prix
      borderColor: '#198754',
      tension: 0.1
    }]
  };
});


const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

const executeTrade = (type: 'buy' | 'sell') => {
  if (!crypto.value || amount.value <= 0) return;
  
  store.addTransaction({
    userId: 'user1',
    cryptoId: crypto.value.id,
    type,
    amount: amount.value,
    price: crypto.value.currentPrice,
    timestamp: new Date()
  });

  amount.value = 0;
};
</script>