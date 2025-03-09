import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
  presets: [preset],
  content: [
    './app/Filament/Admin/**/*.php',
    './resources/views/filament/render-hooks/**/*.blade.php',
    './resources/views/filament/admin/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php'
  ]
}
