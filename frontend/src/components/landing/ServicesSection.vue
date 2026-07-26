<template>
  <section id="services-section">
    <div class="wrap">
      <div class="section-head">
        <div class="eyebrow"><span class="rule"></span>Our Services<span class="rule"></span></div>
        <h2>{{ sectionTitle || 'One partner, every mile of the journey' }}</h2>
        <p>
          {{ sectionSubtitle || 'From the moment your order ships in the US, to the moment it reaches your hands in Pakistan.' }}
        </p>
      </div>
      <div class="services-grid">
        <div class="service-card" v-for="(s, i) in displayServices" :key="s.id">
          <div class="stub">
            <div class="route-code">
              <span class="num">{{ String(i + 1).padStart(2, '0') }}</span>{{ routeLabels[i] || 'SERVICE' }}
            </div>
            <div class="stub-icon">
              <img v-if="s.image" :src="getImageUrl(s.image)" :alt="s.title" loading="lazy" @error="handleImageError" />
              <i v-else :class="iconClasses[i] || 'fas fa-box'"></i>
            </div>
          </div>
          <div class="tear"></div>
          <div class="body-content">
            <h3>{{ s.title }}</h3>
            <p>{{ s.content || s.meta?.description || 'Service description' }}</p>
            <span class="service-link">Learn more <i class="fas fa-arrow-right"></i></span>
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
const displayServices = computed(() => landingStore.getServices || []);

// Short route-style labels and fallback icons, keyed by card position
const routeLabels = ['US → PK · AIR', 'US → PK · SEA', 'PK · LAND', 'CUSTOMS', 'US WAREHOUSE', 'LIVE STATUS'];
const iconClasses = ['fas fa-plane', 'fas fa-ship', 'fas fa-truck', 'fas fa-file-invoice', 'fas fa-warehouse', 'fas fa-location-crosshairs'];

// ============================================================
// IMAGE HELPER - Resolve image path
// ============================================================
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

  if (imagePath.startsWith('services/')) {
    return `${baseUrl}/storage/${imagePath}`;
  }

  if (imagePath.startsWith('/')) {
    return `${baseUrl}${imagePath}`;
  }

  return `${baseUrl}/storage/${imagePath}`;
};

// ============================================================
// HANDLE IMAGE ERROR - Fall back to the icon tile
// ============================================================
const handleImageError = (event) => {
  const img = event.target;
  img.style.display = 'none';
  const parent = img.parentElement;
  if (parent) {
    const fallback = document.createElement('i');
    fallback.className = 'fas fa-box';
    parent.appendChild(fallback);
  }
};

// Remove duplicate fetch - Landing.vue already fetches data
// onMounted(() => { landingStore.fetchLandingData(); });
</script>

<style scoped>
#services-section {
  padding: 110px 0;
  background: #0F1B3D;
  background-image: radial-gradient(rgba(224, 169, 58, 0.10) 1px, transparent 1px);
  background-size: 26px 26px;
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
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #E0A93A;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
}

.eyebrow .rule {
  width: 28px;
  height: 1px;
  background: rgba(224, 169, 58, 0.5);
}

.section-head h2 {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 38px;
  line-height: 1.2;
  color: #F6F1E4;
  margin: 0 0 16px;
}

.section-head p {
  font-family: 'Manrope', sans-serif;
  font-size: 15.5px;
  line-height: 1.65;
  color: #A8AEC4;
  margin: 0;
}

/* ============================================================
   SERVICES GRID
   ============================================================ */
.services-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
}

.service-card {
  position: relative;
  background: #F6F1E4;
  border-radius: 14px;
  padding-top: 8px;
  display: flex;
  flex-direction: column;
  transition: transform .28s ease, box-shadow .28s ease;
}

.service-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.28);
}

/* corner notches punch the ticket out of the navy field */
.service-card::before,
.service-card::after {
  content: "";
  position: absolute;
  top: -9px;
  width: 18px;
  height: 18px;
  background: #0F1B3D;
  border-radius: 50%;
  z-index: 2;
}

.service-card::before {
  left: -9px;
}

.service-card::after {
  right: -9px;
}

/* ============================================================
   STUB (top area: route code + icon/image)
   ============================================================ */
.stub {
  padding: 30px 30px 22px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.route-code {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11.5px;
  letter-spacing: 0.06em;
  color: #2C8C86;
}

.route-code .num {
  color: #B79A5C;
  margin-right: 6px;
}

.stub-icon {
  width: 46px;
  height: 46px;
  border-radius: 10px;
  background: rgba(15, 27, 61, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
  color: #0F1B3D;
  overflow: hidden;
  flex-shrink: 0;
  transition: background .25s ease, color .25s ease, transform .25s ease;
}

.stub-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.service-card:hover .stub-icon {
  background: #0F1B3D;
  color: #E0A93A;
  transform: scale(1.06);
}

/* tear line with side notches, like a torn ticket stub */
.tear {
  position: relative;
  border-top: 1.5px dashed rgba(15, 27, 61, 0.18);
  margin: 0 30px;
}

.tear::before,
.tear::after {
  content: "";
  position: absolute;
  top: -8px;
  width: 16px;
  height: 16px;
  background: #0F1B3D;
  border-radius: 50%;
}

.tear::before {
  left: -46px;
}

.tear::after {
  right: -46px;
}

/* ============================================================
   BODY CONTENT
   ============================================================ */
.body-content {
  padding: 24px 30px 30px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.service-card h3 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 19px;
  font-weight: 600;
  color: #0F1B3D;
  margin: 0 0 10px;
}

.service-card p {
  font-family: 'Manrope', sans-serif;
  font-size: 14px;
  line-height: 1.6;
  color: #565B72;
  margin: 0 0 22px;
  flex: 1;
}

.service-link {
  font-family: 'Manrope', sans-serif;
  font-size: 13px;
  font-weight: 600;
  color: #0F1B3D;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  letter-spacing: 0.01em;
  cursor: pointer;
}

.service-link i {
  font-size: 11px;
  transition: transform .25s ease;
}

.service-card:hover .service-link {
  color: #B37E1C;
}

.service-card:hover .service-link i {
  transform: translateX(3px);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 980px) {
  .services-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  #services-section {
    padding: 72px 0;
  }

  .wrap {
    padding: 0 18px;
  }

  .services-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .section-head h2 {
    font-size: 29px;
  }
}
</style>
