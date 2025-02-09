<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Register</h2>

            <!-- Registration Form -->
            <form @submit.prevent="handleSubmit" v-if="!isOtpPending">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="credentials.email"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="prenom" class="form-label">Nom*</label>
                <input
                  type="text"
                  class="form-control"
                  id="prenom"
                  v-model="credentials.nom"
                />
              </div>
              <div class="mb-3">
                <label for="prenom" class="form-label">Prenom</label>
                <input
                  type="text"
                  class="form-control"
                  id="prenom"
                  v-model="credentials.prenom"
                />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="credentials.password"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="confirmPassword"
                  v-model="credentials.confirmPassword"
                  required
                />
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  {{ loading ? 'Loading...' : 'Register' }}
                </button>
              </div>
            </form>

            <!-- OTP Verification -->
            <form @submit.prevent="handleOtpSubmit" v-if="isOtpPending">
              <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP sent to your email</label>
                <input
                  type="text"
                  class="form-control"
                  id="otp"
                  v-model="otp"
                  required
                />
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  {{ loading ? 'Verifying...' : 'Verify OTP' }}
                </button>
              </div>
            </form>

            <div class="text-center mt-3">
              <router-link to="/login">Already have an account? Login</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import type { RegisterCredentials } from '../types/auth';

const router = useRouter();
const authStore = useAuthStore();
const isOtpPending = computed(() => authStore.isOtpPending);

const credentials = ref<RegisterCredentials>({
  email: '',
  password: '',
  nom: '',
  prenom: '',
  confirmPassword: ''
});
const otp = ref('');
const loading = ref(false);

const handleSubmit = async () => {
  if (credentials.value.password !== credentials.value.confirmPassword) {
    alert('Passwords do not match');
    return;
  }

  try {
    loading.value = true;
    const rep  = await authStore.register(credentials.value); 
    alert(rep.message);
  } catch (error) {
    alert('Registration failed. Please try again.');
  } finally {
    loading.value = false;
  }
};

const handleOtpSubmit = async () => {
  try {
    loading.value = true;
    const rep = await authStore.verifyOtp(otp.value);
    if (rep.success) {
      router.push('/'); 
    } else {
      alert(rep.message);
    }

  } catch (error) {
    alert('Invalid OTP. Please try again.');
  } finally {
    loading.value = false;
  }
};
</script>