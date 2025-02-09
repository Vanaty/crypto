import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Home from '../views/Home.vue';
import CryptoDetail from '../views/CryptoDetail.vue';
import Transactions from '../views/Transactions.vue';
import Login from '../views/Login.vue';
import Analysis from '../views/Analysis.vue';
import Register from '../views/Register.vue';
import Wallet from '../views/Wallet.vue';

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { 
      path: '/',
      component: Home,
      meta: { requiresAuth: true }
    },
    { 
      path: '/crypto/:id',
      component: CryptoDetail,
      meta: { requiresAuth: true }
    },
    { 
      path: '/transactions',
      component: Transactions,
      meta: { requiresAuth: true }
    },
    {
      path: '/wallet',
      component: Wallet,
      meta: { requiresAuth: true }
    },
    {
      path: '/analysis',
      component: Analysis,
      meta: { requiresAuth: true }
    },
    { 
      path: '/login',
      component: Login,
      meta: { requiresAuth: false }
    },
    { 
      path: '/register',
      component: Register,
      meta: { requiresAuth: false }
    }
  ]
});

router.beforeEach((to, _, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else if (!to.meta.requiresAuth && authStore.isAuthenticated) {
    next('/');
  } else {
    next();
  }
});