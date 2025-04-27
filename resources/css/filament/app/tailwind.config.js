import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
  presets: [preset],
  content: [
    './app/Filament/App/**/*.php',
    './app/Livewire/App/**/*.php',
    './resources/views/filament/render-hooks/**/*.blade.php',
    './resources/views/filament/app/**/*.blade.php',
    './resources/views/livewire/app/**/*.blade.php',
    './resources/views/models/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    './vendor/awcodes/filament-quick-create/resources/**/*.blade.php',
    './vendor/archilex/filament-filter-sets/**/*.php'
  ]
}
