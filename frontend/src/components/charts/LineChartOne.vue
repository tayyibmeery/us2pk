<template>
  <ApexChartWrapper type="area" height="310" :options="chartOptions" :series="series" :hasData="hasData" />
</template>

<script setup lang="ts">
import { computed } from 'vue'
import ApexChartWrapper from './ApexChartWrapper.vue'

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
    default: () => ({ categories: [], series: [] })
  },
  colors: {
    type: Array,
    default: () => ['#465FFF', '#9CB9FF']
  }
})

const hasData = computed(() => {
  if (!props.chartData) return false
  const categories = props.chartData.categories || []
  const series = props.chartData.series || []
  return categories.length > 0 && series.length > 0 && series.some((s: any) => s.data?.length > 0)
})

const series = computed(() => {
  if (!props.chartData?.series) return []
  return props.chartData.series
})

const chartOptions = computed(() => ({
  legend: { show: false, position: 'top', horizontalAlign: 'left' },
  colors: props.colors || ['#465FFF', '#9CB9FF'],
  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'area',
    toolbar: { show: false },
  },
  fill: {
    gradient: {
      enabled: true,
      opacityFrom: 0.55,
      opacityTo: 0,
    },
  },
  stroke: { curve: 'straight', width: [2, 2] },
  markers: { size: 0 },
  labels: { show: false, position: 'top' },
  grid: {
    xaxis: { lines: { show: false } },
    yaxis: { lines: { show: true } },
  },
  dataLabels: { enabled: false },
  tooltip: { x: { format: 'dd MMM yyyy' } },
  xaxis: {
    categories: props.chartData?.categories || [],
    axisBorder: { show: false },
    axisTicks: { show: false },
    tooltip: { enabled: false },
  },
  yaxis: {
    title: { style: { fontSize: '0px' } },
  },
}))
</script>
