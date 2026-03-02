import fs from 'fs'
import path from 'path'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer'

function optionalImports() {
  return {
    name: 'optional-imports',
    enforce: 'pre',
    transform(code, id) {
      if (!id.endsWith('.css')) return

      const dir = path.dirname(id)

      return code.replace(
        /^@import\s+['"]([^'"]+)['"]\s*;?\s*$/gm,
        (match, importPath) => {
          if (!importPath.includes('/vendor/')) return match

          const resolved = path.resolve(dir, importPath)
          if (!fs.existsSync(resolved)) {
            return '/* optional: ' + importPath + ' */'
          }

          return match
        }
      )
    }
  }
}

export default defineConfig({
  build: {
    sourcemap: true
  },
  plugins: [
    optionalImports(),
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/landing/app.css',
        'resources/js/landing/app.jsx',
        'resources/css/filament/admin/theme.css',
        'resources/css/filament/app/theme.css',
        'resources/js/filament/app/app.jsx',
        'resources/css/widgets/app.css',
        'resources/js/widgets/app.js'
      ],
      refresh: true
    }),
    react(),
    ViteImageOptimizer()
  ]
})
