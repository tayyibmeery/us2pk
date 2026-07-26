<template>
  <section id="why-us-section" class="whyus-section-wrapper">
    <div class="wrap">
      <!-- Section Header -->
      <div class="section-head center">
        <div class="eyebrow on-dark">Why US2PK</div>
        <h2>{{ sectionTitle || 'Built for the way Pakistanis actually shop online.' }}</h2>
        <p>
          {{ sectionSubtitle || 'No hidden fees, no vanishing packages, no guesswork on customs. Just a clear, tracked path from checkout to your doorstep.' }}
        </p>
      </div>

      <!-- Why Us Items with Alternating Layout -->
      <div v-for="(item, index) in whyUsItems" :key="item.id || index" class="whyus-grid-wrapper">
        <div class="whyus-grid" :class="{
          'grid-reverse': index % 2 === 1
        }">
          <!-- Image Column -->
          <div class="whyus-media" :class="{ 'order-2': index % 2 === 1 }">
            <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.title || 'Why US2PK'" />
            <div v-else class="whyus-placeholder">No Image</div>
          </div>

          <!-- Content Column -->
          <div class="whyus-content" :class="{ 'order-1': index % 2 === 1 }">
            <div class="eyebrow on-dark">{{ item.subtitle || 'Why US2PK' }}</div>
            <h2>{{ item.title || 'Built for the way Pakistanis actually shop online.' }}</h2>
            <p>
              {{ item.content || 'No hidden fees, no vanishing packages, no guesswork on customs. Just a clear, tracked path from checkout to your doorstep.' }}
            </p>
            <ul class="check-list">
              <li v-for="feature in getFeatures(item)" :key="feature">
                <span class="mark">✓</span> {{ feature }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const props = defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();

// ============================================================
// GET ALL WHY US ITEMS
// ============================================================
const whyUsItems = computed(() => {
  // Use getWhyUsAll getter if available, otherwise fallback
  const items = landingStore.getWhyUsAll || landingStore.sections?.whyus || [];
 
  return items;
});

const getFeatures = (item) => {
  if (item?.features && Array.isArray(item.features)) {
    return item.features;
  }
  return [
    'Transparent, weight-based pricing calculated before you ship',
    'Dedicated support team fluent in English & Urdu',
    'Insured shipments with real-time tracking end to end',
    '12+ cities served with local last-mile delivery partners'
  ];
};

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  const base = import.meta.env.VITE_BASE_URL || 'https://us2pk.com';
  if (path.startsWith('/storage/')) return `${base}${path}`;
  if (path.startsWith('storage/')) return `${base}/${path}`;
  if (path.startsWith('whyus/')) return `${base}/storage/${path}`;
  return `${base}/storage/${path}`;
};
</script>

<style scoped>
.whyus-section-wrapper {
  background: #0A1330;
  color: #fff;
  padding: 96px 0;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

/* ============================================================
   SECTION HEADER
   ============================================================ */
.section-head {
  max-width: 620px;
  margin: 0 auto 56px;
  text-align: center;
}

.section-head.center .eyebrow {
  justify-content: center;
}

.section-head.center .eyebrow::before {
  display: none;
}

.section-head h2 {
  color: #fff;
  font-size: 36px;
  font-weight: 600;
  margin-bottom: 14px;
  font-family: 'Space Grotesk', sans-serif;
}

.section-head p {
  color: rgba(255, 255, 255, .6);
  font-size: 15.5px;
}

/* ============================================================
   WHY US GRID - Same pattern as About
   ============================================================ */
.whyus-grid-wrapper {
  margin-bottom: 80px;
}

.whyus-grid-wrapper:last-child {
  margin-bottom: 0;
}

.whyus-grid {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 64px;
  align-items: center;
}

.whyus-grid.grid-reverse {
  grid-template-columns: 0.9fr 1.1fr;
}

/* ============================================================
   MEDIA / IMAGE
   ============================================================ */
.whyus-media {
  position: relative;
  aspect-ratio: 1/1;
  background: #1E3170;
  border: 1px solid rgba(255, 255, 255, .14);
  border-radius: 4px;
  overflow: hidden;
}

.whyus-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.whyus-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, .3);
  font-size: 18px;
}

/* ============================================================
   CONTENT
   ============================================================ */
.whyus-content .eyebrow {
  color: #E0A93A;
}

.whyus-content .eyebrow::before {
  background: #E0A93A;
}

.whyus-content h2 {
  color: #fff;
  font-size: 34px;
  font-weight: 600;
  font-family: 'Space Grotesk', sans-serif;
  margin-bottom: 16px;
}

.whyus-content p {
  color: rgba(255, 255, 255, .6);
  font-size: 15.5px;
  margin-bottom: 24px;
}

.check-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.check-list li {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  font-size: 15px;
  color: rgba(255, 255, 255, .85);
}

.check-list li .mark {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: rgba(224, 169, 58, .15);
  border: 1px solid #E0A93A;
  color: #E0A93A;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  margin-top: 2px;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media(max-width:960px) {

  .whyus-grid,
  .whyus-grid.grid-reverse {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .whyus-grid-wrapper {
    margin-bottom: 60px;
  }

  .whyus-content h2 {
    font-size: 30px;
  }
}

@media(max-width:640px) {
  .whyus-section-wrapper {
    padding: 64px 0;
  }

  .wrap {
    padding: 0 16px;
  }

  .whyus-grid-wrapper {
    margin-bottom: 48px;
  }

  .whyus-content h2 {
    font-size: 24px;
  }

  .section-head h2 {
    font-size: 28px;
  }
}
</style>
