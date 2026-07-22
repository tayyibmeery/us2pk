<!-- src/components/landing/WhyUsSection.vue -->
<template>
  <div class="block__73694 site-section border-top" id="why-us-section">
    <div class="container">
      <div class="row d-flex no-gutters align-items-stretch">
        <div class="col-12 col-lg-6 block__73422 order-lg-2" data-aos="fade-left">
          <img v-if="data?.image" :src="getImageUrl(data.image)" :alt="data?.title || 'Why Us'"
            class="img-fluid w-100 h-100" style="object-fit: cover; min-height: 300px;" @error="handleImageError" />
          <div v-else class="d-flex align-items-center justify-content-center bg-light" style="min-height: 300px;">
            <span class="text-muted">No Image Available</span>
          </div>
        </div>
        <div class="col-lg-5 mr-auto p-lg-5 mt-4 mt-lg-0 order-lg-1" data-aos="fade-right">
          <h2 class="mb-4 text-black">{{ data?.title || 'Why Us' }}</h2>
          <p>{{ data?.content || 'Lorem ipsum dolor, sit amet consectetur adipisicing elit.' }}</p>
          <ul class="ul-check primary list-unstyled mt-5">
            <li v-for="item in features" :key="item">{{ item }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const landingStore = useLandingStore();

const data = computed(() => landingStore.getWhyUs);

const features = computed(() => {
  if (data.value?.meta?.features && Array.isArray(data.value.meta.features)) {
    return data.value.meta.features;
  }
  return [
    'Cargo express',
    'Secure Services',
    'Secure Warehouseing',
    'Cost savings',
    'Proven by great companies'
  ];
});

const getImageUrl = (imagePath) => {
  if (!imagePath) return '';

  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath;
  }

  const baseUrl = import.meta.env.VITE_BASE_URL || 'http://localhost:8000';

  if (imagePath.startsWith('/storage/')) {
    return `${baseUrl}${imagePath}`;
  }

  if (imagePath.startsWith('storage/')) {
    return `${baseUrl}/${imagePath}`;
  }

  if (imagePath.startsWith('pages/')) {
    return `${baseUrl}/storage/${imagePath}`;
  }

  if (imagePath.startsWith('/')) {
    return `${baseUrl}${imagePath}`;
  }

  return `${baseUrl}/storage/${imagePath}`;
};

const handleImageError = (event) => {
  const img = event.target;
  img.style.display = 'none';
};

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
