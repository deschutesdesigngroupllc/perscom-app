@php
  use App\Models\Enums\EventRegistrationStatus;

  $next = $this->nextOccurrence();
  $agenda = $this->agenda();
  $calendarUrl = $this->calendarUrl();
@endphp

<x-filament-widgets::widget>
  <x-filament::section icon="heroicon-o-calendar-days" icon-color="primary">
    <x-slot name="heading">Up Next</x-slot>
    <x-slot name="description">Your schedule for the next {{ $this::DAYS_AHEAD }} days</x-slot>

    @if ($next)
      @php
        $event = $next->event;
        $status = $this->statusFor($next);
        $color = $event->calendar?->color ?: '#2563eb';
        $responses = $this->responses($next);
        $going = $responses
            ->get(EventRegistrationStatus::GOING->value, collect())
            ->merge($responses->get(EventRegistrationStatus::ATTENDED->value, collect()));
        $summary = $this->summary($next);
        $location = $this->location($next);
        $externalUrl = $this->externalUrl($next);
        $image = $event->image?->image_url;
      @endphp

      <div class="relative overflow-hidden rounded-xl bg-white ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10"
        wire:key="upcoming-hero-{{ $next->key() }}">
        <div class="absolute inset-y-0 start-0 w-1.5" style="background-color: {{ $color }}" aria-hidden="true"></div>

        @if ($image)
          <div class="pointer-events-none absolute inset-y-0 end-0 hidden w-2/5 md:block" aria-hidden="true">
            <img src="{{ $image }}" alt="" class="size-full object-cover opacity-40 dark:opacity-30" loading="lazy" />
            <div class="absolute inset-0 bg-linear-to-r from-white via-white/70 to-transparent dark:from-gray-900 dark:via-gray-900/70">
            </div>
          </div>
        @endif

        <div class="relative flex flex-col gap-4 p-4 ps-6 sm:flex-row sm:items-start sm:gap-5 sm:p-5 sm:ps-7">
          <x-events.date-badge :occurrence="$next" size="lg" />

          <div class="flex min-w-0 flex-1 flex-col gap-3">
            <div class="flex flex-col gap-1">
              <p @class([
                  'inline-flex items-center gap-1.5 text-xs font-semibold tracking-wide uppercase',
                  'text-success-600 dark:text-success-400' => $next->isHappeningNow(),
                  'text-primary-600 dark:text-primary-400' => !$next->isHappeningNow(),
              ])>
                @if ($next->isHappeningNow())
                  <span class="size-2 animate-pulse rounded-full bg-success-500"></span>
                @endif
                {{ $this->countdown($next) }}
              </p>

              <a href="{{ $next->url() }}"
                class="text-lg/7 font-semibold text-gray-950 hover:text-primary-600 sm:text-xl/8 dark:text-white dark:hover:text-primary-400">
                {{ $event->name }}
              </a>

              <div class="flex flex-col gap-1 text-sm text-gray-600 dark:text-gray-300">
                <p class="flex items-center gap-1.5">
                  <x-filament::icon icon="heroicon-m-clock" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
                  <span>{{ $this->timeRange($next) }}</span>
                </p>
                @if ($location)
                  <p class="flex min-w-0 items-center gap-1.5">
                    <x-filament::icon icon="heroicon-m-map-pin" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
                    <span class="truncate">{{ $location }}</span>
                  </p>
                @endif
                @if ($externalUrl)
                  <p class="flex min-w-0 items-center gap-1.5">
                    <x-filament::icon icon="heroicon-m-link" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
                    <a href="{{ $externalUrl }}" target="_blank" rel="noopener"
                      class="truncate text-primary-600 hover:underline dark:text-primary-400">{{ $externalUrl }}</a>
                  </p>
                @endif
              </div>
            </div>

            @if ($summary['going'] > 0 || $summary['maybe'] > 0)
              <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <x-events.attendee-stack :registrations="$going" />
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  <span class="font-semibold text-gray-950 dark:text-white">{{ $summary['going'] }}</span> going
                  @if ($summary['maybe'] > 0)
                    · <span class="font-semibold text-gray-950 dark:text-white">{{ $summary['maybe'] }}</span> maybe
                  @endif
                </p>
              </div>
            @endif

            <div class="flex flex-wrap items-center gap-2">
              @if ($this->canCheckIn($next))
                <x-filament::button tag="a" :href="$this->checkInUrl($next)" icon="heroicon-m-check-circle">
                  Check In
                </x-filament::button>
              @endif
              <x-events.rsvp-buttons :occurrence="$next" :status="$status" size="md" />
            </div>
          </div>
        </div>
      </div>

      @if ($agenda->isNotEmpty())
        <div class="mt-6 flex flex-col gap-4">
          @foreach ($agenda as $heading => $occurrences)
            <div class="flex flex-col gap-1" wire:key="upcoming-day-{{ Str::slug($heading) }}">
              <h3 class="px-2 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">{{ $heading }}</h3>
              <div class="flex flex-col gap-1">
                @foreach ($occurrences as $occurrence)
                  <x-events.occurrence-row :occurrence="$occurrence" :status="$this->statusFor($occurrence)" :needs-response="$this->needsResponse($occurrence)"
                    wire:key="upcoming-row-{{ $occurrence->key() }}" />
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      @endif
    @else
      <div class="flex flex-col items-center px-4 py-8 text-center">
        <div
          class="flex size-12 items-center justify-center rounded-full bg-gray-50 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
          <x-filament::icon icon="heroicon-o-calendar" class="size-6 text-gray-400 dark:text-gray-500" />
        </div>
        <p class="mt-3 text-sm font-semibold text-gray-950 dark:text-white">Nothing scheduled this week</p>
        <p class="mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">
          Enjoy the downtime. Browse the calendar for what's coming later, or subscribe so new events show up in your calendar app.
        </p>
      </div>
    @endif

    <x-slot name="footer">
      <div class="flex flex-wrap items-center justify-between gap-2">
        @if ($calendarUrl)
          <x-filament::link :href="$calendarUrl" color="gray" icon="heroicon-m-calendar-days" icon-position="before" size="sm">
            Open Calendar
          </x-filament::link>
        @endif
        {{ $this->subscribeAction }}
      </div>
    </x-slot>
  </x-filament::section>

  <x-filament-actions::modals />
</x-filament-widgets::widget>
