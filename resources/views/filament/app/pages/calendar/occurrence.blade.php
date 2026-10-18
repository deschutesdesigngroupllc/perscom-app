@php
  use App\Filament\App\Widgets\CalendarWidget;
  use Illuminate\Support\Str;

  $event = $occurrence->event;
  $color = $event->calendar?->color ?: CalendarWidget::DEFAULT_COLOR;
@endphp

<div class="flex flex-col gap-4 text-sm">
  @if ($occurrence->isCancelled())
    <x-filament::callout color="danger" icon="heroicon-o-x-circle" heading="This date has been cancelled." :description="$occurrence->occurrence?->notes" />
  @endif

  <dl class="flex flex-col gap-2 text-gray-700 dark:text-gray-300">
    @if ($event->calendar)
      <div class="flex items-center gap-2">
        <dt class="sr-only">Calendar</dt>
        <span class="size-2.5 shrink-0 rounded-full" style="background-color: {{ $color }}"></span>
        <dd>{{ $event->calendar->name }}</dd>
      </div>
    @endif
    @if (filled($event->location))
      <div class="flex items-start gap-2">
        <dt class="sr-only">Location</dt>
        <x-filament::icon icon="heroicon-m-map-pin" class="mt-0.5 size-4 shrink-0 text-gray-400" />
        <dd class="min-w-0 break-words">{{ Str::of($event->location)->squish() }}</dd>
      </div>
    @endif
    @if ($event->repeats)
      <div class="flex items-center gap-2">
        <dt class="sr-only">Repeats</dt>
        <x-filament::icon icon="heroicon-m-arrow-path" class="size-4 shrink-0 text-gray-400" />
        <dd>Part of a repeating series</dd>
      </div>
    @endif
  </dl>

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
      <span><span class="font-semibold text-gray-950 dark:text-white">{{ $summary['going'] }}</span> going</span>
      <span><span class="font-semibold text-gray-950 dark:text-white">{{ $summary['maybe'] }}</span> maybe</span>
      <span><span class="font-semibold text-gray-950 dark:text-white">{{ $summary['not_going'] }}</span> can't go</span>
    </div>
    <x-events.attendee-stack :registrations="$going" />
  </div>

  @if (!$occurrence->isCancelled())
    <x-events.rsvp-buttons :occurrence="$occurrence" :status="$status" size="md" />
  @endif
</div>
