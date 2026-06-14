<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-4 text-xl font-bold border-b border-gray-700">Us2pk Admin</div>
            <nav class="flex-1 overflow-y-auto">
                <router-link v-for="item in menuItems" :key="item.path" :to="item.path"
                    class="block py-2 px-4 hover:bg-gray-700 transition" active-class="bg-gray-900">
                    {{ item.name }}
                </router-link>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <button @click="logout" class="w-full text-left text-red-400 hover:text-red-300">Logout</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <router-view />
        </main>
    </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();

const menuItems = [
    { name: 'Users', path: '/admin/users' },
    { name: 'Shipments', path: '/admin/shipments' },
    { name: 'New Shipment', path: '/admin/new-shipment' },
    { name: 'Consolidations', path: '/admin/consolidations' },
    { name: 'Addresses', path: '/admin/addresses' },
    { name: 'Cities', path: '/admin/cities' },
    { name: 'Settings', path: '/admin/settings' },
    { name: 'Statistics', path: '/admin/statistics' },
    { name: 'Invoices', path: '/admin/invoices' },
    { name: 'Revenues', path: '/admin/revenues' },
    { name: 'Debtors', path: '/admin/debtors' },
];

const logout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
