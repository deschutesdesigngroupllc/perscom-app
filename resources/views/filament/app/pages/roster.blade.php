<x-filament-panels::page>
  <style>
    @media print {

      .fi-sidebar,
      .fi-topbar,
      .fi-header,
      .fi-sidebar-close-overlay {
        display: none !important;
      }

      .fi-main-ctn,
      .fi-main {
        margin: 0 !important;
        padding: 0 !important;
        max-width: none !important;
      }
    }
  </style>

  <div class="flex flex-col gap-4">
    <div class="flex flex-col gap-3 print:hidden">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
        <div class="flex-1">
          <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
            <x-filament::input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by name, rank, position or specialty"
              aria-label="Search the roster" />
          </x-filament::input.wrapper>
        </div>

        <div class="flex items-center gap-1 rounded-lg bg-gray-100 p-1 dark:bg-white/5" role="group" aria-label="Roster layout">
          @foreach (['list' => ['List', 'heroicon-m-bars-3'], 'cards' => ['Cards', 'heroicon-m-squares-2x2'], 'chart' => ['Org Chart', 'heroicon-m-share']] as $key => [$label, $icon])
            <button type="button" wire:click="setDisplay('{{ $key }}')" @class([
                'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition',
                'bg-white text-gray-950 shadow-xs dark:bg-gray-800 dark:text-white' =>
                    $display === $key,
                'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' =>
                    $display !== $key,
            ])
              aria-pressed="{{ $display === $key ? 'true' : 'false' }}">
              <x-filament::icon :icon="$icon" class="h-4 w-4" />
              {{ $label }}
            </button>
          @endforeach
        </div>

        @if ($display === 'chart')
          <x-filament::button color="gray" icon="heroicon-m-printer" x-on:click="window.print()">
            Print
          </x-filament::button>
        @endif
      </div>

      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-center">
        <x-filament::input.wrapper>
          <x-filament::input.select wire:model.live="rankId" aria-label="Filter by rank">
            <option value="">All Ranks</option>
            @foreach ($rankOptions as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper>
          <x-filament::input.select wire:model.live="statusId" aria-label="Filter by status">
            <option value="">All Statuses</option>
            @foreach ($statusOptions as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper>
          <x-filament::input.select wire:model.live="positionId" aria-label="Filter by position">
            <option value="">All Positions</option>
            @foreach ($positionOptions as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper>
          <x-filament::input.select wire:model.live="unitId" aria-label="Filter by unit">
            <option value="">All Units</option>
            @foreach ($unitOptions as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </x-filament::input.select>
        </x-filament::input.wrapper>

        <div class="flex items-center justify-between gap-3">
          <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <x-filament::input.checkbox wire:model.live="onlineOnly" />
            Online Only
          </label>

          @if ($filtering)
            <x-filament::link tag="button" wire:click="resetFilters" color="gray" size="sm">
              Clear Filters
            </x-filament::link>
          @endif
        </div>
      </div>
    </div>

    @if ($groups->isEmpty())
      @include('components.roster.no-groups-found')
    @else
      <div class="print:hidden">
        <x-filament::tabs label="Group tabs">
          @foreach ($groups as $group)
            <x-filament::tabs.item :active="$activeGroup?->is($group)" wire:click="selectGroup({{ $group->getKey() }})" :icon="$group->icon">
              {{ $group->name }}
            </x-filament::tabs.item>
          @endforeach
        </x-filament::tabs>
      </div>

      <div class="hidden print:block text-lg font-semibold text-gray-950">
        {{ $activeGroup?->name }}
      </div>

      <div wire:loading.class="opacity-60" class="text-sm transition-opacity">
        @if (blank($tree))
          <div
            class="rounded-xl bg-white p-6 text-center text-gray-500 shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:text-gray-400 dark:ring-white/10">
            @if ($filtering)
              No members match the current search and filters.
            @else
              {!! filled(Str::of($activeGroup?->empty)->stripTags()) ? $activeGroup->empty : 'There are no units in this group.' !!}
            @endif
          </div>
        @elseif ($display === 'chart')
          <div class="overflow-x-auto pb-4 print:overflow-visible">
            <div class="flex min-w-max justify-center gap-6 p-2">
              @foreach ($tree as $node)
                <x-roster.chart-node :node="$node" :mode="$mode" :insights="$insights" :hidden-fields="$hiddenFields" />
              @endforeach
            </div>
          </div>
        @else
          <div class="flex flex-col gap-3">
            @foreach ($tree as $node)
              <x-roster.tree-node :node="$node" :mode="$mode" :layout="$display" :insights="$insights" :hidden-fields="$hiddenFields"
                :expanded="true" />
            @endforeach
          </div>
        @endif
      </div>
    @endif
  </div>
</x-filament-panels::page>
