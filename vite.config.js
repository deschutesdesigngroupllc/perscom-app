import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer'

export default defineConfig({
  build: {
    rollupOptions: {
      onwarn(warning, warn) {
        if (warning.code === 'MODULE_LEVEL_DIRECTIVE') {
          return
        }
        warn(warning)
      }
    }
  },
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
    }),
    ViteImageOptimizer()
  ]
})
