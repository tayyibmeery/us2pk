<template>
  <section id="about-section" class="about-section-wrapper">
    <div class="wrap">
      <!-- Section Header -->
      <div class="section-head center">
        <div class="eyebrow">About US2PK</div>
        <!-- <p>{{ sectionSubtitle || 'Learn more about our journey and mission.' }}</p> -->
        <h2>{{ sectionTitle || 'The bridge between American retail and Pakistani doorsteps.' }}</h2>

      </div>

      <!-- About Items with Alternating Layout -->
      <div v-for="(item, index) in aboutItems" :key="item.id || index" class="about-grid-wrapper">
        <div class="about-grid" :class="{
          'grid-reverse': index % 2 === 1
        }">
          <!-- Image Column -->
          <div class="about-media" :class="{ 'order-2': index % 2 === 1 }">
            <img v-if="item.image" :src="getImageUrl(item.image)" :alt="item.title" />
            <div v-else class="about-placeholder">No Image</div>
            <div class="stamp">EST. 2016 · Lahore HQ</div>
          </div>

          <!-- Content Column -->
          <div class="about-content" :class="{ 'order-1': index % 2 === 1 }">
            <div class="eyebrow">{{ item.subtitle || 'About US2PK' }}</div>
            <h2>{{ item.title || 'Quick Transport and Logistics Solutions' }}</h2>
            <p>
              {{ item.content || 'We built US2PK because Pakistanis deserve the same access to global products as anyone else. From a US mailbox to final-mile delivery in Lahore, Karachi, or Islamabad, every step is handled in-house — no middlemen, no surprises at customs.' }}
            </p>
            <div class="feature-list">
              <div class="feature" v-for="f in getFeatures(item)" :key="f.title">
                <h4>{{ f.title }}</h4>
                <p>{{ f.description }}</p>
              </div>
            </div>
            <a href="#contact-section" class="btn btn-ink">Get In Touch</a>
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
// USE getAboutAll TO GET ALL ABOUT SECTIONS
// ============================================================
const aboutItems = computed(() => {
  // Use the getAboutAll getter which returns all about sections
  const items = landingStore.getAboutAll || [];
 
  return items;
});

const getFeatures = (item) => {
  if (item?.features && Array.isArray(item.features)) {
    return item.features;
  }
  return [
    { title: 'Global Coverage', description: 'Ship from any US retailer to any city in Pakistan.' },
    { title: 'On-Time Delivery', description: 'Guaranteed transit windows for air and sea freight.' },
    { title: 'Customs Handled', description: 'Full documentation and clearance managed for you.' },
    { title: 'Real Consolidation', description: 'Combine multiple orders into one shipment, one bill.' }
  ];
};

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  const base = import.meta.env.VITE_BASE_URL || 'https://us2pk.com';
  if (path.startsWith('/storage/')) return `${base}${path}`;
  if (path.startsWith('storage/')) return `${base}/${path}`;
  return `${base}/storage/${path}`;
};
</script>

<style scoped>
.about-section-wrapper {
  padding: 10px 0;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

/* Section Header */
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
  font-size: 36px;
  font-weight: 600;
  margin-bottom: 14px;
  font-family: 'Space Grotesk', sans-serif;
}

.section-head p {
  font-size: 15.5px;
  color: #4B4F5E;
}

/* About Grid Wrapper - spacing between items */
.about-grid-wrapper {
  margin-bottom: 80px;
}

.about-grid-wrapper:last-child {
  margin-bottom: 0;
}

/* About Grid with Alternating Layout */
.about-grid {
  display: grid;
  grid-template-columns: 0.9fr 1.1fr;
  gap: 64px;
  align-items: center;
}

.about-grid.grid-reverse {
  grid-template-columns: 1.1fr 0.9fr;
}

/* Mobile: override for stacking */
.about-media {
  position: relative;
  aspect-ratio: 4/5;
  background: #0F1B3D;
  border-radius: 4px;
  overflow: hidden;
}

.about-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.about-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, .3);
  font-size: 18px;
  background: #1E3170;
}

.about-media .stamp {
  position: absolute;
  bottom: 20px;
  left: 20px;
  background: #F6F4EE;
  color: #0A1330;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  letter-spacing: .08em;
  text-transform: uppercase;
  padding: 10px 14px;
  border-radius: 2px;
  border: 1px solid #0A1330;
  transform: rotate(-2deg);
}

.about-content h2 {
  font-size: 36px;
  font-weight: 600;
  margin-bottom: 20px;
  font-family: 'Space Grotesk', sans-serif;
}

.about-content p {
  font-size: 15.5px;
  line-height: 1.65;
  color: #4B4F5E;
}

.eyebrow {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 12px;
  letter-spacing: .16em;
  text-transform: uppercase;
  color: #2C8C86;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}

.eyebrow::before {
  content: "";
  width: 22px;
  height: 1px;
  background: #2C8C86;
  display: inline-block;
}

.feature-list {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 28px;
  margin: 36px 0 40px;
}

.feature {
  border-left: 2px solid #2C8C86;
  padding-left: 16px;
}

.feature h4 {
  font-size: 16px;
  margin-bottom: 6px;
  font-family: 'Space Grotesk', sans-serif;
}

.feature p {
  font-size: 14px;
}

.btn-ink {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 26px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 15px;
  border-radius: 2px;
  border: 1px solid #0F1B3D;
  color: #0F1B3D;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  text-decoration: none;
}

.btn-ink:hover {
  background: #0F1B3D;
  color: #fff;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media(max-width:960px) {

  .about-grid,
  .about-grid.grid-reverse {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .about-grid-wrapper {
    margin-bottom: 60px;
  }

  .about-content h2 {
    font-size: 30px;
  }
}

@media(max-width:640px) {
  .about-section-wrapper {
    padding: 64px 0;
  }

  .wrap {
    padding: 0 16px;
  }

  .about-grid-wrapper {
    margin-bottom: 48px;
  }

  .feature-list {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .about-content h2 {
    font-size: 24px;
  }

  .section-head h2 {
    font-size: 28px;
  }

  .about-media .stamp {
    font-size: 9px;
    padding: 6px 10px;
    bottom: 12px;
    left: 12px;
  }
}
</style>
