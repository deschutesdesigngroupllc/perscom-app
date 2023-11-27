let mix = require('laravel-mix')

require('./nova.mix')

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue({ version: 3 })
  .postCss('resources/css/field.css', 'css', [require('tailwindcss')])
  .nova('perscom/HtmlField')
