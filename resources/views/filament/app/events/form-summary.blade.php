@php
  $color = is_string($calendar?->color) && preg_match('/^#[0-9a-fA-F]{3,8}$/', $calendar->color) ? $calendar->color : '#2563eb';
  $multiDay = $starts && $ends && !$starts->isSameDay($ends);
@endphp

<div class="overflow-hidden rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" aria-live="polite">
  <div class="h-2" style="background-color: {{ $color }}" aria-hidden="true"></div>
  <div class="flex flex-col gap-4 p-4">
    <p class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400">Preview</p>

    <div class="flex items-start gap-3">
      @if ($starts)
        <div
          class="flex w-12 shrink-0 flex-col items-center overflow-hidden rounded-lg bg-white text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
          aria-hidden="true">
          <div class="w-full py-0.5 text-[10px] font-semibold tracking-wider text-white uppercase" style="background-color: {{ $color }}">
            {{ $starts->format('M') }}</div>
          <div class="text-lg/6 font-bold text-gray-950 tabular-nums dark:text-white">{{ $starts->format('j') }}</div>
          <div class="pb-1 text-[10px] font-medium text-gray-500 uppercase dark:text-gray-400">{{ $starts->format('D') }}</div>
        </div>
      @endif
      <div class="min-w-0">
        <p class="font-semibold break-words text-gray-950 dark:text-white">{{ filled($name) ? $name : 'Untitled event' }}</p>
        @if ($calendar)
          <p class="mt-0.5 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
            <span class="size-2 rounded-full" style="background-color: {{ $color }}" aria-hidden="true"></span>
            {{ $calendar->name }}
          </p>
        @endif
      </div>
    </div>

    <dl class="flex flex-col gap-2 text-sm">
      <div class="flex gap-2">
        <dt class="shrink-0"><x-filament::icon icon="heroicon-m-clock" class="size-4 text-gray-400" /><span class="sr-only">When</span></dt>
        <dd class="text-gray-700 dark:text-gray-300">
          @if (!$starts)
            Pick a start date
          @elseif ($allDay)
            {{ $starts->format('D, M j') }}{{ $multiDay ? ' – ' . $ends->format('D, M j') : '' }} &middot; All day
          @else
            {{ $starts->format('D, M j, g:i A') }}
            @if ($ends && $ends->greaterThan($starts))
              – {{ $multiDay ? $ends->format('D, M j, g:i A') : $ends->format('g:i A') }}
            @endif
          @endif
        </dd>
      </div>
      @if (filled($repeatRule))
        <div class="flex gap-2">
          <dt class="shrink-0"><x-filament::icon icon="heroicon-m-arrow-path" class="size-4 text-gray-400" /><span class="sr-only">Repeats</span></dt>
          <dd class="text-gray-700 dark:text-gray-300">{{ $repeatRule }}</dd>
        </div>
      @endif
      @if (filled($location))
        <div class="flex gap-2">
          <dt class="shrink-0"><x-filament::icon icon="heroicon-m-map-pin" class="size-4 text-gray-400" /><span class="sr-only">Where</span></dt>
          <dd class="break-words whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $location }}</dd>
        </div>
      @endif
    </dl>

    <div class="flex flex-wrap gap-1.5">
      <x-filament::badge :color="$registrationEnabled ? 'success' : 'gray'" :icon="$registrationEnabled ? 'heroicon-m-check' : 'heroicon-m-minus'" size="sm">
        RSVPs {{ $registrationEnabled ? 'on' : 'off' }}
      </x-filament::badge>
      <x-filament::badge :color="$remindersEnabled ? 'success' : 'gray'" :icon="$remindersEnabled ? 'heroicon-m-bell' : 'heroicon-m-bell-slash'" size="sm">
        Reminders {{ $remindersEnabled ? 'on' : 'off' }}
      </x-filament::badge>
    </div>
  </div>
</div>
