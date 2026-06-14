<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Users Management</h1>
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">Name</th>
                        <th class="py-2 px-4 border">Email</th>
                        <th class="py-2 px-4 border">PCode</th>
                        <th class="py-2 px-4 border">City</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id">
                        <td class="py-2 px-4 border">{{ user.id }}</td>
                        <td class="py-2 px-4 border">{{ user.name }}</td>
                        <td class="py-2 px-4 border">{{ user.email }}</td>
                        <td class="py-2 px-4 border">{{ user.pcode }}</td>
                        <td class="py-2 px-4 border">{{ user.city?.city_name }}</td>
                        <td class="py-2 px-4 border">
                            <select v-model="user.status" @change="updateStatus(user)" class="border rounded px-2 py-1">
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="approved">Approved</option>
                            </select>
                        </td>
                        <td class="py-2 px-4 border">
                            <button @click="viewDetails(user)" class="text-blue-600 hover:underline">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const users = ref([]);

const fetchUsers = async () => {
    const res = await api.get('/admin/users');
    users.value = res.data;
};

const updateStatus = async (user: any) => {
    await api.post(`/admin/users/${user.id}/status`, { status: user.status });
    // optionally show toast message
};

const viewDetails = (user: any) => {
    alert(`User details: ${user.name}\nAddress: ${user.address}\nPhone: ${user.phone}`);
};

onMounted(fetchUsers);
</script>
