<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Login</h2>

            <!-- Login Form -->
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
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="credentials.password"
                  required
                />
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  {{ loading ? 'Loading...' : 'Login' }}
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

            <!-- 2FA Verification
            <form @submit.prevent="handleTfaSubmit" v-if="isTfaPending">
              <div class="mb-3">
                <label for="tfaCode" class="form-label">Enter 2FA Code</label>
                <input
                  type="text"
                  class="form-control"
                  id="tfaCode"
                  v-model="tfaCode"
                  required
                />
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  {{ loading ? 'Verifying...' : 'Verify 2FA' }}
                </button>
              </div>
            </form> -->

            <div class="text-center mt-3">
              <router-link to="/register">Don't have an account? Register</router-link>
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
import type { LoginCredentials } from '../types/auth';

const router = useRouter();
const authStore = useAuthStore();
const isOtpPending = computed(() => authStore.isOtpPending);

const credentials = ref<LoginCredentials>({
  email: '',
  password: ''
});
const otp = ref('');
// const tfaCode = ref('');
const loading = ref(false);

const handleSubmit = async () => {
  try {
    loading.value = true;
    const rep = await authStore.login(credentials.value);
    if (!authStore.isOtpPending && !authStore.isTfaPending) {
      router.push('/');
    }
    if(!rep.success) {
      alert(rep.message);
    }
  } catch (error) {
    alert('Login failed. Please check your credentials.');
  } finally {
    loading.value = false;
  }
};

const handleOtpSubmit = async () => {
  try {
    loading.value = true;
    const rep = await authStore.verifyLoginOtp(otp.value);
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

// const handleTfaSubmit = async () => {
//   try {
//     loading.value = true;
//     await authStore.verifyTfa(tfaCode.value);
//     router.push('/');
//   } catch (error) {
//     alert('Invalid 2FA code. Please try again.');
//   } finally {
//     loading.value = false;
//   }
// };
</script>