<x-filament-widgets::widget class="fi-filament-info-widget">
  <x-filament::section>
    <div class="flex items-center gap-x-3">
      <div class="flex-1">
        <div class="font-bold leading-6 tracking-tight">
          {{ $this->title }}
        </div>

        @if ($this->subtitle)
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $this->subtitle }}
          </p>
        @endif
      </div>

      <div class="flex flex-col items-end gap-y-1">
        <x-filament::link color="gray" href="https://docs.perscom.io" icon="heroicon-m-book-open"
          icon-alias="panels::widgets.filament-info.open-documentation-button" rel="noopener noreferrer" target="_blank">
          {{ __('filament-panels::widgets/filament-info-widget.actions.open_documentation.label') }}
        </x-filament::link>
        @if($this->plan)
          <x-filament::badge color="{{ $this->planColor ?? 'info' }}">{{ $this->plan }}</x-filament::badge>
        @endif
      </div>
    </div>
  </x-filament::section>
</x-filament-widgets::widget>
