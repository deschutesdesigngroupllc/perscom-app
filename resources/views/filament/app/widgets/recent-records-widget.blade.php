@php
  $tiles = [
      [
          'label' => 'Rank',
          'icon' => 'heroicon-o-chevron-double-up',
          'color' => 'warning',
          'image' => $this->getLatestRankRecord()?->rank?->image?->image_url,
          'record' => $this->getLatestRankRecord(),
          'title' => $this->getLatestRankRecord()?->rank?->name ?? 'Rank Change',
          'href' => $this->getUserRecordsUrl('4'),
      ],
      [
          'label' => 'Assignment',
          'icon' => 'heroicon-o-rectangle-stack',
          'color' => 'info',
          'image' => $this->getLatestAssignmentRecord()?->unit?->image?->image_url,
          'record' => $this->getLatestAssignmentRecord(),
          'title' =>
              $this->getLatestAssignmentRecord()?->position?->name ??
              ($this->getLatestAssignmentRecord()?->unit?->name ?? 'Assignment Change'),
          'href' => $this->getUserRecordsUrl('0'),
      ],
      [
          'label' => 'Award',
          'icon' => 'heroicon-o-trophy',
          'color' => 'warning',
          'image' => $this->getLatestAwardRecord()?->award?->image?->image_url,
          'record' => $this->getLatestAwardRecord(),
          'title' => $this->getLatestAwardRecord()?->award?->name ?? 'Award Received',
          'href' => $this->getUserRecordsUrl('1'),
      ],
      [
          'label' => 'Qualification',
          'icon' => 'heroicon-o-star',
          'color' => 'success',
          'image' => $this->getLatestQualificationRecord()?->qualification?->image?->image_url,
          'record' => $this->getLatestQualificationRecord(),
          'title' => $this->getLatestQualificationRecord()?->qualification?->name ?? 'Qualification Earned',
          'href' => $this->getUserRecordsUrl('3'),
      ],
      [
          'label' => 'Combat',
          'icon' => 'heroicon-o-fire',
          'color' => 'danger',
          'image' => null,
          'record' => $this->getLatestCombatRecord(),
          'title' => $this->getLatestCombatRecord() ? \Illuminate\Support\Str::limit($this->getLatestCombatRecord()->text, 30) : null,
          'href' => $this->getUserRecordsUrl('2'),
      ],
      [
          'label' => 'Service',
          'icon' => 'heroicon-o-clipboard-document-list',
          'color' => 'primary',
          'image' => null,
          'record' => $this->getLatestServiceRecord(),
          'title' => $this->getLatestServiceRecord() ? \Illuminate\Support\Str::limit($this->getLatestServiceRecord()->text, 30) : null,
          'href' => $this->getUserRecordsUrl('5'),
      ],
  ];
@endphp

<x-filament-widgets::widget>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($tiles as $tile)
      <a href="{{ $tile['href'] }}" class="group block">
        <x-filament::section compact>
          <div class="flex items-start gap-x-3">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-inset ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
              @if ($tile['image'])
                <img src="{{ $tile['image'] }}" alt="" class="h-7 w-7 object-contain" />
              @else
                <x-dynamic-component :component="$tile['icon']" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
              @endif
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center justify-between gap-x-2">
                <x-filament::badge :color="$tile['color']">
                  {{ $tile['label'] }}
                </x-filament::badge>
                <x-heroicon-m-arrow-up-right
                  class="h-4 w-4 shrink-0 text-gray-300 transition-colors group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400" />
              </div>
              @if ($tile['record'])
                <p class="mt-1 truncate text-sm font-medium text-gray-950 dark:text-white">
                  {{ $tile['title'] }}
                </p>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                  {{ $tile['record']->created_at->diffForHumans() }}
                </p>
              @else
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
              @endif
            </div>
          </div>
        </x-filament::section>
      </a>
    @endforeach
  </div>
</x-filament-widgets::widget>
