<template>
  <ProfitLossTemplate :pageTitle="'P&L - Since Inception'" :periodText="'Since Inception to Present'"
    :periodRange="'Inception → Present'" :data="data" :loading="loading" :error="error" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import ProfitLossTemplate from './ProfitLossTemplate.vue';

const data = ref<any>({});
const loading = ref(false);
const error = ref('');

async function fetchData() {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get('/admin/pandl/since-inception');
    data.value = res.data;
  } catch (e: any) {
    error.value = e.message || 'Failed to fetch P&L data';
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
