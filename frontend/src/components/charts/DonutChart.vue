<template>
  <VueApexCharts type="donut" :options="options" :series="series" height="300" />
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

const options = computed(() => ({
  labels: props.chartData.labels,
  legend: { position: 'bottom', fontFamily: 'Outfit', markers: { radius: 99 } },
  colors: ['#465FFF', '#9CB9FF', '#FF9F9F'],
  plotOptions: { pie: { donut: { size: '60%' } } },
  dataLabels: { enabled: false },
  stroke: { show: false },
}))
const series = computed(() => props.chartData.series)
</script>
