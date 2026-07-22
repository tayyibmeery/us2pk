<!-- src/components/landing/ServicesSection.vue -->
<template>
  <div id="services-section" class="container-xxl py-5">
    <div class="container py-5">
      <div class="text-center wow fadeInUp">
        <h6 class="text-secondary text-uppercase">{{ sectionData?.meta?.subtitle || 'Our Services' }}</h6>
        <h1 class="mb-5">{{ sectionData?.title || 'Explore Our Services' }}</h1>
      </div>
      <div class="row g-4">
        <div v-for="(service, index) in displayServices" :key="service.id || index"
          class="col-md-6 col-lg-4 wow fadeInUp">
          <div class="service-item p-4">
            <div class="overflow-hidden mb-4" style="height: 200px; background: #f8f9fa;">
              <img v-if="service.image" class="img-fluid w-100 h-100" :src="getImageUrl(service.image)"
                :alt="service.title" style="object-fit: cover;" @error="handleImageError" />
              <div v-else class="d-flex align-items-center justify-content-center h-100">
                <span class="text-muted">No Image</span>
              </div>
            </div>
            <h4 class="mb-3">{{ service.title }}</h4>
            <p>{{ service.content || service.meta?.description }}</p>
            <a class="btn-slide mt-2" :href="service.meta?.link || '#'">
              <i class="fa fa-arrow-right"></i><span>Read More</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const landingStore = useLandingStore();

const sectionData = computed(() => {
  const data = landingStore.getServices;
  return data.length > 0 ? data[0] : null;
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
  const parent = img.parentElement;
  if (parent) {
    const placeholder = document.createElement('div');
    placeholder.className = 'd-flex align-items-center justify-content-center h-100 bg-light';
    placeholder.style.height = '200px';
    placeholder.innerHTML = '<span class="text-muted">No Image</span>';
    parent.appendChild(placeholder);
  }
};

const displayServices = computed(() => {
  const services = landingStore.getServices;
  return services && services.length > 0 ? services : [];
});

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
