<!-- src/components/landing/AboutSection.vue -->
<template>
  <div id="about-section" class="container-fluid overflow-hidden py-5 px-lg-0">
    <div class="container about py-5 px-lg-0">
      <div class="row g-5 mx-lg-0">
        <div class="col-lg-6 ps-lg-0 wow fadeInLeft" style="min-height: 400px;">
          <div class="position-relative h-100">
            <img v-if="sectionData?.image" :src="getImageUrl(sectionData.image)"
              class="position-absolute img-fluid w-100 h-100" style="object-fit: cover;"
              :alt="sectionData?.title || 'About US2PK'" @error="handleImageError" />
            <div v-else
              class="position-absolute img-fluid w-100 h-100 bg-light d-flex align-items-center justify-content-center"
              style="object-fit: cover;">
              <span class="text-muted fs-4">No Image Available</span>
            </div>
          </div>
        </div>
        <div class="col-lg-6 about-text wow fadeInUp">
          <h6 class="text-secondary text-uppercase mb-3">{{ sectionData?.meta?.subtitle || 'About Us' }}</h6>
          <h1 class="mb-5">{{ sectionData?.title || 'Quick Transport and Logistics Solutions' }}</h1>
          <p class="mb-5">
            {{ sectionData?.content || 'US2PK connects Pakistan to the world\'s best products through trusted shopping, shipping, and delivery solutions. We handle everything from purchase to doorstep.' }}
          </p>
          <div class="row g-4 mb-5">
            <div class="col-sm-6 wow fadeIn" v-for="(feature, index) in features" :key="index">
              <i :class="feature.icon || 'fa fa-globe fa-3x text-primary mb-3'"></i>
              <h5>{{ feature.title }}</h5>
              <p class="m-0">{{ feature.description }}</p>
            </div>
          </div>
          <a href="#contact" class="btn btn-primary py-3 px-5">Get In Touch</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const landingStore = useLandingStore();

const sectionData = computed(() => landingStore.getAbout);

const features = computed(() => {
  if (sectionData.value?.meta?.features) {
    return sectionData.value.meta.features;
  }
  return [
    {
      icon: 'fa fa-globe fa-3x text-primary mb-3',
      title: 'Global Coverage',
      description: 'Ship from anywhere in the USA to any city in Pakistan.'
    },
    {
      icon: 'fa fa-shipping-fast fa-3x text-primary mb-3',
      title: 'On Time Delivery',
      description: 'We guarantee your packages arrive safely and on schedule.'
    }
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
  const parent = img.parentElement;
  if (parent) {
    const placeholder = document.createElement('div');
    placeholder.className = 'position-absolute img-fluid w-100 h-100 bg-light d-flex align-items-center justify-content-center';
    placeholder.innerHTML = '<span class="text-muted fs-4">No Image</span>';
    parent.appendChild(placeholder);
  }
};

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
