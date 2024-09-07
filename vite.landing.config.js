import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    react(),
    laravel({
      hotFile: 'public/landing.hot',
      buildDirectory: 'landing',
      input: ['resources/js/landing/app.jsx', 'resources/css/landing/app.css'],
      refresh: ['resources/js/landing/**', 'resources/views/landing/**']
    })
  ]
})
