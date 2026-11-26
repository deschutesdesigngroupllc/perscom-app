<x-filament-panels::page>
  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($this->getRecordTypes() as $type)
      <a href="{{ $type['url'] }}"
        class="group flex flex-col gap-3 rounded-xl bg-white p-5 ring-1 ring-gray-950/5 transition hover:ring-gray-950/15 focus-visible:outline-2 focus-visible:outline-primary-600 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-white/20">
        <span class="flex items-center gap-3">
          @if ($type['icon'])
            <x-filament::icon :icon="$type['icon']" class="size-5 text-gray-400 dark:text-gray-500" />
          @endif
          <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $type['label'] }}</span>
        </span>
        <span
          class="font-[family-name:var(--font-display)] text-3xl font-bold text-gray-950 tabular-nums dark:text-white">{{ number_format($type['total']) }}</span>
        <span class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
          <span>{{ number_format($type['recent']) }} in the last 30 days</span>
          <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::ChevronRight"
            class="size-4 text-gray-400 transition group-hover:translate-x-0.5 motion-reduce:transition-none" />
        </span>
      </a>
    @endforeach
  </div>
</x-filament-panels::page>
