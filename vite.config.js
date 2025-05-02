import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  server: {
    cors: true
  },
  plugins: [
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
    })
  ]
})
