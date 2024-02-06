export default {
  plugins: {
    tailwindcss: {},
    'postcss-focus-visible': {
      replaceWith: '[data-focus-visible-added]'
    },
    autoprefixer: {}
  }
}
