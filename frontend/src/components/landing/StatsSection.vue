<template>
  <div class="stats-strip">
    <div class="wrap">
      <div class="stat-item">
        <div class="num">{{ stats?.happy_clients || '18,400+' }}</div>
        <div class="label">Happy Clients</div>
      </div>
      <div class="stat-item">
        <div class="num">{{ stats?.complete_shipments || '54,900+' }}</div>
        <div class="label">Shipments Delivered</div>
      </div>
      <div class="stat-item">
        <div class="num">{{ stats?.customer_reviews || '4.8 / 5' }}</div>
        <div class="label">Customer Rating</div>
      </div>
      <div class="stat-item">
        <div class="num">{{ stats?.active_services || '12' }}</div>
        <div class="label">Cities Served in PK</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();
const stats = computed(() => landingStore.getStats);

onMounted(() => { landingStore.fetchLandingData(); });
</script>

<style scoped>
.stats-strip {
  background: #0F1B3D;
  border-top: 1px solid rgba(255, 255, 255, .14);
  border-bottom: 1px solid rgba(255, 255, 255, .14);
}

.stats-strip .wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 36px 32px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
}

.stat-item {
  text-align: center;
  border-right: 1px solid rgba(255, 255, 255, .14);
  padding: 0 12px;
}

.stat-item:last-child {
  border-right: none;
}

.stat-item .num {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 32px;
  font-weight: 600;
  color: #E0A93A;
}

.stat-item .label {
  font-size: 12.5px;
  color: rgba(255, 255, 255, .55);
  text-transform: uppercase;
  letter-spacing: .06em;
  margin-top: 6px;
}

@media(max-width:960px) {
  .stats-strip .wrap {
    grid-template-columns: 1fr 1fr;
    gap: 24px;
  }

  .stat-item {
    border-right: none;
  }
}

@media(max-width:640px) {
  .stats-strip .wrap {
    grid-template-columns: 1fr;
    gap: 16px;
    padding: 24px 16px;
  }

  .stat-item .num {
    font-size: 24px;
  }
}
</style>
