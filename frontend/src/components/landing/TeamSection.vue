<!-- src/components/landing/TeamSection.vue -->
<template>
  <div class="site-section" id="team-section">
    <div class="container">
      <div class="row mb-5 justify-content-center">
        <div class="col-md-7 text-center">
          <div class="block-heading-1">
            <h2>{{ sectionData?.title || 'Our Staff' }}</h2>
            <p>{{ sectionData?.content || 'Far far away, behind the word mountains...' }}</p>
          </div>
        </div>
      </div>
      <div class="owl-carousel owl-all mb-5">
        <div v-for="member in displayTeam" :key="member.id" class="block-team-member-1 text-center rounded h-100">
          <figure>
            <img v-if="member.image && !member.image.includes('/depot/')" :src="getImageUrl(member.image)"
              :alt="member.title" class="img-fluid rounded-circle"
              style="width: 150px; height: 150px; object-fit: cover;" @error="handleImageError" />
            <div v-else class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
              style="width: 150px; height: 150px;">
              <span class="text-white fs-1">{{ getInitials(member.title) }}</span>
            </div>
          </figure>
          <h3 class="font-size-20 text-black">{{ member.title }}</h3>
          <span
            class="d-block font-gray-5 letter-spacing-1 text-uppercase font-size-12 mb-3">{{ member.meta?.position || 'Member' }}</span>
          <p class="mb-4">{{ member.content }}</p>
          <div class="block-social-1">
            <a :href="member.meta?.facebook || '#'" class="btn border-w-2 rounded primary-primary-outline--hover">
              <span class="icon-facebook"></span>
            </a>
            <a :href="member.meta?.twitter || '#'" class="btn border-w-2 rounded primary-primary-outline--hover">
              <span class="icon-twitter"></span>
            </a>
            <a :href="member.meta?.instagram || '#'" class="btn border-w-2 rounded primary-primary-outline--hover">
              <span class="icon-instagram"></span>
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
  const data = landingStore.getTeam;
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

const displayTeam = computed(() => {
  const team = landingStore.getTeam;
  return team && team.length > 0 ? team : [];
});

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
