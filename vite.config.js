import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/landing/app.css',
        'resources/js/landing/app.jsx',
        'resources/css/filament/admin/theme.css',
        'resources/css/filament/app/theme.css'
      ],
      refresh: true
    })
  ]
})
