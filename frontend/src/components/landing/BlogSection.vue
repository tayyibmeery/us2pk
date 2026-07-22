<!-- src/components/landing/BlogSection.vue -->
<template>
  <div class="site-section py-5" id="blog-section">
    <div class="container">
      <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-4 mb-5 mb-lg-0">
          <div class="block-heading-1">
            <h2>{{ sectionData?.title || 'Articles' }}</h2>
            <p>{{ sectionData?.content || 'Latest news and updates from US2PK' }}</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div v-for="post in displayBlog" :key="post.id" class="col-lg-6">
          <div class="mb-5 d-block blog-entry" data-aos="fade-right" data-aos-delay="">
            <a :href="`/blog/${post.slug}`" class="blog-thumbnail mb-3 d-block">
              <img v-if="post.image && !post.image.includes('/depot/')" :src="getImageUrl(post.image)" :alt="post.title"
                class="img-fluid" style="max-height: 200px; width: 100%; object-fit: cover;"
                @error="handleImageError" />
              <div v-else class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                <span class="text-muted">No Image</span>
              </div>
            </a>
            <div class="blog-excerpt">
              <span
                class="d-block text-muted">{{ post.meta?.date || new Date(post.created_at).toLocaleDateString() }}</span>
              <h2 class="h4 mb-3"><a :href="`/blog/${post.slug}`">{{ post.title }}</a></h2>
              <p>{{ post.content }}</p>
              <p><a :href="`/blog/${post.slug}`" class="text-primary">Read More</a></p>
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
  const data = landingStore.getBlog;
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
};

const displayBlog = computed(() => {
  const blog = landingStore.getBlog;
  return blog && blog.length > 0 ? blog : [];
});

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>
