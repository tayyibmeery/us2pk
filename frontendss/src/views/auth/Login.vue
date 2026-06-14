<template>
    <div class="container mx-auto px-4 h-screen flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-lg rounded-lg p-8">
                <h2 class="text-2xl font-bold text-center mb-6">Sign In to Us2pk</h2>

                <form @submit.prevent="handleLogin">
                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email Address *
                        </label>
                        <input id="email" v-model="email" type="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="you@example.com" />
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password *
                        </label>
                        <input id="password" v-model="password" type="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="••••••••" />
                    </div>

                    <!-- Error Message -->
                    <div v-if="error" class="mb-4 text-red-500 text-sm text-center">
                        {{ error }}
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="loading"
                        class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition disabled:opacity-50">
                        {{ loading ? 'Signing in...' : 'Sign In' }}
                    </button>
                </form>

                <p class="text-center text-gray-600 text-sm mt-6">
                    Don't have an account?
                    <router-link to="/register" class="text-blue-600 hover:underline">Register</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

async function handleLogin() {
    loading.value = true;
    error.value = '';
    try {
        await authStore.login(email.value, password.value);
        // Redirect based on role
        if (authStore.isAdmin) {
            router.push('/admin/dashboard'); // or wherever admin goes
        } else {
            router.push('/dashboard');
        }
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Login failed. Please check your credentials.';
    } finally {
        loading.value = false;
    }
}
</script>
