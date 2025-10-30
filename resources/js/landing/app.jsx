import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createRoot } from 'react-dom/client'

import.meta.glob(['../../svg/**', '../../../images/landing/landing/**'])

const loadGetTermsScript = () => {
  const scriptId = 'getterms-embed-js'
  if (!document.getElementById(scriptId)) {
    const script = document.createElement('script')
    script.id = scriptId
    script.src = 'https://app.getterms.io/dist/js/embed.js'
    script.async = true
    document.body.appendChild(script)
  }
}

loadGetTermsScript()

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.jsx`, import.meta.glob('./pages/**/*.jsx')),
  progress: {
    color: '#2563EB'
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />)
  }
})
