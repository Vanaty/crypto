<template>
    <div class="container py-4">
      <div class="card shadow">
        <div class="card-body">
          <h1 class="card-title mb-4">Cryptocurrency Analysis</h1>
  
          <form @submit.prevent="performAnalysis">
            <!-- Analysis Type -->
            <div class="mb-4">
              <label class="form-label">Analysis Type</label>
              <select v-model="analysisType" class="form-select">
                <option value="quartile1">First Quartile</option>
                <option value="max">Maximum</option>
                <option value="min">Minimum</option>
                <option value="mean">Mean</option>
                <option value="stddev">Standard Deviation</option>
              </select>
            </div>
  
            <!-- Cryptocurrencies Selection -->
            <div class="mb-4">
              <label class="form-label">Select Cryptocurrencies</label>
              <div class="mb-2">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="selectAll"
                    v-model="selectAllCryptos"
                    @change="toggleAllCryptos"
                  />
                  <label class="form-check-label" for="selectAll">
                    Select All
                  </label>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-4" v-for="crypto in cryptos" :key="crypto.id">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :id="crypto.id"
                      v-model="selectedCryptos"
                      :value="crypto.id"
                    />
                    <label class="form-check-label" :for="crypto.id">
                      {{ crypto.name }}
                    </label>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Date Range -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label">Start Date and Time</label>
                <input
                  type="datetime-local"
                  class="form-control"
                  v-model="startDateTime"
                  required
                />
              </div>
              <div class="col-md-6">
                <label class="form-label">End Date and Time</label>
                <input
                  type="datetime-local"
                  class="form-control"
                  v-model="endDateTime"
                  required
                />
              </div>
            </div>
  
            <!-- Submit Button -->
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary" :disabled="!isFormValid || loading">
                {{ loading ? 'Analyzing...' : 'Analyze' }}
              </button>
            </div>
          </form>
  
          <!-- Results -->
          <div v-if="results" class="mt-4">
            <h3 class="mb-3">Analysis Results</h3>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Cryptocurrency</th>
                    <th>Result</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(result, cryptoId) in results" :key="cryptoId">
                    <td>{{ getCryptoName(cryptoId) }}</td>
                    <td>{{ formatResult(result) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue';
  import { storeToRefs } from 'pinia';
  import { useCryptoStore } from '../stores/crypto';
  
  const store = useCryptoStore();
  const { cryptos } = storeToRefs(store);
  
  const analysisType = ref('mean');
  const selectedCryptos = ref<string[]>([]);
  const selectAllCryptos = ref(false);
  const startDateTime = ref('');
  const endDateTime = ref('');
  const loading = ref(false);
  const results = ref<Record<string, number> | null>(null);
  
  const isFormValid = computed(() => 
    analysisType.value &&
    selectedCryptos.value.length > 0 &&
    startDateTime.value &&
    endDateTime.value
  );
  
  const toggleAllCryptos = () => {
    if (selectAllCryptos.value) {
      selectedCryptos.value = cryptos.value.map(c => c.id);
    } else {
      selectedCryptos.value = [];
    }
  };
  
  const getCryptoName = (cryptoId: string) => 
    cryptos.value.find(c => c.id === cryptoId)?.name || cryptoId;
  
  const formatResult = (value: number) => {
    if (analysisType.value === 'stddev' || analysisType.value === 'mean') {
      return value.toFixed(2);
    }
    return value.toString();
  };
  
  const calculateFirstQuartile = (data: number[]): number => {
    const sorted = [...data].sort((a, b) => a - b);
    const position = Math.floor(sorted.length * 0.25);
    return sorted[position];
  };
  
  const calculateMean = (data: number[]): number => {
    return data.reduce((sum, value) => sum + value, 0) / data.length;
  };
  
  const calculateStdDev = (data: number[]): number => {
    const mean = calculateMean(data);
    const squaredDiffs = data.map(value => Math.pow(value - mean, 2));
    const variance = calculateMean(squaredDiffs);
    return Math.sqrt(variance);
  };
  
  const performAnalysis = async () => {
    loading.value = true;
    const start = new Date(startDateTime.value).getTime();
    const end = new Date(endDateTime.value).getTime();
  
    try {
      const analysisResults: Record<string, number> = {};
  
      for (const cryptoId of selectedCryptos.value) {
        const crypto = cryptos.value.find(c => c.id === cryptoId);
        if (!crypto) continue;
  
        // const priceData = crypto.priceHistory.filter((_, index) => {
        //   const timestamp = Date.now() - (crypto.priceHistory.length - index) * 10000;
        //   return timestamp >= start && timestamp <= end;
        // });
        const priceData = crypto.priceHistory.filter(entry => {
          const timestamp = entry.date.getTime();
          return timestamp >= start && timestamp <= end;
        }).map(entry => entry.price);

  
        if (priceData.length === 0) continue;
  
        switch (analysisType.value) {
          case 'quartile1':
            analysisResults[cryptoId] = calculateFirstQuartile(priceData);
            break;
          case 'max':
            analysisResults[cryptoId] = Math.max(...priceData);
            break;
          case 'min':
            analysisResults[cryptoId] = Math.min(...priceData);
            break;
          case 'mean':
            analysisResults[cryptoId] = calculateMean(priceData);
            break;
          case 'stddev':
            analysisResults[cryptoId] = calculateStdDev(priceData);
            break;
        }
      }
  
      results.value = analysisResults;
    } catch (error) {
      console.error('Analysis failed:', error);
      alert('Failed to perform analysis. Please try again.');
    } finally {
      loading.value = false;
    }
  };
  </script>