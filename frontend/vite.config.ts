import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueJsx(),
    vueDevTools(),
  ],
  define: {
    'process.env': {},
    // Suppress console warnings in production
    'import.meta.env.VITE_SUPPRESS_WARNINGS': JSON.stringify(true),
  },
  optimizeDeps: {
    include: ['vue3-apexcharts', 'apexcharts'],
    // Exclude from optimization to prevent issues
    exclude: [],
  },
  server: {
    host: true,
    port: 5173,
    // Add this to suppress warnings during development
    watch: {
      usePolling: true,
    },
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  // Add this to handle warnings
  build: {
    rollupOptions: {
      onwarn(warning, warn) {
        // Suppress specific warnings
        if (warning.code === 'MODULE_LEVEL_DIRECTIVE') {
          return
        }
        warn(warning)
      },
    },
  },
})
