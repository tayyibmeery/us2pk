<!-- src/components/landing/TestimonialsSection.vue -->
<template>
  <div id="testimonials-section" class="site-section bg-light block-13" data-aos="fade">
    <div class="container">
      <div class="text-center mb-5">
        <div class="block-heading-1">
          <h2>{{ sectionData?.title || 'Happy Clients' }}</h2>
        </div>
      </div>
      <div class="owl-carousel nonloop-block-13">
        <div v-for="testimonial in displayTestimonials" :key="testimonial.id">
          <div class="block-testimony-1 text-center">
            <blockquote class="mb-4">
              <p>&ldquo;{{ testimonial.content }}&rdquo;</p>
            </blockquote>
            <figure>
              <img v-if="testimonial.image && !testimonial.image.includes('/depot/')"
                :src="getImageUrl(testimonial.image)" :alt="testimonial.title" class="img-fluid rounded-circle mx-auto"
                style="width: 100px; height: 100px; object-fit: cover;" @error="handleImageError" />
              <div v-else class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                style="width: 100px; height: 100px;">
                <span class="text-white fs-1">{{ getInitials(testimonial.title) }}</span>
              </div>
            </figure>
            <h3 class="font-size-20 text-black">{{ testimonial.title }}</h3>
            <div v-if="testimonial.meta && testimonial.meta.rating" class="text-warning">
              <span v-for="n in 5" :key="n" class="fa fa-star"
                :class="{ 'text-warning': n <= testimonial.meta.rating }"></span>
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

const sectionData = computed(() => {
  const data = landingStore.getTestimonials;
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

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
};

const handleImageError = (event) => {
  const img = event.target;
  img.style.display = 'none';
};

const displayTestimonials = computed(() => {
  const testimonials = landingStore.getTestimonials;
  return testimonials && testimonials.length > 0 ? testimonials : [];
});

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
