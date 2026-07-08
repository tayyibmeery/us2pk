<template>
  <PageBreadcrumb :pageTitle="'Voucher Details'" />
  <div v-if="loading" class="text-center py-10">Loading...</div>
  <div v-else-if="!voucher" class="text-center py-10 text-gray-400">Voucher not found.</div>
  <div v-else class="bg-white dark:bg-gray-800 rounded-xl border p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">{{ voucher.voucher_no }}</h2>
      <span :class="voucher.approved ? 'text-green-600' : 'text-yellow-600'">
        {{ voucher.approved ? 'Approved' : 'Not Approved' }}
      </span>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
      <div><strong>Date:</strong> {{ formatDate(voucher.date) }}</div>
      <div><strong>Source:</strong> {{ voucher.source }}</div>
      <div class="col-span-2"><strong>Description:</strong> {{ voucher.description }}</div>
    </div>
    <table class="w-full text-sm border">
      <thead class="bg-gray-100 dark:bg-gray-700">
        <tr>
          <th class="px-4 py-2 text-left">Account</th>
          <th class="px-4 py-2 text-right">Debit</th>
          <th class="px-4 py-2 text-right">Credit</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="detail in voucher.details" :key="detail.id" class="border-t">
          <td class="px-4 py-2">{{ detail.account?.name || 'N/A' }}</td>
          <td class="px-4 py-2 text-right">{{ Number(detail.debit).toFixed(2) }}</td>
          <td class="px-4 py-2 text-right">{{ Number(detail.credit).toFixed(2) }}</td>
        </tr>
      </tbody>
      <tfoot class="font-bold bg-gray-50 dark:bg-gray-800">
        <tr>
          <td class="px-4 py-2">Total</td>
          <td class="px-4 py-2 text-right">{{ Number(totalDebit).toFixed(2) }}</td>
          <td class="px-4 py-2 text-right">{{ Number(totalCredit).toFixed(2) }}</td>
        </tr>
      </tfoot>
    </table>
    <div class="mt-4 flex gap-2">
      <button @click="goBack" class="px-4 py-2 border rounded hover:bg-gray-50">Back</button>
      <button v-if="!voucher.approved" @click="approve"
        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Approve</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const voucher = ref<any>(null);

const totalDebit = computed(() => {
  if (!voucher.value?.details) return 0;
  return voucher.value.details.reduce((sum: number, d: any) => sum + (Number(d.debit) || 0), 0);
});

const totalCredit = computed(() => {
  if (!voucher.value?.details) return 0;
  return voucher.value.details.reduce((sum: number, d: any) => sum + (Number(d.credit) || 0), 0);
});

const formatDate = (date: string) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-GB');
};

async function fetchVoucher() {
  loading.value = true;
  try {
    const res = await api.get(`/admin/vouchers/${route.params.id}`);
    voucher.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function approve() {
  if (!confirm('Approve this voucher?')) return;
  try {
    await api.post(`/admin/vouchers/${voucher.value.id}/approve`);
    await fetchVoucher();
  } catch (e) {
    alert('Approval failed');
  }
}

function goBack() {
  router.push('/admin/vouchers');
}

onMounted(fetchVoucher);
</script>
