import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import viteReact from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    viteReact(),
    laravel({
      input: ['resources/js/app.js'],
      refresh: true
    })
  ]
})
