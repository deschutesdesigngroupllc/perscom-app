import preset from '../../../vendor/filament/filament/tailwind.config.preset'

export default {
  presets: [preset],
  content: [
    './app/Livewire/Widgets/**/*.php',
    './resources/views/widgets/**/*.blade.php',
    './resources/views/components/widgets/**/*.blade.php',
    './resources/views/livewire/widgets/**/*.blade.php',
    './vendor/filament/**/*.blade.php'
  ]
}
