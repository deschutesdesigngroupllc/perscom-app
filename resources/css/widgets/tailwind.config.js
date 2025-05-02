import preset from '../../../vendor/filament/filament/tailwind.config.preset'

export default {
  presets: [preset],
  darkMode: 'class',
  content: [
    './app/Livewire/Widgets/**/*.php',
    './resources/views/widgets/**/*.blade.php',
    './resources/views/components/widgets/**/*.blade.php',
    './resources/views/livewire/widgets/**/*.blade.php',
    './vendor/filament/**/*.blade.php'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: 'rgb(239, 246, 255)',
          100: 'rgb(219, 234, 254)',
          200: 'rgb(191, 219, 254)',
          300: 'rgb(147, 197, 253)',
          400: 'rgb(96, 165, 250)',
          500: 'rgb(59, 130, 246)',
          600: 'rgb(37, 99, 235)',
          700: 'rgb(29, 78, 216)',
          800: 'rgb(30, 64, 175)',
          900: 'rgb(30, 58, 138)',
          950: 'rgb(30, 58, 138)'
        }
      }
    }
  }
}
