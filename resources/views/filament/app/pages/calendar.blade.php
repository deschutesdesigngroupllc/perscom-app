@php
  use App\Filament\App\Pages\Calendar;
  use App\Filament\App\Widgets\CalendarWidget;
  use Filament\Support\Colors\Color;

  $layouts = [
      'agenda' => ['Agenda', 'heroicon-m-list-bullet'],
      'month' => ['Month', 'heroicon-m-calendar-days'],
      'week' => ['Week', 'heroicon-m-view-columns'],
  ];
@endphp

<x-filament-panels::page>
  <div class="flex min-w-0 flex-col gap-4" x-data x-init="if (!@js($hasDisplayPreference) && @js($display) !== 'agenda' && window.matchMedia('(max-width: 639px)').matches) { $wire.setDisplay('agenda', false) }">
    <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="flex min-w-0 flex-1 gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0" role="group"
        aria-label="Filter events">
        @foreach ($calendars as $calendar)
          @php
            $active = in_array($calendar->getKey(), $calendarIds, true);
            $color = $calendar->color ?: CalendarWidget::DEFAULT_COLOR;
          @endphp
          <x-filament::button wire:key="calendar-chip-{{ $calendar->getKey() }}" wire:click="toggleCalendar({{ $calendar->getKey() }})"
            aria-pressed="{{ $active ? 'true' : 'false' }}" :color="$active ? Color::generateV3Palette($color) : 'gray'" size="sm" class="shrink-0 whitespace-nowrap">
            <span class="inline-flex items-center gap-1.5">
              <span class="size-2.5 shrink-0 rounded-full" style="background-color: {{ $color }}" aria-hidden="true"></span>
              {{ $calendar->name }}
            </span>
          </x-filament::button>
        @endforeach

        @foreach (['mine' => ['My Events', 'heroicon-m-user', 'toggleMine'], 'openOnly' => ['Open for Registration', 'heroicon-m-user-plus', 'toggleOpenOnly']] as $property => [$label, $icon, $method])
          @php($active = $this->{$property})
          <x-filament::button wire:click="{{ $method }}" aria-pressed="{{ $active ? 'true' : 'false' }}" :color="$active ? 'primary' : 'gray'"
            :icon="$icon" size="sm" class="shrink-0 whitespace-nowrap">
            {{ $label }}
          </x-filament::button>
        @endforeach

        @if ($this->isFiltering())
          <x-filament::link tag="button" wire:click="clearFilters" color="gray" size="sm" icon="heroicon-m-x-mark"
            class="shrink-0">
            Clear
          </x-filament::link>
        @endif
      </div>

      <x-filament::tabs label="Calendar layout" class="mx-0 shrink-0 self-start">
        @foreach ($layouts as $key => [$label, $icon])
          <x-filament::tabs.item wire:click="setDisplay('{{ $key }}')" :active="$display === $key" :icon="$icon">
            {{ $label }}
          </x-filament::tabs.item>
        @endforeach
      </x-filament::tabs>
    </div>

    @if ($display === 'agenda')
      <div wire:loading.class="opacity-60" wire:target="toggleCalendar,toggleMine,toggleOpenOnly,togglePast,clearFilters,loadMore"
        class="flex min-w-0 flex-col gap-4 transition-opacity">
        <div class="flex items-center justify-between gap-3">
          <h2 class="text-sm font-semibold text-gray-950 dark:text-white">{{ $past ? 'Past Events' : 'Upcoming Events' }}</h2>
          <x-filament::link tag="button" wire:click="togglePast" color="gray" size="sm" :icon="$past ? 'heroicon-m-arrow-right' : 'heroicon-m-clock'">
            {{ $past ? 'Show upcoming' : 'Show past 30 days' }}
          </x-filament::link>
        </div>

        @forelse ($days as $date => $occurrences)
          <x-filament::section wire:key="agenda-day-{{ $date }}" :heading="Calendar::dayHeading($date)" heading-tag="h3" compact class="min-w-0">
            <div class="-m-2 flex flex-col divide-y divide-gray-950/5 dark:divide-white/5">
              @foreach ($occurrences as $occurrence)
                <x-events.occurrence-row wire:key="agenda-occurrence-{{ $occurrence->key() }}" :occurrence="$occurrence" :status="$responses->get($occurrence->key())" />
              @endforeach
            </div>
          </x-filament::section>
        @empty
          <x-filament::empty-state icon="heroicon-o-calendar" :heading="$past ? 'No past events' : 'No upcoming events'" :description="$this->isFiltering()
              ? 'No events match the current filters in this period.'
              : ($past
                  ? 'There were no events in this period.'
                  : 'There are no upcoming events in this period.')" />
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
            key('calendar-widget-' . $display . '-' . md5(json_encode([$calendarIds, $mine, $openOnly])))
        )
      </div>
    @endif
  </div>
</x-filament-panels::page>
