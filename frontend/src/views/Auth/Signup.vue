<template>
  <FullScreenLayout>
    <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">
      <div class="relative flex flex-col justify-center w-full h-screen lg:flex-row dark:bg-gray-900">
        <div class="flex flex-col flex-1 w-full lg:w-1/2">
          <div class="w-full max-w-md pt-10 mx-auto">
            <router-link to="/"
              class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
              <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                fill="none">
                <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke="" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
              Back to dashboard
            </router-link>
          </div>
          <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
            <div class="mb-5 sm:mb-8">
              <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">Sign Up
              </h1>
              <p class="text-sm text-gray-500 dark:text-gray-400">Enter your email and password to sign up!</p>
            </div>
            <form @submit.prevent="handleSubmit">
              <div class="space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full
                      Name<span class="text-error-500">*</span></label>
                    <input v-model="form.name" type="text" id="name" placeholder="Muhammad Ali"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                  </div>
                  <div>
                    <label for="email"
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email<span
                        class="text-error-500">*</span></label>
                    <input v-model="form.email" type="email" id="email" placeholder="you@example.com"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                  </div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div>
                    <label for="password"
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password<span
                        class="text-error-500">*</span></label>
                    <input v-model="form.password" type="password" id="password" placeholder="Min 8 characters"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                  </div>
                  <div>
                    <label for="password_confirmation"
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm Password<span
                        class="text-error-500">*</span></label>
                    <input v-model="form.password_confirmation" type="password" id="password_confirmation"
                      placeholder="Confirm password"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                  </div>
                </div>
                <div>
                  <label for="phone"
                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone<span
                      class="text-error-500">*</span></label>
                  <input v-model="form.phone" type="tel" id="phone" placeholder="+92-300-1234567"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                  <div>
                    <label for="city_id"
                      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">City<span
                        class="text-error-500">*</span></label>
                    <select v-model="form.city_id" id="city_id"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                      <option value="">Select City</option>
                      <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.city_name }}</option>
                    </select>
                  </div>
                  <div>
                    <label for="source" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">How
                      did you hear?<span class="text-error-500">*</span></label>
                    <select v-model="form.source" id="source"
                      class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                      <option value="">Select Source</option>
                      <option v-for="src in sources" :key="src" :value="src">{{ src }}</option>
                    </select>
                  </div>
                </div>
                <div>
                  <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full
                    Address<span class="text-error-500">*</span></label>
                  <textarea v-model="form.address" id="address" rows="2" placeholder="House #, Street, Area, City"
                    class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                </div>
                <div>
                  <label
                    class="flex items-start text-sm font-normal text-gray-700 cursor-pointer select-none dark:text-gray-400">
                    <div class="relative">
                      <input type="checkbox" v-model="agreeToTerms" class="sr-only" />
                      <div
                        :class="agreeToTerms ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                        class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                        <span :class="agreeToTerms ? '' : 'opacity-0'">
                          <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437"
                              stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                        </span>
                      </div>
                    </div>
                    <p class="inline-block font-normal text-gray-500 dark:text-gray-400">By creating an account you
                      agree to the <span class="text-gray-800 dark:text-white/90">Terms and Conditions</span> and our
                      <span class="text-gray-800 dark:text-white">Privacy Policy</span>
                    </p>
                  </label>
                </div>
                <div v-if="error" class="text-sm text-red-500">{{ error }}</div>
                <div v-if="success" class="text-sm text-green-600">{{ success }}</div>
                <div>
                  <button type="submit" :disabled="loading"
                    class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 disabled:opacity-50">
                    {{ loading ? 'Registering...' : 'Sign Up' }}
                  </button>
                </div>
              </div>
            </form>
            <div class="mt-5">
              <p class="text-sm font-normal text-center text-gray-700 dark:text-gray-400 sm:text-start">
                Already have an account?
                <router-link to="/signin" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">Sign
                  In</router-link>
              </p>
            </div>
          </div>
        </div>
        <div class="relative items-center hidden w-full h-full lg:w-1/2 bg-brand-950 dark:bg-white/5 lg:grid">
          <div class="flex items-center justify-center z-1">
            <common-grid-shape />
            <div class="flex flex-col items-center max-w-xs">
              <router-link to="/" class="block mb-4">
                <img width="231" height="48" src="/images/logo/auth-logo.svg" alt="Logo" />
              </router-link>
              <p class="text-center text-gray-400 dark:text-white/60">Free and Open-Source Tailwind CSS Admin Dashboard
                Template</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FullScreenLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useCityStore } from '@/stores/cityStore';
import FullScreenLayout from '@/components/layout/FullScreenLayout.vue';
import CommonGridShape from '@/components/common/CommonGridShape.vue';

const router = useRouter();
const authStore = useAuthStore();
const cityStore = useCityStore();
const { items: cities } = storeToRefs(cityStore);

const loading = ref(false);
const error = ref('');
const success = ref('');
const agreeToTerms = ref(false);
const sources = ['Facebook', 'Google Search', 'WhatsApp', 'Friend Referral', 'Newspapers', 'Twitter/X', 'Other'];

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  city_id: '',
  address: '',
  source: '',
});

// Load cities when component mounts
// onMounted(async () => {
//   if (!cityStore.items.length) {
//     try {
//       // ✅ Use public endpoint – no authentication required
//       await cityStore.fetchPublicCities();
//     } catch (err) {
//       console.error('Failed to load cities:', err);
//     }
//   }
// });

onMounted(async () => {
  if (!cityStore.items.length) {
    try {
      await cityStore.fetchPublicCities()
    } catch (err) {
      console.error('Failed to load cities:', err)
      // Optionally show a user-friendly error
    }
  }
});

async function handleSubmit() {
  if (!agreeToTerms.value) {
    error.value = 'You must agree to the Terms and Conditions.';
    return;
  }
  loading.value = true;
  error.value = '';
  success.value = '';
  try {
    await authStore.register(form.value);
    success.value = 'Registration successful! Please check your email to verify your account.';
    setTimeout(() => router.push('/signin'), 3000);
  } catch (err: any) {
    const errs = err.response?.data?.errors;
    error.value = errs ? Object.values(errs).flat().join(' ') : err.response?.data?.message || 'Registration failed.';
  } finally {
    loading.value = false;
  }
}
</script>
