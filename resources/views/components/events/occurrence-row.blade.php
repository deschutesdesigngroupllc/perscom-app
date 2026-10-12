@props(['occurrence', 'status' => null, 'showRsvp' => true, 'needsResponse' => false])

@php
  $event = $occurrence->event;
  $starts = $occurrence->localStarts();
  $ends = $occurrence->localEnds();
  $time = $occurrence->allDay ? 'All day' : $starts->format('g:i A') . ($ends->greaterThan($starts) ? ' – ' . $ends->format('g:i A') : '');
@endphp

<div
  {{ $attributes->class('flex flex-col gap-3 rounded-lg p-2 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center dark:hover:bg-white/5') }}>
  <a href="{{ $occurrence->url() }}" class="group flex min-w-0 flex-1 items-center gap-3">
    <x-events.date-badge :occurrence="$occurrence" />

    <div class="min-w-0 flex-1">
      <p class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400">
        <span>{{ $time }}</span>
        @if ($occurrence->isHappeningNow() && !$occurrence->isCancelled())
          <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
            <span class="size-1.5 animate-pulse rounded-full bg-success-500"></span> Happening now
          </span>
        @endif
        @if ($needsResponse && !$occurrence->isCancelled())
          <span
            class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-1.5 py-0.5 text-[10px] font-semibold text-primary-700 ring-1 ring-primary-600/20 ring-inset dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
            <span class="size-1.5 rounded-full bg-primary-500"></span> Respond
          </span>
        @endif
      </p>
      <p @class([
          'truncate font-semibold text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400',
          'line-through decoration-gray-400' => $occurrence->isCancelled(),
      ])>{{ $event->name }}</p>
      @if ($occurrence->isCancelled())
        <p class="text-xs font-medium text-danger-600 dark:text-danger-400">Cancelled</p>
      @elseif (filled($event->location))
        <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::of($event->location)->squish() }}</p>
      @endif
    </div>
  </a>

  @if ($showRsvp && !$occurrence->isCancelled())
    <x-events.rsvp-buttons :occurrence="$occurrence" :status="$status" class="shrink-0 self-start sm:self-center" />
  @endif
</div>
