import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'
import {ViteImageOptimizer} from 'vite-plugin-image-optimizer'

export default defineConfig({
  plugins: [
    react(),
    laravel({
      input: ['resources/js/app.jsx', 'resources/css/nova.css'],
      refresh: true
    }),
    ViteImageOptimizer()
  ]
})
