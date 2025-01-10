<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
      <div class="container">
        <router-link class="navbar-brand" to="/">Crypto Trading</router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <template v-if="isAuthenticated">
              <li class="nav-item">
                <router-link class="nav-link" to="/">Home</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" to="/transactions">Transactions</router-link>
              </li>
            </template>
          </ul>
          <div class="d-flex">
            <template v-if="isAuthenticated">
              <button @click="handleLogout" class="btn btn-outline-danger">
                Logout
              </button>
            </template>
            <template v-else>
              <router-link to="/login" class="btn btn-outline-primary me-2">
                Login
              </router-link>
              <router-link to="/register" class="btn btn-primary">
                Register
              </router-link>
            </template>
          </div>
        </div>
      </div>
    </nav>
    
    <router-view></router-view>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from './stores/auth';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';

const authStore = useAuthStore();
const router = useRouter();
const { isAuthenticated } = storeToRefs(authStore);

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};

// Initialize authentication state
authStore.initAuth();
</script>