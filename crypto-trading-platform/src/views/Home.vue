<template>
  <div class="container py-4">
    <h1 class="display-4 mb-4">Cryptocurrency Market</h1>
    <div class="row g-4">
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
    </div>
  </div>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useCryptoStore } from '../stores/crypto';
import { useCurrencyStore } from '../stores/currency';

const currencyStore  = useCurrencyStore();
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
</style>