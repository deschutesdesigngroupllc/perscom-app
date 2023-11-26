const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['../../resources/views/fields/html/**/*.php'],
  prefix: 'hf-',
  theme: {
    extend: {
      colors: {
        gray: colors.slate
      }
    }
  },
  corePlugins: {
    preflight: false
  }
}