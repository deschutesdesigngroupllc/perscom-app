const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./resources/**/*.{js,vue}'],
  prefix: 'da-',
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
