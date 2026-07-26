<template>
  <section id="faq-section">
    <div class="wrap">
      <div class="section-head">
        <div class="eyebrow"><span class="rule"></span>FAQ<span class="rule"></span></div>
        <h2>{{ sectionTitle || 'Common questions' }}</h2>
      </div>

      <div class="faq-columns">
        <div class="faq-col" v-for="(col, c) in columns" :key="c">
          <div class="faq-item" v-for="faq in col" :key="faq.globalIndex"
            :class="{ open: openItems.includes(faq.globalIndex) }">
            <div class="faq-q" @click="toggleItem(faq.globalIndex)">
              <span class="faq-tag">{{ String(faq.globalIndex + 1).padStart(2, '0') }}</span>
              <span class="faq-q-text">{{ faq.title || faq.question }}</span>
              <span class="faq-toggle"><i class="fas fa-chevron-down"></i></span>
            </div>
            <div class="faq-a">
              <div class="faq-a-inner">
                <p><span class="a-tag">A</span>{{ faq.content || faq.answer }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const props = defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();
const openItems = ref([0]);
const toggleItem = (i) => {
  const idx = openItems.value.indexOf(i);
  idx === -1 ? openItems.value.push(i) : openItems.value.splice(idx, 1);
};

const displayFaqs = computed(() => landingStore.getFaq || []);

// split into two columns, numbered sequentially left-to-right, top-to-bottom
const columns = computed(() => {
  const items = displayFaqs.value.map((faq, i) => ({ ...faq, globalIndex: i }));
  const mid = Math.ceil(items.length / 2);
  return [items.slice(0, mid), items.slice(mid)];
});

onMounted(() => { landingStore.fetchLandingData(); });
</script>

<style scoped>
#faq-section {
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
   TWO-COLUMN LAYOUT
   ============================================================ */
.faq-columns {
  max-width: 1020px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.faq-col {
  padding: 0 40px;
}

.faq-col:first-child {
  padding-left: 0;
  border-right: 1.5px dashed #C9C4AF;
}

.faq-col:last-child {
  padding-right: 0;
}

.faq-item {
  border-bottom: 1.5px dashed #C9C4AF;
}

.faq-col>.faq-item:last-child {
  border-bottom: none;
}

.faq-q {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px 2px;
  cursor: pointer;
}

.faq-tag {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11.5px;
  color: #B79A5C;
  padding-top: 3px;
  flex-shrink: 0;
}

.faq-q-text {
  flex: 1;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 15px;
  line-height: 1.4;
  color: #0F1B3D;
}

.faq-toggle {
  flex-shrink: 0;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 1.5px solid rgba(15, 27, 61, 0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2C8C86;
  font-size: 10px;
  transition: transform .25s ease, border-color .25s ease, background .25s ease, color .25s ease;
}

.faq-item.open .faq-toggle {
  transform: rotate(180deg);
  background: #0F1B3D;
  border-color: #0F1B3D;
  color: #E0A93A;
}

/* CSS-only height animation to true content height, no clipping */
.faq-a {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows .3s ease;
}

.faq-item.open .faq-a {
  grid-template-rows: 1fr;
}

.faq-a-inner {
  overflow: hidden;
}

.faq-a-inner p {
  display: flex;
  gap: 14px;
  margin: 0;
  padding: 0 2px 22px 40px;
  font-family: 'Manrope', sans-serif;
  font-size: 13.5px;
  line-height: 1.6;
  color: #565B72;
}

.faq-a-inner p .a-tag {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11.5px;
  color: #2C8C86;
  flex-shrink: 0;
  margin-left: -18px;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 900px) {
  .faq-columns {
    grid-template-columns: 1fr;
  }

  .faq-col {
    padding: 0;
  }

  .faq-col:first-child {
    padding-left: 0;
    border-right: none;
    border-bottom: 1.5px dashed #C9C4AF;
    padding-bottom: 8px;
    margin-bottom: 8px;
  }
}

@media (max-width: 640px) {
  #faq-section {
    padding: 72px 0;
  }

  .section-head h2 {
    font-size: 28px;
  }

  .faq-q-text {
    font-size: 14.5px;
  }
}
</style>
