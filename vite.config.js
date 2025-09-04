import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/landing/app.css',
        'resources/js/landing/app.jsx',
        'resources/css/filament/admin/theme.css',
        'resources/css/filament/app/theme.css',
        'resources/css/widgets/app.css',
        'resources/js/widgets/app.js'
      ],
      refresh: true
    }),
    react(),
    ViteImageOptimizer()
  ]
})
