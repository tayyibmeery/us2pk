<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold">Welcome {{ user?.name }}</h1>
        <button @click="logout" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Logout</button>
    </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const user = authStore.user

const logout = async () => {
    await authStore.logout()
    router.push('/login')
}
</script>
