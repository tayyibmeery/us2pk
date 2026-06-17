<template>

    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mx-auto w-full max-w-[630px]">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">User Dashboard</h3>
          <button @click="logout"
            class="rounded-md bg-red-500 px-4 py-2 text-sm text-white hover:bg-red-600">Logout</button>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
              <p class="text-sm text-gray-500">Name</p>
              <p class="font-semibold">{{ user?.name }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
              <p class="text-sm text-gray-500">Email</p>
              <p class="font-semibold">{{ user?.email }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
              <p class="text-sm text-gray-500">Phone</p>
              <p class="font-semibold">{{ user?.phone }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
              <p class="text-sm text-gray-500">City</p>
              <p class="font-semibold">{{ user?.city?.city_name }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
              <p class="text-sm text-gray-500">PCode</p>
              <p class="font-semibold">{{ user?.pcode }}</p>
            </div>
            <div class="col-span-full rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
              <p class="text-sm text-gray-500">Address</p>
              <p class="font-semibold">{{ user?.address }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
 
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import AdminLayout from '@/components/layout/AdminLayout.vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const authStore = useAuthStore();
const user = computed(() => authStore.user);
const currentPageTitle = computed(() => 'Dashboard');

const logout = () => authStore.logout();
</script>
