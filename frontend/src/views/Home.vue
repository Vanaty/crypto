<template>
  <div class="container py-4">
    <h1 class="display-4 mb-4">Cryptocurrency Market</h1>
    <div class="row g-4">
      <template v-if="cryptos.length > 0">
        <div class="col-12 col-md-6 col-lg-4" v-for="crypto in cryptos" :key="crypto.id">
          <router-link :to="'/crypto/' + crypto.id" class="text-decoration-none">
            <div class="card h-100 shadow-sm hover-shadow">
              <div class="card-body">
                <h2 class="card-title h5">{{ crypto.name }} ({{ crypto.symbol }})</h2>
                <p class="display-6 my-3">{{ formatCurrency(crypto.currentPrice) }}</p>
                <p :class="crypto.change24h >= 0 ? 'text-success' : 'text-danger'">
                  <i :class="crypto.change24h >= 0 ? 'bi bi-arrow-up' : 'bi bi-arrow-down'"></i>
                  {{ Math.abs(crypto.change24h).toFixed(2) }}%
                </p>
              </div>
            </div>
          </router-link>
        </div>
      </template>
      
      <!-- Loading Skeleton -->
      <template v-else>
        <div class="col-12 col-md-6 col-lg-4" v-for="n in 6" :key="n">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <div class="skeleton skeleton-title mb-3"></div>
              <div class="skeleton skeleton-price mb-3"></div>
              <div class="skeleton skeleton-change"></div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useCryptoStore } from '../stores/crypto';
import { useCurrencyStore } from '../stores/currency';

const currencyStore = useCurrencyStore();
const { cryptos } = storeToRefs(useCryptoStore());

const formatCurrency = (amount: number) => {
  return currencyStore.format(amount);
};
</script>

<style scoped>
.hover-shadow:hover {
  transform: translateY(-2px);
  transition: transform 0.2s;
}

/* Skeleton Loading Effect */
.skeleton {
  background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 4px;
}

.skeleton-title {
  width: 80%;
  height: 20px;
}

.skeleton-price {
  width: 60%;
  height: 32px;
}

.skeleton-change {
  width: 40%;
  height: 16px;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
