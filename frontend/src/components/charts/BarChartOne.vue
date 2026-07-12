<template>
  <ApexChartWrapper type="bar" height="180" :options="chartOptions" :series="series" :hasData="hasData" />
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
  color: {
    type: String,
    default: '#465FFF'
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
  colors: [props.color || '#465fff'],
  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'bar',
    toolbar: { show: false },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '39%',
      borderRadius: 5,
      borderRadiusApplication: 'end',
    },
  },
  dataLabels: { enabled: false },
  stroke: { show: true, width: 4, colors: ['transparent'] },
  xaxis: {
    categories: props.chartData?.categories || [],
    axisBorder: { show: false },
    axisTicks: { show: false },
  },
  legend: {
    show: true,
    position: 'top',
    horizontalAlign: 'left',
    fontFamily: 'Outfit',
    markers: { radius: 99 },
  },
  yaxis: { title: false },
  grid: { yaxis: { lines: { show: true } } },
  fill: { opacity: 1 },
  tooltip: {
    x: { show: false },
    y: { formatter: (val: number) => val.toString() },
  },
}))
</script>
