<x-filament-widgets::widget>
  <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
      <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-transparent"></div>
      <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-amber-500/10 blur-2xl"></div>

      <div class="relative">
        <div class="flex items-center gap-x-3 mb-5">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 shadow-lg shadow-amber-500/25">
            <x-heroicon-s-chevron-double-up class="h-5 w-5 text-white" />
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Current Rank</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Your current rank</p>
          </div>
        </div>

        <div class="flex items-center gap-x-4">
          @if ($this->getRankImageUrl())
            <div
              class="flex-shrink-0 rounded-lg bg-gradient-to-br from-amber-100 to-orange-50 p-2 dark:from-amber-900/30 dark:to-orange-900/20">
              <img src="{{ $this->getRankImageUrl() }}" alt="{{ $this->getRank()?->name }}" class="h-14 w-14 object-contain" />
            </div>
          @else
            <div
              class="flex h-[72px] w-[72px] flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-100 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/20">
              <x-heroicon-o-chevron-double-up class="h-8 w-8 text-amber-500" />
            </div>
          @endif

          <div class="min-w-0 flex-1">
            @if ($this->getRank())
              <p class="truncate text-xl font-bold text-gray-900 dark:text-white">
                {{ $this->getRank()->name }}
              </p>
              <div class="mt-1 flex flex-wrap items-center gap-2">
                @if ($this->getRank()->abbreviation)
                  <span
                    class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                    {{ $this->getRank()->abbreviation }}
                  </span>
                @endif
                @if ($this->getRank()->paygrade)
                  <span
                    class="inline-flex items-center rounded-md bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-500/20">
                    {{ $this->getRank()->paygrade }}
                  </span>
                @endif
              </div>
            @else
              <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No rank assigned</p>
              <p class="text-sm text-gray-400 dark:text-gray-500">Contact your administrator</p>
            @endif
          </div>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
          <a href="{{ $this->getRankRecordsUrl() }}"
            class="inline-flex items-center gap-x-2 text-sm font-semibold text-amber-600 transition hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300">
            <x-heroicon-m-clipboard-document-list class="h-4 w-4" />
            View Rank Records
            <x-heroicon-m-arrow-right class="h-4 w-4" />
          </a>
        </div>
      </div>
    </div>

    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
      <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent"></div>
      <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl"></div>

      <div class="relative">
        <div class="flex items-center gap-x-3 mb-5">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 shadow-lg shadow-blue-500/25">
            <x-heroicon-s-identification class="h-5 w-5 text-white" />
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Current Assignment</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Your position & unit</p>
          </div>
        </div>

        <div class="flex items-center gap-x-4">
          @if ($this->getUnitImageUrl())
            <div
              class="flex-shrink-0 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-50 p-2 dark:from-blue-900/30 dark:to-indigo-900/20">
              <img src="{{ $this->getUnitImageUrl() }}" alt="{{ $this->getUnit()?->name }}" class="h-14 w-14 object-contain" />
            </div>
          @else
            <div
              class="flex h-[72px] w-[72px] flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/20">
              <x-heroicon-o-identification class="h-8 w-8 text-blue-500" />
            </div>
          @endif

          <div class="min-w-0 flex-1">
            @if ($this->getPosition() || $this->getUnit())
              @if ($this->getPosition())
                <p class="truncate text-xl font-bold text-gray-900 dark:text-white">
                  {{ $this->getPosition()->name }}
                </p>
              @endif
              @if ($this->getUnit())
                <p class="mt-0.5 truncate text-sm text-gray-500 dark:text-gray-400">
                  {{ $this->getUnit()->name }}
                </p>
              @endif
            @else
              <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No current assignment</p>
              <p class="text-sm text-gray-400 dark:text-gray-500">Contact your administrator</p>
            @endif
          </div>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
          <a href="{{ $this->getAssignmentRecordsUrl() }}"
            class="inline-flex items-center gap-x-2 text-sm font-semibold text-blue-600 transition hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            <x-heroicon-m-clipboard-document-list class="h-4 w-4" />
            View Assignment Records
            <x-heroicon-m-arrow-right class="h-4 w-4" />
          </a>
        </div>
      </div>
    </div>

    <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
      <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 via-teal-500/5 to-transparent"></div>
      <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-emerald-500/10 blur-2xl"></div>

      <div class="relative">
        <div class="flex items-center gap-x-3 mb-5">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 shadow-lg shadow-emerald-500/25">
            <x-heroicon-s-calendar-days class="h-5 w-5 text-white" />
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Upcoming Events</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Next scheduled events</p>
          </div>
        </div>

        <div class="space-y-3">
          @forelse ($this->getUpcomingEvents() as $event)
            <a href="{{ $this->getEventUrl($event) }}"
              class="group flex items-center gap-x-3 rounded-lg p-2 -mx-2 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
              @if ($event->image?->image_url)
                <img src="{{ $event->image->image_url }}" alt="{{ $event->name }}"
                  class="h-10 w-10 flex-shrink-0 rounded-lg object-cover ring-1 ring-gray-200 dark:ring-gray-700" />
              @else
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                  <x-heroicon-o-calendar class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                </div>
              @endif

              <div class="min-w-0 flex-1">
                <p
                  class="truncate text-sm font-semibold text-gray-900 group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
                  {{ $event->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  {{ $event->starts->format('M j, Y') }}
                  @if (!$event->all_day)
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $event->starts->format('g:i A') }}</span>
                  @endif
                </p>
              </div>

              <x-heroicon-m-chevron-right
                class="h-5 w-5 flex-shrink-0 text-gray-300 transition group-hover:text-emerald-500 dark:text-gray-600" />
            </a>
          @empty
            <div class="py-6 text-center">
              <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <x-heroicon-o-calendar class="h-6 w-6 text-gray-400" />
              </div>
              <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">No upcoming events</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">Check back later</p>
            </div>
          @endforelse
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
          <a href="{{ $this->getEventsListUrl() }}"
            class="inline-flex items-center gap-x-2 text-sm font-semibold text-emerald-600 transition hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">
            <x-heroicon-m-calendar-days class="h-4 w-4" />
            View All Events
            <x-heroicon-m-arrow-right class="h-4 w-4" />
          </a>
        </div>
      </div>
    </div>
  </div>
</x-filament-widgets::widget>
