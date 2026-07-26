<template>
  <section id="team-section">
    <div class="wrap">
      <div class="section-head">
        <div class="eyebrow"><span class="rule"></span>Our Team<span class="rule"></span></div>
        <h2>{{ sectionTitle || 'The people behind every shipment' }}</h2>
      </div>
      <div class="team-grid">
        <div class="team-card" v-for="m in displayTeam" :key="m.id">
          <div class="badge">
            <div class="team-photo">
              <img v-if="m.image && !m.image.includes('/depot/')" :src="getImageUrl(m.image)" :alt="m.title" />
              <div v-else class="team-placeholder">{{ getInitials(m.title) }}</div>
            </div>
          </div>
          <div class="team-name">{{ m.title }}</div>
          <div class="team-role">{{ m.meta?.position || 'Team Member' }}</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const props = defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();
const displayTeam = computed(() => landingStore.getTeam);

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  const base = import.meta.env.VITE_BASE_URL || 'https://us2pk.com';
  if (path.startsWith('/storage/')) return `${base}${path}`;
  if (path.startsWith('storage/')) return `${base}/${path}`;
  return `${base}/storage/${path}`;
};

const getInitials = (name) => name ? name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) : '?';

onMounted(() => { landingStore.fetchLandingData(); });
</script>

<style scoped>
#team-section {
  padding: 110px 0;
  background: #EDEAE0;
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
  max-width: 560px;
  margin: 0 auto 64px;
  text-align: center;
}

.eyebrow {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 12.5px;
  letter-spacing: 0.12em;
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
  background: rgba(44, 140, 134, 0.4);
}

.section-head h2 {
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 36px;
  line-height: 1.2;
  color: #0F1B3D;
  margin: 0;
}

/* ============================================================
   TEAM GRID
   ============================================================ */
.team-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px 28px;
}

.team-card {
  position: relative;
  text-align: center;
  padding-top: 14px;
}

/* ============================================================
   BADGE (photo card styled as a crew ID badge)
   ============================================================ */
.badge {
  position: relative;
  border: 1.5px dashed rgba(15, 27, 61, 0.20);
  border-radius: 14px;
  padding: 14px 14px 20px;
  transition: border-color .25s ease, transform .25s ease;
}

.team-card:hover .badge {
  border-color: #E0A93A;
  transform: translateY(-5px);
}

/* punch hole at the top, like a lanyard clip */
.badge::before {
  content: "";
  position: absolute;
  top: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 16px;
  height: 16px;
  background: #EDEAE0;
  border: 1.5px dashed rgba(15, 27, 61, 0.20);
  border-radius: 50%;
  transition: border-color .25s ease;
}

.team-card:hover .badge::before {
  border-color: #E0A93A;
}

.team-photo {
  aspect-ratio: 1/1;
  border-radius: 8px;
  overflow: hidden;
  background: #0F1B3D;
}

.team-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(100%);
  transition: filter .35s ease, transform .35s ease;
}

.team-card:hover .team-photo img {
  filter: grayscale(0%);
  transform: scale(1.04);
}

.team-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(246, 241, 228, 0.85);
  font-size: 28px;
  font-family: 'IBM Plex Mono', monospace;
  background: #1E3170;
}

/* ============================================================
   NAME / ROLE
   ============================================================ */
.team-name {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 16px;
  font-weight: 600;
  color: #0F1B3D;
  margin-top: 16px;
}

.team-role {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11.5px;
  color: #2C8C86;
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-top: 4px;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 980px) {
  .team-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  #team-section {
    padding: 72px 0;
  }

  .wrap {
    padding: 0 18px;
  }

  .team-grid {
    grid-template-columns: 1fr 1fr;
    gap: 24px 18px;
  }

  .section-head h2 {
    font-size: 28px;
  }
}
</style>
