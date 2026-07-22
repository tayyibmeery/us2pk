<!-- src/components/landing/HeroSection.vue -->
<template>
  <div id="home-section" class="container-fluid p-0 pb-5">
    <div class="owl-carousel header-carousel position-relative mb-5">
      <div v-for="(hero, index) in displayHeroes" :key="hero.id || index" class="owl-carousel-item position-relative">
        <div v-if="hero.image" class="position-relative" style="height: 100vh; min-height: 400px; background: #1a1a2e;">
          <img class="img-fluid w-100 h-100" :src="getImageUrl(hero.image)" :alt="hero.title || 'US2PK Shipping'"
            style="object-fit: cover;" @error="handleImageError" />
        </div>
        <div v-else class="position-relative"
          style="height: 100vh; min-height: 400px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
          <div class="d-flex align-items-center justify-content-center h-100">
            <span class="text-white fs-2">Upload Image Required</span>
          </div>
        </div>
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
          style="background: rgba(6, 3, 21, .5);">
          <div class="container">
            <div class="row justify-content-start">
              <div class="col-10 col-lg-8">
                <h5 class="text-white text-uppercase mb-3 animated slideInDown">
                  {{ hero.meta?.subtitle || 'Shipping & Logistics Solution' }}
                </h5>
                <h1 class="display-3 text-white animated slideInDown mb-4" v-html="hero.content || defaultContent">
                </h1>
                <p class="fs-5 fw-medium text-white mb-4 pb-2">
                  {{ hero.meta?.description || 'Trusted shopping, shipping, and delivery from USA to Pakistan.' }}
                </p>
                <router-link :to="hero.meta?.button1_link || '/signup'"
                  class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">
                  {{ hero.meta?.button1_text || 'Get Started' }}
                </router-link>
                <a :href="hero.meta?.button2_link || '#contact'"
                  class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">
                  {{ hero.meta?.button2_text || 'Free Quote' }}
                </a>
              </div>
            </div>
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

const defaultContent = '#1 Place For Your <span class="text-primary">US2PK</span> Shipments';

// Image helper function - same as in Pages.vue
const getImageUrl = (imagePath) => {
  if (!imagePath) return '';

  // If it's already a full URL, return as is
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath;
  }

  // Get the base URL from environment variable
  const baseUrl = import.meta.env.VITE_BASE_URL || 'http://localhost:8000';

  // If it starts with /storage/, it's already a storage path
  if (imagePath.startsWith('/storage/')) {
    return `${baseUrl}${imagePath}`;
  }

  // If it starts with storage/ (without leading slash), add the slash and base URL
  if (imagePath.startsWith('storage/')) {
    return `${baseUrl}/${imagePath}`;
  }

  // If it starts with pages/, it's a storage path
  if (imagePath.startsWith('pages/')) {
    return `${baseUrl}/storage/${imagePath}`;
  }

  // If it starts with /, it's a public path
  if (imagePath.startsWith('/')) {
    return `${baseUrl}${imagePath}`;
  }

  // If it doesn't have any prefix, assume it's from storage
  return `${baseUrl}/storage/${imagePath}`;
};

const handleImageError = (event) => {
  const img = event.target;
  img.style.display = 'none';
  const parent = img.parentElement;
  if (parent) {
    const placeholder = document.createElement('div');
    placeholder.className = 'd-flex align-items-center justify-content-center w-100 h-100';
    placeholder.style.minHeight = '400px';
    placeholder.style.background = '#1a1a2e';
    placeholder.innerHTML = '<span class="text-white fs-2">No Image Available</span>';
    parent.appendChild(placeholder);
  }
};

const displayHeroes = computed(() => {
  const heroes = landingStore.getHero;
  return heroes && heroes.length > 0 ? heroes : [];
});

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
