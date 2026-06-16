<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mx-auto w-full max-w-[630px]">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">Admin Dashboard</h3>
          <button @click="logout"
            class="rounded-md bg-red-500 px-4 py-2 text-sm text-white hover:bg-red-600">Logout</button>
        </div>
        <p class="text-gray-600 dark:text-gray-300">Welcome, {{ user?.name }}. This is your admin panel.</p>
        <p class="mt-4 text-sm text-gray-500">You can manage users, shipments, and more from here.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import AdminLayout from '@/components/layout/AdminLayout.vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const authStore = useAuthStore();
const user = computed(() => authStore.user);
const currentPageTitle = computed(() => 'Admin');

const logout = () => authStore.logout();
</script>
