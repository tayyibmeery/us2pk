<template>
  <section class="pricing-bg" id="pricing-section">
    <div class="wrap">
      <div class="section-head">
        <div class="eyebrow"><span class="rule"></span>Pricing<span class="rule"></span></div>
        <h2>{{ sectionTitle || 'Simple, weight-based shipping rates' }}</h2>
        <p>{{ sectionSubtitle || 'No membership required. Pay only for what you ship.' }}</p>
      </div>
      <div class="pricing-grid">
        <div class="price-card" v-for="(p, i) in displayPricing" :key="p.id" :class="{ featured: p.featured }">
          <span v-if="p.featured" class="price-tag">Most Popular</span>
          <div class="tariff-code">TARIFF / {{ String(i + 1).padStart(2, '0') }}</div>
          <h3>{{ p.title }}</h3>
          <div class="sub">{{ p.meta?.subtitle || 'Best for your shipping needs' }}</div>
          <div class="price-amount"><span class="num">{{ p.meta?.price || '$9.9' }}</span><span class="per">/
              {{ p.meta?.interval || 'kg' }}</span></div>
          <ul class="price-feats">
            <li v-for="(f, i) in getFeatures(p)" :key="i"><b>&check;</b> {{ f }}</li>
          </ul>
          <a href="#contact-section" class="btn-ink" :class="{ 'btn-amber': p.featured }">Get Quote</a>
          <div class="tear-edge"></div>
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
const displayPricing = computed(() => landingStore.getPricing);

const getFeatures = (plan) => {
  if (plan.features && Array.isArray(plan.features)) return plan.features;
  if (plan.content) {
    const matches = plan.content.match(/<li[^>]*>([^<]*)<\/li>/g);
    if (matches) return matches.map(m => m.replace(/<[^>]*>/g, '').trim());
  }
  return ['Standard shipping', 'Tracking included', 'Customer support'];
};

onMounted(() => { landingStore.fetchLandingData(); });
</script>

<style scoped>
.pricing-bg {
  background: #0F1B3D;
  background-image: radial-gradient(rgba(224, 169, 58, 0.10) 1px, transparent 1px);
  background-size: 26px 26px;
  padding: 110px 0;
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
  font-size: 36px;
  line-height: 1.2;
  color: #F6F1E4;
  margin: 0 0 14px;
}

.section-head p {
  font-family: 'Manrope', sans-serif;
  font-size: 15.5px;
  line-height: 1.6;
  color: #A8AEC4;
  margin: 0;
}

/* ============================================================
   PRICING GRID
   ============================================================ */
.pricing-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  align-items: stretch;
}

.price-card {
  position: relative;
  background: #F6F1E4;
  border-radius: 12px 12px 0 0;
  padding: 34px 30px 30px;
  display: flex;
  flex-direction: column;
  transition: transform .25s ease, box-shadow .25s ease;
}

.price-card:hover {
  transform: translateY(-6px);
}

.price-card.featured {
  box-shadow: 0 20px 44px rgba(0, 0, 0, 0.35);
}

/* torn receipt edge along the bottom of the card */
.price-card .tear-edge {
  height: 12px;
  margin: 0 -1px;
  background-image:
    linear-gradient(135deg, #0F1B3D 50%, transparent 50%),
    linear-gradient(45deg, #0F1B3D 50%, transparent 50%);
  background-size: 16px 16px;
  background-position: bottom left;
  background-repeat: repeat-x;
}

.tariff-code {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  letter-spacing: 0.05em;
  color: #2C8C86;
  margin-bottom: 16px;
}

/* stamp badge on the featured plan */
.price-tag {
  position: absolute;
  top: -14px;
  right: 22px;
  background: #F6F1E4;
  color: #B37E1C;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 10.5px;
  font-weight: 500;
  padding: 6px 12px;
  border: 1.5px dashed #E0A93A;
  border-radius: 3px;
  text-transform: uppercase;
  letter-spacing: .08em;
  transform: rotate(-4deg);
}

.price-card h3 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 19px;
  font-weight: 600;
  color: #0F1B3D;
  margin: 0 0 6px;
}

.price-card .sub {
  font-family: 'Manrope', sans-serif;
  font-size: 13.5px;
  color: #565B72;
  margin-bottom: 24px;
}

.price-amount {
  display: flex;
  align-items: baseline;
  gap: 6px;
  margin-bottom: 26px;
  padding-bottom: 22px;
  border-bottom: 1.5px dashed rgba(15, 27, 61, 0.18);
}

.price-amount .num {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 36px;
  font-weight: 600;
  color: #0F1B3D;
  font-variant-numeric: tabular-nums;
}

.price-amount .per {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 13px;
  color: #565B72;
}

/* ============================================================
   FEATURE LIST
   ============================================================ */
.price-feats {
  list-style: none;
  padding: 0;
  margin: 0 0 28px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.price-feats li {
  display: flex;
  gap: 10px;
  font-family: 'Manrope', sans-serif;
  font-size: 13.5px;
  color: #4B4F5E;
  padding: 10px 0;
  border-bottom: 1px dashed rgba(15, 27, 61, 0.12);
}

.price-feats li:last-child {
  border-bottom: none;
}

.price-feats li b {
  color: #2C8C86;
  font-weight: 600;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn-ink {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 14px 26px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 14.5px;
  border-radius: 3px;
  border: 1.5px solid #0F1B3D;
  color: #0F1B3D;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, background .15s ease, color .15s ease;
  text-decoration: none;
}

.btn-ink:hover {
  background: #0F1B3D;
  color: #F6F1E4;
}

.btn-amber {
  background: #E0A93A;
  color: #0A1330;
  border-color: #E0A93A;
}

.btn-amber:hover {
  background: #E0A93A;
  color: #0A1330;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(224, 169, 58, .35);
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 980px) {
  .pricing-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .pricing-bg {
    padding: 72px 0;
  }

  .pricing-grid {
    grid-template-columns: 1fr;
  }

  .section-head h2 {
    font-size: 28px;
  }
}
</style>
