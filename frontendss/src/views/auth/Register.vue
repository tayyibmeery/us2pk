<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-2xl font-bold text-center mb-6">Create an Account</h2>

            <form @submit.prevent="handleRegister">
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Full Name *</label>
                    <input v-model="form.name" type="text" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address *</label>
                    <input v-model="form.email" type="email" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number *</label>
                    <input v-model="form.phone" type="tel" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="+92-XXXXXXXXXX" />
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Address *</label>
                    <textarea v-model="form.address" required rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>

                <!-- City (dropdown) -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">City *</label>
                    <select v-model="form.city_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="" disabled>Select your city</option>
                        <option v-for="city in cities" :key="city.id" :value="city.id">
                            {{ city.city_name }}
                        </option>
                    </select>
                </div>

                <!-- Source (optional, hidden or text) -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">How did you find us? (Optional)</label>
                    <input v-model="form.source" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        placeholder="Facebook, Google, Friend, etc." />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password *</label>
                    <input v-model="form.password" type="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password *</label>
                    <input v-model="form.password_confirmation" type="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                </div>

                <!-- Error/Success Messages -->
                <div v-if="error" class="mb-4 text-red-500 text-sm">
                    {{ error }}
                </div>
                <div v-if="success" class="mb-4 text-green-600 text-sm">
                    {{ success }}
                </div>

                <button type="submit" :disabled="loading"
                    class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 transition disabled:opacity-50">
                    {{ loading ? 'Registering...' : 'Register' }}
                </button>
            </form>

            <p class="text-center text-gray-600 text-sm mt-6">
                Already have an account?
                <router-link to="/login" class="text-blue-600 hover:underline">Sign In</router-link>
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import api from '@/services/api';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    city_id: '',
    source: '',
    password: '',
    password_confirmation: '',
});

const cities = ref<any[]>([]);
const loading = ref(false);
const error = ref('');
const success = ref('');

// Fetch cities for dropdown
async function loadCities() {
    try {
        const res = await api.get('/cities');
        cities.value = res.data;
    } catch (err) {
        console.error('Failed to load cities', err);
    }
}

async function handleRegister() {
    loading.value = true;
    error.value = '';
    success.value = '';

    try {
        await authStore.register(form.value);
        success.value =
            'Registration successful! Please check your email to verify your account.';
        // Optionally reset form
        form.value = {
            name: '',
            email: '',
            phone: '',
            address: '',
            city_id: '',
            source: '',
            password: '',
            password_confirmation: '',
        };
        // Redirect to login after 3 seconds
        setTimeout(() => {
            router.push('/login');
        }, 3000);
    } catch (err: any) {
        const errors = err.response?.data?.errors;
        if (errors) {
            error.value = Object.values(errors).flat().join(' ');
        } else {
            error.value = err.response?.data?.message || 'Registration failed.';
        }
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadCities();
});
</script>
