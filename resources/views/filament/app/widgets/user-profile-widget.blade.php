<x-filament-widgets::widget>
  <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-filament::section icon="heroicon-o-chevron-double-up" icon-color="warning">
      <x-slot name="heading">Current Rank</x-slot>
      <x-slot name="description">Your current rank</x-slot>

      <div class="flex items-center gap-x-4">
        @if ($this->getRankImageUrl())
          <div
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
            <img src="{{ $this->getRankImageUrl() }}" alt="{{ $this->getRank()?->name }}" class="h-12 w-12 object-contain" />
          </div>
        @else
          <div
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
            <x-heroicon-o-chevron-double-up class="h-7 w-7 text-gray-400 dark:text-gray-500" />
          </div>
        @endif

        <div class="min-w-0 flex-1">
          @if ($this->getRank())
            <p class="truncate text-base font-semibold text-gray-950 dark:text-white">
              {{ $this->getRank()->name }}
            </p>
            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
              @if ($this->getRank()->abbreviation)
                <x-filament::badge color="gray">{{ $this->getRank()->abbreviation }}</x-filament::badge>
              @endif
              @if ($this->getRank()->paygrade)
                <x-filament::badge color="gray">{{ $this->getRank()->paygrade }}</x-filament::badge>
              @endif
            </div>
          @else
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No rank assigned</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Contact your administrator</p>
          @endif
        </div>
      </div>

      <x-slot name="footerActions">
        <x-filament::link :href="$this->getRankRecordsUrl()" color="gray" icon="heroicon-m-clipboard-document-list" icon-position="before" size="sm">
          View Rank Records
        </x-filament::link>
      </x-slot>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-rectangle-stack" icon-color="info">
      <x-slot name="heading">Current Assignment</x-slot>
      <x-slot name="description">Your position &amp; unit</x-slot>

      <div class="flex items-center gap-x-4">
        @if ($this->getUnitImageUrl())
          <div
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
            <img src="{{ $this->getUnitImageUrl() }}" alt="{{ $this->getUnit()?->name }}" class="h-12 w-12 object-contain" />
          </div>
        @else
          <div
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
            <x-heroicon-o-identification class="h-7 w-7 text-gray-400 dark:text-gray-500" />
          </div>
        @endif

        <div class="min-w-0 flex-1">
          @if ($this->getPosition() || $this->getUnit())
            @if ($this->getPosition())
              <p class="truncate text-base font-semibold text-gray-950 dark:text-white">
                {{ $this->getPosition()->name }}
              </p>
            @endif
            @if ($this->getUnit())
              <p class="mt-0.5 truncate text-sm text-gray-500 dark:text-gray-400">
                {{ $this->getUnit()->name }}
              </p>
            @endif
          @else
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No current assignment</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Contact your administrator</p>
          @endif
        </div>
      </div>

      <x-slot name="footerActions">
        <x-filament::link :href="$this->getAssignmentRecordsUrl()" color="gray" icon="heroicon-m-clipboard-document-list" icon-position="before" size="sm">
          View Assignment Records
        </x-filament::link>
      </x-slot>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-calendar-days" icon-color="success">
      <x-slot name="heading">Upcoming Events</x-slot>
      <x-slot name="description">Next scheduled events</x-slot>

      <div class="-mx-2 space-y-1">
        @forelse ($this->getUpcomingEvents() as $event)
          <a href="{{ $this->getEventUrl($event) }}"
            class="group flex items-center gap-x-3 rounded-lg px-2 py-1.5 transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
            @if ($event->image?->image_url)
              <img src="{{ $event->image->image_url }}" alt="{{ $event->name }}"
                class="h-9 w-9 shrink-0 rounded-md object-cover ring-1 ring-gray-200 dark:ring-gray-700" />
            @else
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
                <x-heroicon-o-calendar class="h-4 w-4 text-gray-400 dark:text-gray-500" />
              </div>
            @endif

            <div class="min-w-0 flex-1">
              <p
                class="truncate text-sm font-medium text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                {{ $event->name }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ $event->starts->format('M j, Y') }}@if (!$event->all_day)
                  &middot; {{ $event->starts->format('g:i A') }}
                @endif
              </p>
            </div>

            <x-heroicon-m-chevron-right
              class="h-4 w-4 shrink-0 text-gray-300 transition-colors group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400" />
          </a>
          @empty
            <div class="px-2 py-4 text-center">
              <div
                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
                <x-heroicon-o-calendar class="h-5 w-5 text-gray-400 dark:text-gray-500" />
              </div>
              <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">No upcoming events</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">Check back later</p>
            </div>
          @endforelse
        </div>

        <x-slot name="footerActions">
          <x-filament::link :href="$this->getEventsListUrl()" color="gray" icon="heroicon-m-calendar-days" icon-position="before" size="sm">
            View All Events
          </x-filament::link>
        </x-slot>
      </x-filament::section>
    </div>
  </x-filament-widgets::widget>
