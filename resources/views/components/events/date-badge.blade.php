@props(['occurrence', 'size' => 'md'])

@php
  $starts = $occurrence->localStarts();
  $color = $occurrence->event->calendar?->color ?: '#2563eb';
@endphp

<div
  {{ $attributes->class([
      'flex shrink-0 flex-col items-center overflow-hidden rounded-lg bg-white text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
      'w-12' => $size === 'md',
      'w-16' => $size === 'lg',
      'opacity-60' => $occurrence->isCancelled(),
  ]) }}
  aria-label="{{ $starts->format('l, F j') }}">
  <div class="w-full py-0.5 text-[10px] font-semibold tracking-wider text-white uppercase" style="background-color: {{ $color }}">
    {{ $starts->format('M') }}
  </div>
  <div @class(['font-bold text-gray-950 tabular-nums dark:text-white', 'text-lg/6' => $size === 'md', 'text-2xl/8' => $size === 'lg'])>
    {{ $starts->format('j') }}
  </div>
  <div class="pb-1 text-[10px] font-medium text-gray-500 uppercase dark:text-gray-400">{{ $starts->format('D') }}</div>
</div>
