import '@iframe-resizer/child'
import { Livewire } from '../../../vendor/livewire/livewire/dist/livewire.esm'

Livewire.start()

if (window.livewireScriptConfig) {
  window.livewireScriptConfig.uri = '/v2/widgets/livewire/update'
}
