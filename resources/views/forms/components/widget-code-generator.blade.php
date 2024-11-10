<div x-ignore ax-load ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('widget-code-generator') }}"
  x-on:update-code.window="updateCode($event.detail.code)" x-data="widgetCodeGenerator({
      state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }}
  })">
  <x-filament::button :color="$getColor()" x-on:click="copyCode().then(() => $tooltip('Copied'))">
    {{ $getLabel() }}
  </x-filament::button>
</div>
