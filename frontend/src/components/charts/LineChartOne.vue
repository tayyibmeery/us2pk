<template>
  <div class="max-w-full overflow-x-auto custom-scrollbar">
    <div id="chartThree" class="-ml-4 min-w-[1000px] xl:min-w-full pl-2">
      <VueApexCharts type="area" height="310" :options="chartOptions" :series="series" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
})

const series = computed(() => props.chartData.series)
const chartOptions = computed(() => ({
  legend: { show: false, position: 'top', horizontalAlign: 'left' },
  colors: ['#465FFF', '#9CB9FF'],
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
    categories: props.chartData.categories,
    axisBorder: { show: false },
    axisTicks: { show: false },
    tooltip: { enabled: false },
  },
  yaxis: {
    title: { style: { fontSize: '0px' } },
  },
}))
</script>
