@props(['items', 'emptyMessage' => 'No credentials have been assigned yet.', 'storageKey' => 'user-credential-view'])

@use('App\Models\Enums\CredentialType')

@php
  /** @var \Illuminate\Support\Collection $items */
  $iconFor = fn(?CredentialType $type): string => match ($type) {
      CredentialType::Certification => 'heroicon-o-academic-cap',
      CredentialType::License => 'heroicon-o-identification',
      default => 'heroicon-o-document-check',
  };
  $colorFor = fn(?CredentialType $type): string => match ($type) {
      CredentialType::Certification => 'warning',
      CredentialType::License => 'info',
      default => 'gray',
  };
@endphp

@if ($items->isEmpty())
  <p class="text-sm text-gray-500 dark:text-gray-400">{{ $emptyMessage }}</p>
@else
  <div x-data="{ view: $persist('list').as('{{ $storageKey }}') }" class="flex flex-col gap-3">

    <div x-show="view === 'grid'" x-cloak class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($items as $item)
        <div
          class="group flex items-start gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition hover:-translate-y-0.5 hover:ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-white/20">
          <div
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <x-filament::icon :icon="$iconFor($item->type ?? null)" class="h-6 w-6 text-primary-600 dark:text-primary-400" />
          </div>

          <div class="flex min-w-0 flex-1 flex-col gap-1">
            <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
              {{ $item->name }}
            </p>

            <div class="flex flex-wrap items-center gap-1.5">
              @if ($item->type)
                <x-filament::badge :color="$colorFor($item->type)" size="sm" :icon="$iconFor($item->type)">
                  {{ $item->type->getLabel() }}
                </x-filament::badge>
              @endif
              @if ($item->issuer)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ $item->issuer->name }}
                </span>
              @endif
            </div>

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
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <x-filament::icon :icon="$iconFor($item->type ?? null)" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
          </div>
          <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
              {{ $item->name }}
            </p>
            <div class="flex flex-wrap items-center gap-1.5">
              @if ($item->issuer)
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ $item->issuer->name }}
                </span>
              @endif
            </div>
          </div>
          @if ($item->type)
            <x-filament::badge :color="$colorFor($item->type)" size="sm" :icon="$iconFor($item->type)">
              {{ $item->type->getLabel() }}
            </x-filament::badge>
          @endif
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
