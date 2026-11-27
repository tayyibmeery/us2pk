<!-- src/components/landing/BlogSection.vue -->
<template>
  <section id="blog-section" class="blog-section-wrapper">
    <div class="wrap">
      <!-- Section Header -->
      <div class="section-head center">
        <div class="eyebrow"><span class="rule"></span>From The Journal<span class="rule"></span></div>
        <h2>{{ sectionTitle || 'Stories, updates & shipping insights' }}</h2>
        <p>{{ sectionSubtitle || 'Latest news and updates from US2PK.' }}</p>
      </div>

      <!-- Blog Grid -->
      <div v-if="displayBlog.length" class="blog-grid">
        <div v-for="post in displayBlog" :key="post.id" class="blog-card" :class="{ 'is-open': isExpanded(post.id) }">
          <a :href="`/blog/${post.slug}`" class="blog-media">
            <img v-if="post.image && !post.image.includes('/depot/')" :src="getImageUrl(post.image)" :alt="post.title"
              loading="lazy" @error="handleImageError" />
            <div v-else class="blog-placeholder">No Image</div>
          </a>
          <div class="blog-body">
            <span class="blog-date">{{ formatDate(post) }}</span>
            <a :href="`/blog/${post.slug}`" class="blog-title-link">
              <h3>{{ post.title }}</h3>
            </a>
            <p class="blog-excerpt">{{ stripHtml(post.content) }}</p>

            <button type="button" class="blog-toggle" :aria-expanded="isExpanded(post.id)" @click="toggleBlog(post.id)">
              {{ isExpanded(post.id) ? 'Show less' : 'Learn more' }}
              <i class="fas fa-chevron-down"></i>
            </button>

            <div class="blog-detail">
              <div class="blog-detail-inner">
                <p>{{ stripHtml(post.content) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="blog-empty">No articles published yet.</div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const props = defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();

const displayBlog = computed(() => {
  const blog = landingStore.getBlog;
  return blog && blog.length > 0 ? blog : [];
});

// ============================================================
// EXPAND / COLLAPSE — same pattern as ServicesSection: "Learn
// more" opens an inline panel instead of the whole card being a link.
// ============================================================
const openIds = reactive(new Set());

const isExpanded = (id) => openIds.has(id);

const toggleBlog = (id) => {
  if (openIds.has(id)) {
    openIds.delete(id);
  } else {
    openIds.add(id);
  }
};

const getImageUrl = (imagePath) => {
  if (!imagePath) return '';

  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath;
  }

  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com';

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
  if (parent && !parent.querySelector('.blog-placeholder')) {
    const fallback = document.createElement('div');
    fallback.className = 'blog-placeholder';
    fallback.textContent = 'No Image';
    parent.appendChild(fallback);
  }
};

const stripHtml = (html) => {
  if (!html) return '';
  return html
    .replace(/<[^>]*>/g, ' ')
    .replace(/&nbsp;/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
};

const formatDate = (post) => {
  if (post?.meta?.date) return post.meta.date;
  const d = new Date(post?.created_at);
  if (isNaN(d.getTime())) return '';
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

onMounted(() => {
  landingStore.fetchLandingData();
});
</script>

<style scoped>
.blog-section-wrapper {
  padding: 110px 0;
  background: #F6F4EE;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

/* ============================================================
   SECTION HEAD
   ============================================================ */
.section-head {
  max-width: 620px;
  margin: 0 auto 64px;
  text-align: center;
}

.eyebrow {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 12.5px;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: #2C8C86;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
}

.eyebrow .rule {
  width: 28px;
  height: 1px;
  background: rgba(44, 140, 134, 0.45);
}

.section-head h2 {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 36px;
  line-height: 1.2;
  color: #12141C;
  margin: 0 0 14px;
}

.section-head p {
  font-family: 'Inter', sans-serif;
  font-size: 15.5px;
  line-height: 1.65;
  color: #4B4F5E;
  margin: 0;
}

/* ============================================================
   BLOG GRID
   ============================================================ */
.blog-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  align-items: start;
}

.blog-card {
  display: flex;
  flex-direction: column;
  background: #FFFFFF;
  border: 1px solid #DEDACB;
  border-radius: 6px;
  overflow: hidden;
  transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
}

.blog-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(15, 27, 61, 0.14);
  border-color: transparent;
}

/* once expanded, drop the hover lift so the open card doesn't jump around */
.blog-card.is-open:hover {
  transform: none;
}

.blog-media {
  position: relative;
  aspect-ratio: 16/10;
  background: #0F1B3D;
  overflow: hidden;
  display: block;
}

.blog-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .5s ease;
}

.blog-card:hover .blog-media img {
  transform: scale(1.06);
}

.blog-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, .3);
  font-size: 14px;
  font-family: 'IBM Plex Mono', monospace;
  background: #1E3170;
}

.blog-body {
  padding: 26px 26px 28px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.blog-date {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #B79A5C;
  margin-bottom: 12px;
}

.blog-title-link {
  text-decoration: none;
  color: inherit;
}

.blog-body h3 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 19px;
  font-weight: 600;
  line-height: 1.35;
  color: #12141C;
  margin: 0 0 10px;
  transition: color .15s ease;
}

.blog-title-link:hover h3 {
  color: #2C8C86;
}

.blog-excerpt {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  line-height: 1.65;
  color: #4B4F5E;
  margin: 0 0 18px;
  /* clamp to 3 lines until the card is expanded */
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}



/* ============================================================
   LEARN MORE TOGGLE — same pattern as ServicesSection
   ============================================================ */
.blog-toggle {
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #0F1B3D;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  cursor: pointer;
  background: none;
  border: none;
  padding: 0;
  margin-top: auto;
  align-self: flex-start;
}

.blog-toggle i {
  font-size: 10px;
  transition: transform .25s ease;
}

.blog-card.is-open .blog-toggle i {
  transform: rotate(180deg);
}

.blog-toggle:hover {
  color: #2C8C86;
}

/* ============================================================
   INLINE DETAIL PANEL
   Grid-rows trick animates height without measuring it in JS.
   ============================================================ */
.blog-detail {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows .35s ease;
}

.blog-card.is-open .blog-detail {
  grid-template-rows: 1fr;
}

.blog-detail-inner {
  overflow: hidden;
}

.blog-detail-inner p {
  font-family: 'Inter', sans-serif;
  font-size: 13.5px;
  line-height: 1.65;
  color: #4B4F5E;
  margin: 16px 0 0;
  padding-top: 16px;
  border-top: 1px dashed rgba(15, 27, 61, 0.16);
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.blog-empty {
  text-align: center;
  padding: 40px 0;
  font-family: 'Inter', sans-serif;
  font-size: 14.5px;
  color: #4B4F5E;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 980px) {
  .blog-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .blog-section-wrapper {
    padding: 72px 0;
  }

  .wrap {
    padding: 0 18px;
  }

  .blog-grid {
    grid-template-columns: 1fr;
    gap: 22px;
  }

  .section-head h2 {
    font-size: 28px;
  }

  .section-head {
    margin-bottom: 44px;
  }
}
</style>
