<template>
  <section class="prohibited" id="prohibited-items">
    <div class="caution-stripe"></div>
    <div class="wrap">
      <div class="section-head">
        <div class="eyebrow"><span class="rule"></span>Know Before You Ship<span class="rule"></span></div>
        <h2>{{ sectionTitle || 'Items we\'re unable to carry' }}</h2>
        <p>{{ sectionSubtitle || 'Restricted by international customs and carrier regulations.' }}</p>
      </div>
      <div class="prohib-grid">
        <div class="prohib-card" v-for="item in displayItems" :key="item.id"
          :style="{ '--sev-color': getSeverityColor(item.severity), '--sev-bg': getSeverityBg(item.severity) }">
          <div class="prohib-top">
            <div class="prohib-icon"><i :class="item.icon || 'fas fa-ban'"></i></div>
            <span class="sev" :class="getSeverityClass(item.severity)">{{ getSeverityLabel(item.severity) }}</span>
          </div>
          <div v-if="item.category" class="prohib-category">{{ item.category }}</div>
          <h4>{{ item.item_name }}</h4>
          <p>{{ item.description || 'Restricted item' }}</p>
          <div class="prohib-reason"><span class="label">Reason —</span>
            {{ item.reason || 'Prohibited by customs regulations' }}</div>
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

// active items only, ordered by the `order` field
const displayItems = computed(() => {
  const items = landingStore.prohibitedItems || [];
  return items
    .filter(i => i.is_active === undefined || i.is_active === 1 || i.is_active === true)
    .slice()
    .sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
});

const getSeverityClass = (s) => ({ high: 'sev-high', medium: 'sev-medium', low: 'sev-low' }[s] || 'sev-medium');
const getSeverityLabel = (s) => ({ high: 'High Risk', medium: 'Medium Risk', low: 'Low Risk' }[s] || s);
const getSeverityColor = (s) => ({ high: '#EF9187', medium: '#E0A93A', low: '#7FCEC8' }[s] || '#E0A93A');
const getSeverityBg = (s) => ({ high: 'rgba(194,74,62,.16)', medium: 'rgba(224,169,58,.14)', low: 'rgba(44,140,134,.16)' }[s] || 'rgba(224,169,58,.14)');

onMounted(() => { landingStore.fetchProhibitedItems(); });
</script>

<style scoped>
.prohibited {
  background: #0A1330;
  color: #fff;
  padding-bottom: 110px;
}

/* caution stripe across the top of the section — the signature */
.caution-stripe {
  height: 10px;
  background-image: repeating-linear-gradient(-45deg,
      #E0A93A,
      #E0A93A 12px,
      #0A1330 12px,
      #0A1330 24px);
  margin-bottom: 96px;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

/* ============================================================
   SECTION HEAD
   ============================================================ */
.prohibited .section-head {
  max-width: 620px;
  margin: 0 auto 56px;
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

.prohibited .section-head h2 {
  color: #fff;
  font-size: 36px;
  font-weight: 600;
  font-family: 'Space Grotesk', sans-serif;
  margin: 0 0 14px;
}

.prohibited .section-head p {
  color: rgba(255, 255, 255, .55);
  font-family: 'Manrope', sans-serif;
  font-size: 15.5px;
  line-height: 1.6;
  margin: 0;
}

/* ============================================================
   GRID
   ============================================================ */
.prohib-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.prohib-card {
  background: rgba(255, 255, 255, .03);
  border: 1px solid rgba(255, 255, 255, .12);
  border-left: 3px solid var(--sev-color, #EF9187);
  border-radius: 6px;
  padding: 26px 24px 22px;
  transition: background .2s ease;
}

.prohib-card:hover {
  background: rgba(255, 255, 255, .05);
}

.prohib-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 18px;
}

.prohib-icon {
  width: 42px;
  height: 42px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  background: var(--sev-bg, rgba(194, 74, 62, .16));
  color: var(--sev-color, #EF9187);
  flex-shrink: 0;
}

/* flag-shaped severity tag */
.sev {
  position: relative;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 10px;
  padding: 4px 14px 4px 10px;
  text-transform: uppercase;
  letter-spacing: .05em;
  flex-shrink: 0;
  height: fit-content;
  clip-path: polygon(0 0, calc(100% - 6px) 0, 100% 50%, calc(100% - 6px) 100%, 0 100%);
}

.sev-high {
  background: rgba(194, 74, 62, .20);
  color: #EF9187;
}

.sev-medium {
  background: rgba(224, 169, 58, .18);
  color: #E0A93A;
}

.sev-low {
  background: rgba(44, 140, 134, .20);
  color: #7FCEC8;
}

.prohib-category {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 10.5px;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, .4);
  margin-bottom: 6px;
}

.prohib-card h4 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 16.5px;
  font-weight: 600;
  color: #fff;
  margin: 0 0 10px;
}

.prohib-card p {
  font-family: 'Manrope', sans-serif;
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(255, 255, 255, .55);
  margin: 0 0 16px;
}

.prohib-reason {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11.5px;
  line-height: 1.6;
  letter-spacing: .02em;
  color: rgba(255, 255, 255, .45);
  padding-top: 14px;
  border-top: 1px dashed rgba(255, 255, 255, .14);
}

.prohib-reason .label {
  color: rgba(255, 255, 255, .65);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 960px) {
  .prohib-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .prohibited {
    padding-bottom: 72px;
  }

  .caution-stripe {
    margin-bottom: 64px;
  }

  .prohib-grid {
    grid-template-columns: 1fr;
  }

  .prohibited .section-head h2 {
    font-size: 28px;
  }
}
</style>
