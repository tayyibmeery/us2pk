<template>
  <div v-if="hasData" class="apex-chart-wrapper" ref="chartContainer">
    <VueApexCharts :type="type" :height="height" :options="options" :series="series" />
  </div>
  <div v-else class="flex items-center justify-center" :style="{ height: height + 'px' }">
    <p class="text-gray-400">No data available</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
  type: {
    type: String,
    default: 'line'
  },
  height: {
    type: [String, Number],
    default: 300
  },
  options: {
    type: Object,
    default: () => ({})
  },
  series: {
    type: Array,
    default: () => []
  },
  hasData: {
    type: Boolean,
    default: true
  }
})

const chartContainer = ref<HTMLElement | null>(null)

onMounted(async () => {
  await nextTick()
  // Fix touch events after chart renders
  if (chartContainer.value) {
    const canvases = chartContainer.value.querySelectorAll('.apexcharts-canvas')
    canvases.forEach(el => {
      if (el) {
        el.setAttribute('style', 'touch-action: auto !important')
      }
    })
  }
})
</script>

<style scoped>
.apex-chart-wrapper {
  touch-action: auto;
}

.apex-chart-wrapper :deep(.apexcharts-canvas) {
  touch-action: auto !important;
}
</style>
