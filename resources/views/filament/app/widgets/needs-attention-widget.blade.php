<x-filament-widgets::widget class="fi-needs-attention-widget">
  @php($items = $this->getItems())
  <x-filament::section :heading="'Needs Attention'" :icon="\Filament\Support\Icons\Heroicon::OutlinedBellAlert" icon-color="brass" compact>
    @if (blank($items))
      <div class="flex items-center gap-3 py-2 text-sm text-gray-600 dark:text-gray-400">
        <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedCheckCircle" class="size-5 text-success-600 dark:text-success-400" />
        All clear. Nothing needs your attention right now.
      </div>
    @else
      <ul @class([
          'grid gap-3 sm:grid-cols-2',
          'xl:grid-cols-3' => count($items) === 3,
          'xl:grid-cols-4' => count($items) === 4,
          'xl:grid-cols-5' => count($items) >= 5,
      ])>
        @foreach ($items as $item)
          <li>
            <a href="{{ $item['url'] }}"
              class="group flex h-full items-start gap-3 rounded-lg p-3 ring-1 ring-gray-950/5 transition hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-primary-600 dark:ring-white/10 dark:hover:bg-white/5">
              <x-filament::icon :icon="$item['icon']" class="mt-0.5 size-5 shrink-0 text-gray-400 dark:text-gray-500" />
              <span class="min-w-0 flex-1">
                <span class="flex items-center gap-2">
                  <x-filament::badge :color="$item['color']">{{ number_format($item['count']) }}</x-filament::badge>
                  <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $item['label'] }}</span>
                </span>
                <span class="mt-1 block text-xs text-gray-600 dark:text-gray-400">{{ $item['description'] }}</span>
              </span>
              <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::ChevronRight"
                class="mt-0.5 size-4 shrink-0 text-gray-400 transition group-hover:translate-x-0.5 motion-reduce:transition-none dark:text-gray-500" />
            </a>
          </li>
        @endforeach
      </ul>
    @endif
  </x-filament::section>
</x-filament-widgets::widget>
