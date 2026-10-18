@php
  use Filament\Support\Colors\Color;

  $event = $this->getEvent();
  $occurrence = $this->getSelectedOccurrence();
  $color = $this->getCalendarColor();
  $image = $event->image?->image_url;
  $category = $event->categories->first();
  $repeatRule = $this->getRepeatRule();
  $status = $this->getViewerResponse();
  $closedMessage = $this->getResponseClosedMessage();
  $pickerOccurrences = $this->getPickerOccurrences();
  $localStarts = $occurrence?->localStarts();
  $wholeSeries = $this->wholeSeries;
@endphp

<div class="flex flex-col gap-4" x-data="{ showFloatingBar: false }">
  <section aria-labelledby="event-title"
    class="overflow-hidden rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="relative h-36 sm:h-52" aria-hidden="true">
      @if (filled($image))
        <img src="{{ $image }}" alt="" class="absolute inset-0 size-full object-cover" />
      @else
        <div class="absolute inset-0"
          style="background-color: {{ $color }}; background-image: radial-gradient(rgb(255 255 255 / 0.22) 1px, transparent 1.5px), linear-gradient(135deg, rgb(255 255 255 / 0.18), rgb(0 0 0 / 0.28)); background-size: 18px 18px, 100% 100%;">
        </div>
      @endif
      <div class="absolute inset-0 bg-linear-to-t from-black/30 to-transparent"></div>
    </div>

    <div class="flex flex-col gap-4 px-4 pb-5 sm:flex-row sm:items-start sm:gap-5 sm:px-6">
      @if ($occurrence)
        <x-events.date-badge :occurrence="$occurrence" size="lg"
          class="relative z-10 -mt-10 shadow-md ring-4! ring-white! dark:ring-gray-900!" />
      @endif

      <div class="flex min-w-0 flex-1 flex-col gap-2 sm:pt-3">
        <div class="flex flex-wrap items-center gap-2">
          @if ($occurrence?->isCancelled())
            <x-filament::badge color="danger" icon="heroicon-m-x-circle">Cancelled</x-filament::badge>
          @elseif ($occurrence?->isHappeningNow())
            <x-filament::badge color="success" icon="heroicon-m-signal">Happening now</x-filament::badge>
          @elseif ($occurrence?->hasEnded())
            <x-filament::badge color="gray" icon="heroicon-m-check-circle">Ended</x-filament::badge>
          @elseif ($occurrence)
            <x-filament::badge color="info" icon="heroicon-m-clock">Starts {{ $occurrence->starts->diffForHumans() }}</x-filament::badge>
          @endif

          @if ($event->calendar)
            <x-filament::badge :color="Color::generateV3Palette($color)" icon="heroicon-m-calendar">
              <span class="sr-only">Calendar:</span> {{ $event->calendar->name }}
            </x-filament::badge>
          @endif

          @if ($category)
            <x-filament::badge color="gray" icon="heroicon-m-tag">
              <span class="sr-only">Category:</span> {{ $category->name }}
            </x-filament::badge>
          @endif
        </div>

        <h1 id="event-title" @class([
            'text-2xl font-bold tracking-tight text-gray-950 sm:text-3xl dark:text-white',
            'line-through decoration-gray-400' => $occurrence?->isCancelled(),
        ])>
          {{ $event->name }}
        </h1>

        @if ($occurrence)
          <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-medium text-gray-700 dark:text-gray-300">
            <x-filament::icon icon="heroicon-m-calendar" class="size-4 text-gray-400" />
            <span>{{ $localStarts->format('l, F j, Y') }}</span>
            <span aria-hidden="true" class="text-gray-300 dark:text-gray-600">&middot;</span>
            <span>{{ $this->formatTimeRange($occurrence) }}</span>
          </p>
        @endif

        @if (filled($repeatRule))
          <p class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <x-filament::icon icon="heroicon-m-arrow-path" class="size-4 shrink-0 text-gray-400" />
            <span>{{ $repeatRule }}</span>
          </p>
        @endif
      </div>
    </div>
  </section>

  @if ($occurrence)
    <x-filament::section aria-label="Your RSVP" x-intersect:enter="showFloatingBar = false"
      x-intersect:leave="showFloatingBar = $el.getBoundingClientRect().top < 0" compact>
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-950 dark:text-white">
            @if ($status)
              You responded {{ $status->getMemberLabel() }}
            @elseif ($closedMessage)
              RSVP
            @else
              Are you going?
            @endif
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400">{{ $localStarts->format('l, F j') }} &middot;
            {{ $this->formatTimeRange($occurrence) }}</p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
          @if (blank($closedMessage))
            @if ($event->repeats)
              <x-filament::button.group class="self-start sm:self-auto" role="group" aria-label="Apply my response to">
                @foreach ([false => 'This date', true => 'All dates'] as $scope => $label)
                  <x-filament::button :color="$wholeSeries === (bool) $scope ? 'primary' : 'gray'" size="sm" wire:click="$set('wholeSeries', {{ $scope ? 'true' : 'false' }})"
                    aria-pressed="{{ $wholeSeries === (bool) $scope ? 'true' : 'false' }}">
                    {{ $label }}
                  </x-filament::button>
                @endforeach
              </x-filament::button.group>
            @endif

            <x-events.rsvp-buttons :occurrence="$occurrence" :status="$status" size="md" :whole-series="$wholeSeries" class="self-start sm:self-auto" />
          @else
            <p class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
              <x-filament::icon :icon="$occurrence->isCancelled() ? 'heroicon-m-x-circle' : 'heroicon-m-information-circle'" @class([
                  'size-5 shrink-0',
                  'text-danger-500' => $occurrence->isCancelled(),
                  'text-gray-400' => !$occurrence->isCancelled(),
              ]) />
              <span>{{ $closedMessage }}</span>
            </p>
            @if ($occurrence->isCancelled() && filled($occurrence->occurrence?->notes))
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $occurrence->occurrence->notes }}</p>
            @endif
          @endif
        </div>
      </div>
    </x-filament::section>

    @if (blank($closedMessage))
      <div x-show="showFloatingBar" x-cloak x-transition.opacity
        class="fixed inset-x-4 bottom-4 z-30 mx-auto flex max-w-xl flex-col gap-2 rounded-xl bg-white/95 p-3 shadow-lg ring-1 ring-gray-950/10 backdrop-blur sm:flex-row sm:items-center sm:justify-between dark:bg-gray-900/95 dark:ring-white/10">
        <div class="min-w-0">
          <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">{{ $event->name }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ $localStarts->format('D, M j') }}@if ($event->repeats)
              &middot; {{ $wholeSeries ? 'All dates' : 'This date' }}
            @endif
          </p>
        </div>
        <x-events.rsvp-buttons :occurrence="$occurrence" :status="$status" :whole-series="$wholeSeries" class="self-start sm:self-auto" />
      </div>
    @endif
  @endif

  @if ($pickerOccurrences->count() > 1)
    <nav aria-label="Event dates">
      <ol class="flex snap-x gap-2 overflow-x-auto pb-2">
        @foreach ($pickerOccurrences as $date)
          @php
            $isSelected = $occurrence && $date->starts->equalTo($occurrence->starts);
            $dateStarts = $date->localStarts();
            $description = collect([
                $dateStarts->format('l, F j, Y'),
                $date->isCancelled() ? 'cancelled' : null,
                $date->hasEnded() ? 'past' : null,
            ])
                ->filter()
                ->implode(', ');
          @endphp
          <li class="shrink-0 snap-start">
            <a href="{{ $date->url() }}" wire:click.prevent="$set('occurrence', {{ $date->starts->getTimestamp() }})"
              aria-label="{{ $description }}" @if ($isSelected) aria-current="date" @endif @class([
                  'flex w-16 flex-col items-center rounded-lg px-2 py-2 text-center transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600',
                  'bg-primary-600 text-white ring-2 ring-primary-600 ring-offset-2 ring-offset-white dark:ring-offset-gray-950' => $isSelected,
                  'bg-white text-gray-700 ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-white/10' => !$isSelected,
                  'opacity-60' => $date->hasEnded() && !$isSelected,
              ])>
              <span class="text-[11px] font-medium uppercase">{{ $dateStarts->format('D') }}</span>
              <span @class([
                  'text-lg/6 font-bold tabular-nums',
                  'line-through' => $date->isCancelled(),
              ])>{{ $dateStarts->format('j') }}</span>
              <span class="text-[11px] uppercase">{{ $dateStarts->format('M') }}</span>
              @if ($date->isCancelled())
                <x-filament::icon icon="heroicon-m-x-mark" class="mt-0.5 size-3.5" />
              @endif
            </a>
          </li>
        @endforeach
      </ol>
    </nav>
  @endif
</div>
