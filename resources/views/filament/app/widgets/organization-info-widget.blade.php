<x-filament-widgets::widget class="fi-organization-info-widget">
  <x-filament::section compact>
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        @if ($this->plan)
          <x-filament::badge color="{{ $this->planColor ?? 'gray' }}">{{ $this->plan }}</x-filament::badge>
        @endif
        @if ($this->version)
          <span class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $this->version }}</span>
        @endif
      </div>

      <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
        @if ($this->canResumeOnboarding())
          <x-filament::link tag="button" color="primary" icon="heroicon-m-rocket-launch" wire:click="resumeOnboarding">
            Resume Setup Wizard
          </x-filament::link>
        @endif
        <x-filament::link color="gray" href="https://docs.perscom.io" icon="heroicon-m-book-open" rel="noopener noreferrer"
          target="_blank">
          Documentation
        </x-filament::link>
      </div>
    </div>
  </x-filament::section>
</x-filament-widgets::widget>
