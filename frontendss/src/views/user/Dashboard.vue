<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Welcome, {{ user.name }}</h1>

        <!-- Tabs -->
        <div class="flex border-b mb-6">
            <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="[
                'py-2 px-4 font-medium',
                activeTab === tab.key
                    ? 'border-b-2 border-blue-600 text-blue-600'
                    : 'text-gray-600 hover:text-blue-600'
            ]">
                {{ tab.label }}
            </button>
        </div>

        <!-- Personal Information -->
        <div v-if="activeTab === 'personal'">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Name:</strong> {{ user.name }}</div>
                    <div><strong>Email:</strong> {{ user.email }}</div>
                    <div><strong>Phone:</strong> {{ user.phone }}</div>
                    <div><strong>City:</strong> {{ user.city?.city_name }}</div>
                    <div class="col-span-2"><strong>Address:</strong> {{ user.address }}</div>
                    <div><strong>PCode:</strong> {{ user.pcode }}</div>
                </div>
            </div>
        </div>

        <!-- My Shipments -->
        <div v-if="activeTab === 'shipments'">
            <div v-if="shipments.length === 0" class="text-gray-500">No shipments yet.</div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border">PSI</th>
                            <th class="py-2 px-4 border">Description</th>
                            <th class="py-2 px-4 border">Status</th>
                            <th class="py-2 px-4 border">Weight</th>
                            <th class="py-2 px-4 border">Total (PKR)</th>
                            <th class="py-2 px-4 border">Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="shipment in shipments" :key="shipment.id">
                            <td class="py-2 px-4 border">{{ shipment.psi }}</td>
                            <td class="py-2 px-4 border">{{ shipment.description }}</td>
                            <td class="py-2 px-4 border">{{ shipment.status }}</td>
                            <td class="py-2 px-4 border">{{ shipment.weight }} {{ shipment.weight_unit }}</td>
                            <td class="py-2 px-4 border">{{ shipment.total }}</td>
                            <td class="py-2 px-4 border">
                                <img v-if="shipment.image" :src="shipment.image"
                                    class="w-12 h-12 object-cover cursor-pointer"
                                    @click="openImageModal(shipment.image)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Prohibited Items -->
        <div v-if="activeTab === 'prohibited'">
            <div class="bg-white shadow rounded-lg p-6">
                <ul class="list-disc pl-5 space-y-2">
                    <li v-for="item in prohibitedItems" :key="item.id">
                        <strong>{{ item.item_name }}</strong> – {{ item.description }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Account Setting (Change Password) -->
        <div v-if="activeTab === 'settings'">
            <div class="bg-white shadow rounded-lg p-6 max-w-md">
                <form @submit.prevent="changePassword">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">New Password *</label>
                        <input v-model="passwordForm.password" type="password" required
                            class="w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Confirm Password *</label>
                        <input v-model="passwordForm.password_confirmation" type="password" required
                            class="w-full px-3 py-2 border rounded-md" />
                    </div>
                    <div v-if="passwordError" class="text-red-500 text-sm mb-2">{{ passwordError }}</div>
                    <div v-if="passwordSuccess" class="text-green-600 text-sm mb-2">{{ passwordSuccess }}</div>
                    <button type="submit" :disabled="passwordLoading"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ passwordLoading ? 'Updating...' : 'Update Password' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Image Modal -->
        <div v-if="modalImage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="modalImage = null">
            <div class="bg-white p-2 rounded-lg max-w-lg max-h-full" @click.stop>
                <img :src="modalImage" class="max-w-full max-h-96" />
            </div>
        </div>
    </div>



        <div class="p-8">
            <h1 class="text-2xl font-bold">Welcome {{ user?.name }}</h1>
            <button @click="logout" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Logout</button>
        </div>

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const user = ref(authStore.user);
const shipments = ref([]);
const prohibitedItems = ref([]);
const activeTab = ref('personal');

const tabs = [
    { key: 'personal', label: 'Personal Information' },
    { key: 'shipments', label: 'My Shipments' },
    { key: 'prohibited', label: 'Prohibited Items to Import' },
    { key: 'settings', label: 'Account Setting' },
];

const modalImage = ref(null);
const openImageModal = (url: string) => { modalImage.value = url; };

// Change password
const passwordForm = ref({ password: '', password_confirmation: '' });
const passwordLoading = ref(false);
const passwordError = ref('');
const passwordSuccess = ref('');

const changePassword = async () => {
    passwordLoading.value = true;
    passwordError.value = '';
    passwordSuccess.value = '';
    try {
        await api.post('/user/change-password', passwordForm.value);
        passwordSuccess.value = 'Password changed successfully.';
        passwordForm.value = { password: '', password_confirmation: '' };
    } catch (err: any) {
        passwordError.value = err.response?.data?.message || 'Failed to change password.';
    } finally {
        passwordLoading.value = false;
    }
};

const router = useRouter()

const logout = async () => {
    await authStore.logout()
    router.push('/login')
}
// Load data
onMounted(async () => {
    try {
        const [shipRes, prohibRes] = await Promise.all([
            api.get('/user/shipments'),
            api.get('/user/prohibited-items'),
        ]);
        shipments.value = shipRes.data;
        prohibitedItems.value = prohibRes.data;
    } catch (err) {
        console.error(err);
    }
});
</script>
