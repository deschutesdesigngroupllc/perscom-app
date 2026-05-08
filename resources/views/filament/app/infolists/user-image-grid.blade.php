@props([
    'items',
    'emptyMessage' => 'Nothing to show yet.',
    'fallbackIcon' => 'heroicon-o-photo',
    'storageKey' => 'user-image-grid-view',
])

@php
  /** @var \Illuminate\Support\Collection $items */
@endphp

@if ($items->isEmpty())
  <p class="text-sm text-gray-500 dark:text-gray-400">{{ $emptyMessage }}</p>
@else
  <div x-data="{ view: $persist('list').as('{{ $storageKey }}') }" class="flex flex-col gap-3">

    <div x-show="view === 'grid'" x-cloak class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
      @foreach ($items as $item)
        <div
          class="group relative flex flex-col items-center gap-3 rounded-xl bg-white p-4 text-center shadow-sm ring-1 ring-gray-950/5 transition hover:-translate-y-0.5 hover:ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-white/20">
          <div
            class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-gray-50 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            @if ($item->image?->image_url)
              <img src="{{ $item->image->image_url }}" alt="{{ $item->name }}"
                class="h-full w-full object-contain p-2 transition group-hover:scale-105" />
            @else
              <x-filament::icon :icon="$fallbackIcon" class="h-10 w-10 text-gray-400 dark:text-gray-500" />
            @endif
          </div>
          <div class="flex flex-col gap-0.5">
            <p class="line-clamp-2 text-sm font-semibold text-gray-950 dark:text-white">
              {{ $item->name }}
            </p>
            @if (!empty($item->description))
              <p class="line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                {{ strip_tags($item->description) }}
              </p>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div x-show="view === 'list'" x-cloak
      class="flex flex-col divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
      @foreach ($items as $item)
        <div class="flex items-center gap-4 p-4">
          <div
            class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-50 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            @if ($item->image?->image_url)
              <img src="{{ $item->image->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-contain p-1" />
            @else
              <x-filament::icon :icon="$fallbackIcon" class="h-6 w-6 text-gray-400 dark:text-gray-500" />
            @endif
          </div>
          <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
              {{ $item->name }}
            </p>
            @if (!empty($item->description))
              <p class="line-clamp-2 text-xs text-gray-500 dark:text-gray-400">
                {{ strip_tags($item->description) }}
              </p>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <div class="flex justify-start pt-1">
      <div class="inline-flex items-center gap-1 rounded-lg bg-gray-50 p-1 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
        <button type="button" x-on:click="view = 'list'"
          :class="view === 'list' ?
              'bg-white text-gray-950 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:text-white dark:ring-white/10' :
              'text-gray-500 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white'"
          class="flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-medium transition">
          <x-filament::icon icon="heroicon-m-list-bullet" class="h-4 w-4" />
          List
        </button>
        <button type="button" x-on:click="view = 'grid'"
          :class="view === 'grid' ?
              'bg-white text-gray-950 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:text-white dark:ring-white/10' :
              'text-gray-500 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white'"
          class="flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-medium transition">
          <x-filament::icon icon="heroicon-m-squares-2x2" class="h-4 w-4" />
          Grid
        </button>
      </div>
    </div>
  </div>
@endif
