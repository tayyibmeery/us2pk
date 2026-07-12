<template>
  <ApexChartWrapper type="pie" height="300" :options="options" :series="series" :hasData="hasData" />
</template>

<script setup lang="ts">
import { computed } from 'vue'
import ApexChartWrapper from './ApexChartWrapper.vue'

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
    default: () => ({ labels: [], series: [] })
  },
})

const hasData = computed(() => {
  if (!props.chartData) return false
  const labels = props.chartData.labels || []
  const series = props.chartData.series || []
  return labels.length > 0 && series.length > 0 && series.some((v: number) => v > 0)
})

const options = computed(() => ({
  labels: props.chartData?.labels || [],
  legend: { position: 'bottom', fontFamily: 'Outfit', markers: { radius: 99 } },
  colors: ['#465FFF', '#9CB9FF', '#FF9F9F', '#FFD966'],
  dataLabels: { enabled: false },
  stroke: { show: false },
}))

const series = computed(() => {
  if (!props.chartData?.series) return []
  return props.chartData.series
})
</script>
