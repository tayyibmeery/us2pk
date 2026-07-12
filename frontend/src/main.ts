import './assets/main.css'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import VueApexCharts from 'vue3-apexcharts'

// Suppress apexcharts touch warnings
const originalConsoleWarn = console.warn
console.warn = (...args: any[]) => {
  const message = args[0] || ''
  if (typeof message === 'string') {
    // Suppress specific warnings from apexcharts
    if (message.includes('Added non-passive event listener') ||
      message.includes('touchstart') ||
      message.includes('scroll-blocking')) {
      return
    }
    // Suppress vue3-apexcharts warnings
    if (message.includes('vue3-apexcharts')) {
      return
    }
  }
  originalConsoleWarn.apply(console, args)
}

// Also suppress the violation warnings from Chrome
const originalConsoleError = console.error
console.error = (...args: any[]) => {
  const message = args[0] || ''
  if (typeof message === 'string') {
    // Suppress [Violation] warnings
    if (message.includes('[Violation]') && message.includes('touchstart')) {
      return
    }
    if (message.includes('[Violation]') && message.includes('Added non-passive')) {
      return
    }
  }
  originalConsoleError.apply(console, args)
}

const app = createApp(App)

app.config.errorHandler = (err, instance, info) => {
  // Ignore apexcharts related errors
  if (err && typeof err === 'object' && 'message' in err) {
    const errorMessage = err.message || ''
    if (errorMessage.includes('hidden') || errorMessage.includes('apexcharts')) {
      console.debug('Apexcharts error suppressed:', errorMessage)
      return
    }
  }
  console.error('🔥 GLOBAL ERROR:', err)
  console.error('Component:', instance)
  console.error('Info:', info)
  if (err && typeof err === 'object' && 'stack' in err) {
    console.error(err.stack)
  }
}

app.use(createPinia())
app.use(router)
app.use(VueApexCharts)

function formatDate(date: string | undefined) {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-GB');
}

function truncateWords(text: string | null | undefined, limit = 15) {
  if (!text) return '—';
  const words = text.split(/\s+/);
  if (words.length <= limit) return text;
  return words.slice(0, limit).join(' ') + '…';
}

// Make them globally available in templates
app.config.globalProperties.formatDate = formatDate;
app.config.globalProperties.truncateWords = truncateWords;

// Fix for apexcharts touch events
app.mixin({
  mounted() {
    // Fix touch events for all apexcharts instances
    this.$nextTick(() => {
      const canvases = document.querySelectorAll('.apexcharts-canvas')
      canvases.forEach((el: any) => {
        if (el && el.style) {
          el.style.touchAction = 'auto !important'
        }
      })
    })
  }
})



app.mount('#app')
