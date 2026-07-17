<!-- src/components/landing/HeroSection.vue -->
<template>
  <div class="container-fluid p-0 pb-5">
    <div class="owl-carousel header-carousel position-relative mb-5">
      <div v-for="(hero, index) in displayHeroes" :key="hero.id || index" class="owl-carousel-item position-relative">
        <img class="img-fluid" :src="getImageUrl(hero.image || defaultImages[index])"
          :alt="hero.title || 'US2PK Shipping'">
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
                <router-link to="/signup" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">
                  {{ hero.meta?.button1_text || 'Get Started' }}
                </router-link>
                <a href="#contact" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">
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
import { computed } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const landingStore = useLandingStore();
const heroes = computed(() => landingStore.getHero);

// Default content for fallback
const defaultContent = '#1 Place For Your <span class="text-primary">US2PK</span> Shipments';

// Default images (using require for dynamic paths)
const defaultImages = [
  '/landing/img/carousel-1.jpg',
  '/landing/img/carousel-2.jpg'
];

// Function to get image URL with fallback
const getImageUrl = (imagePath) => {
  if (!imagePath) {
    return '/landing/img/carousel-1.jpg';
  }

  // If it's already a full URL or starts with http, return as is
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath;
  }

  // If it starts with /, it's a public path
  if (imagePath.startsWith('/')) {
    return imagePath;
  }

  // Otherwise, treat as relative to public folder
  return `/${imagePath}`;
};

// Default heroes if no data from API
const defaultHeroes = [
  {
    id: 1,
    title: 'Default Hero 1',
    content: '#1 Place For Your <span class="text-primary">US2PK</span> Shipments',
    image: '/landing/img/carousel-1.jpg',
    meta: {
      subtitle: 'Shipping & Logistics Solution',
      description: 'Trusted shopping, shipping, and delivery from USA to Pakistan.',
      button1_text: 'Get Started',
      button2_text: 'Free Quote'
    }
  },
  {
    id: 2,
    title: 'Default Hero 2',
    content: 'International <span class="text-primary">Shipping</span> Made Easy',
    image: '/landing/img/carousel-2.jpg',
    meta: {
      subtitle: 'Fast & Reliable',
      description: 'We handle customs, consolidation, and delivery – you just shop.',
      button1_text: 'Join Now',
      button2_text: 'Contact Us'
    }
  }
];

// Use default heroes if no data from API
const displayHeroes = computed(() => {
  return heroes.value.length > 0 ? heroes.value : defaultHeroes;
});
</script>
