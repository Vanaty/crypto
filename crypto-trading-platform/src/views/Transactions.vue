<template>
  <div class="container py-4">
    <h1 class="display-4 mb-4">Transaction History</h1>
    
    <div class="card shadow">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Type</th>
              <th>Crypto</th>
              <th>Amount</th>
              <th>Price</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tx in sortedTransactions" :key="tx.id">
              <td>
                <span :class="tx.type === 'buy' ? 'badge bg-success' : 'badge bg-danger'">
                  {{ tx.type.toUpperCase() }}
                </span>
              </td>
              <td>{{ getCryptoName(tx.cryptoId) }}</td>
              <td>{{ tx.amount }}</td>
              <td>${{ tx.price.toFixed(2) }}</td>
              <td>{{ new Date(tx.timestamp).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useCryptoStore } from '../stores/crypto';

const store = useCryptoStore();
const { transactions, cryptos } = storeToRefs(store);

const sortedTransactions = computed(() => 
  [...transactions.value].sort((a, b) => 
    new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
  )
);

const getCryptoName = (cryptoId: string) => 
  cryptos.value.find(c => c.id === cryptoId)?.name || cryptoId;
</script>