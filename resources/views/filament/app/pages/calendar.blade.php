@php
  use App\Filament\App\Pages\Calendar;
  use App\Filament\App\Widgets\CalendarWidget;

  $layouts = [
      'agenda' => ['Agenda', 'heroicon-m-list-bullet'],
      'month' => ['Month', 'heroicon-m-calendar-days'],
      'week' => ['Week', 'heroicon-m-view-columns'],
  ];
  $chipClasses = 'inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium whitespace-nowrap ring-1 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600';
@endphp

<x-filament-panels::page>
  <div class="flex min-w-0 flex-col gap-4" x-data
    x-init="if (! @js($hasDisplayPreference) && @js($display) !== 'agenda' && window.matchMedia('(max-width: 639px)').matches) { $wire.setDisplay('agenda', false) }">
    <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="flex min-w-0 flex-1 gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0" role="group" aria-label="Filter events">
        @foreach ($calendars as $calendar)
          @php
            $active = in_array($calendar->getKey(), $calendarIds, true);
            $color = $calendar->color ?: CalendarWidget::DEFAULT_COLOR;
          @endphp
          <button type="button" wire:key="calendar-chip-{{ $calendar->getKey() }}" wire:click="toggleCalendar({{ $calendar->getKey() }})"
            aria-pressed="{{ $active ? 'true' : 'false' }}" @class([
                $chipClasses,
                'text-gray-950 dark:text-white' => $active,
                'bg-white text-gray-700 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/20 dark:hover:bg-white/10' => !$active,
            ])
            @if ($active) style="background-color: color-mix(in srgb, {{ $color }} 16%, transparent); --tw-ring-color: {{ $color }};" @endif>
            <span class="size-2.5 shrink-0 rounded-full" style="background-color: {{ $color }}"></span>
            {{ $calendar->name }}
          </button>
        @endforeach

        @foreach (['mine' => ['My Events', 'heroicon-m-user', 'toggleMine'], 'openOnly' => ['Open for Registration', 'heroicon-m-user-plus', 'toggleOpenOnly']] as $property => [$label, $icon, $method])
          @php($active = $this->{$property})
          <button type="button" wire:click="{{ $method }}" aria-pressed="{{ $active ? 'true' : 'false' }}" @class([
              $chipClasses,
              'bg-primary-600 text-white ring-primary-600' => $active,
              'bg-white text-gray-700 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/20 dark:hover:bg-white/10' => !$active,
          ])>
            <x-filament::icon :icon="$icon" class="size-4" />
            {{ $label }}
          </button>
        @endforeach

        @if ($this->isFiltering())
          <button type="button" wire:click="clearFilters"
            class="inline-flex shrink-0 items-center px-2 py-1 text-sm font-medium whitespace-nowrap text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            Clear
          </button>
        @endif
      </div>

      <div class="flex shrink-0 items-center gap-1 self-start rounded-lg bg-gray-100 p-1 dark:bg-white/5" role="group" aria-label="Calendar layout">
        @foreach ($layouts as $key => [$label, $icon])
          <button type="button" wire:click="setDisplay('{{ $key }}')" @class([
              'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition',
              'bg-white text-gray-950 shadow-xs dark:bg-gray-800 dark:text-white' => $display === $key,
              'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' => $display !== $key,
          ]) aria-pressed="{{ $display === $key ? 'true' : 'false' }}">
            <x-filament::icon :icon="$icon" class="size-4" />
            {{ $label }}
          </button>
        @endforeach
      </div>
    </div>

    @if ($display === 'agenda')
      <div wire:loading.class="opacity-60" wire:target="toggleCalendar,toggleMine,toggleOpenOnly,togglePast,clearFilters,loadMore"
        class="flex min-w-0 flex-col gap-4 transition-opacity">
        <div class="flex items-center justify-between gap-3">
          <h2 class="text-sm font-semibold text-gray-950 dark:text-white">{{ $past ? 'Past Events' : 'Upcoming Events' }}</h2>
          <x-filament::link tag="button" wire:click="togglePast" color="gray" size="sm"
            :icon="$past ? 'heroicon-m-arrow-right' : 'heroicon-m-clock'">
            {{ $past ? 'Show upcoming' : 'Show past 30 days' }}
          </x-filament::link>
        </div>

        @forelse ($days as $date => $occurrences)
          <section wire:key="agenda-day-{{ $date }}" class="min-w-0 overflow-hidden rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="border-b border-gray-950/5 px-4 py-2 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:border-white/10 dark:text-gray-400">
              {{ Calendar::dayHeading($date) }}
            </h3>
            <div class="flex flex-col divide-y divide-gray-950/5 p-2 dark:divide-white/5">
              @foreach ($occurrences as $occurrence)
                <x-events.occurrence-row wire:key="agenda-occurrence-{{ $occurrence->key() }}" :occurrence="$occurrence"
                  :status="$responses->get($occurrence->key())" />
              @endforeach
            </div>
          </section>
        @empty
          <div class="rounded-xl bg-white p-6 text-center text-sm text-gray-500 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:text-gray-400 dark:ring-white/10">
            @if ($this->isFiltering())
              No events match the current filters in this period.
            @else
              {{ $past ? 'There were no events in this period.' : 'There are no upcoming events in this period.' }}
            @endif
          </div>
        @endforelse

        <div class="flex justify-center">
          <x-filament::button color="gray" wire:click="loadMore" wire:target="loadMore" :icon="$past ? 'heroicon-m-arrow-up' : 'heroicon-m-arrow-down'">
            {{ $past ? 'Load earlier' : 'Load more' }}
          </x-filament::button>
        </div>
      </div>
    @else
      <div class="min-w-0">
        @livewire(
            CalendarWidget::class,
            [
                'display' => $display,
                'calendarIds' => $calendarIds,
                'mine' => $mine,
                'openOnly' => $openOnly,
                'onCalendarPage' => true,
            ],
            key('calendar-widget-' . $display . '-' . md5(json_encode([$calendarIds, $mine, $openOnly]))),
        )
      </div>
    @endif
  </div>
</x-filament-panels::page>
