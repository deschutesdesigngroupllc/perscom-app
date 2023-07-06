import React from 'react'
import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import.meta.glob(['../svg/**', '../images/**'])

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.jsx`, import.meta.glob('./pages/**/*.jsx')),
  progress: {
    color: '#2563EB'
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />)
  }
})
