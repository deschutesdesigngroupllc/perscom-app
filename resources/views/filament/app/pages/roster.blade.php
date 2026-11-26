<x-filament-panels::page>
  <style>
    @page {
      size: landscape;
      margin: 12mm;
    }

    @media print {

      .fi-sidebar,
      .fi-topbar,
      .fi-header,
      .fi-sidebar-close-overlay {
        display: none !important;
      }

      html,
      body,
      .fi-body,
      .fi-layout,
      .fi-main-ctn,
      .fi-main {
        background: #fff !important;
      }

      .fi-main-ctn,
      .fi-main {
        margin: 0 !important;
        padding: 0 !important;
        max-width: none !important;
      }

      * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .roster-print-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
        line-height: 1.3;
        color: #111827;
      }

      .roster-print-table th,
      .roster-print-table td {
        border: 0.5pt solid #d1d5db;
        padding: 1mm 2mm;
        text-align: left;
        vertical-align: top;
      }

      .roster-print-table thead th {
        background: #e5e7eb;
        font-size: 8pt;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
      }

      .roster-print-table tbody {
        break-inside: avoid;
      }

      .roster-print-unit th {
        background: #f3f4f6;
        font-weight: 600;
      }

      .roster-print-unit span {
        margin-left: 2mm;
        font-weight: 400;
        color: #6b7280;
      }

      .roster-print-col-rank {
        width: 12%;
      }

      .roster-print-col-slot {
        width: 16%;
      }

      .roster-print-col-status {
        width: 14%;
      }

      .roster-print-empty {
        color: #9ca3af;
        font-style: italic;
      }
    }
  </style>

  {{-- Printing switches to the light theme and, for the org chart, scales it to fit one landscape page. --}}
  <div x-data="{
      wasDark: false,
      prepareForPrint() {
          this.wasDark = document.documentElement.classList.contains('dark')
          document.documentElement.classList.remove('dark')
          const chart = this.$root.querySelector('[data-roster-chart]')
          if (!chart) return
          chart.style.zoom = ''
          const printableWidth = 960
          const nodes = chart.children
          const chartWidth = nodes[nodes.length - 1].getBoundingClientRect().right - nodes[0].getBoundingClientRect().left
          chart.style.zoom = Math.min(1, printableWidth / chartWidth)
      },
      restoreAfterPrint() {
          if (this.wasDark) document.documentElement.classList.add('dark')
          this.$root.querySelector('[data-roster-chart]')?.style.removeProperty('zoom')
      },
  }" x-on:beforeprint.window="prepareForPrint()" x-on:afterprint.window="restoreAfterPrint()"
    class="flex flex-col gap-4">
    <div class="flex flex-col gap-3 print:hidden">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
        <div class="flex-1">
          <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
            <x-filament::input type="search" wire:model.live.debounce.300ms="search"
              placeholder="Search by name, rank, position or specialty" aria-label="Search the roster" />
          </x-filament::input.wrapper>
        </div>

        <x-filament::button.group role="group" aria-label="Roster layout">
          @foreach (['list' => ['List', 'heroicon-m-bars-3'], 'cards' => ['Cards', 'heroicon-m-squares-2x2'], 'chart' => ['Org Chart', 'heroicon-m-share']] as $key => [$label, $icon])
            <x-filament::button :color="$display === $key ? 'primary' : 'gray'" :icon="$icon" wire:click="setDisplay('{{ $key }}')"
              aria-pressed="{{ $display === $key ? 'true' : 'false' }}">
              {{ $label }}
            </x-filament::button>
          @endforeach
        </x-filament::button.group>

        <x-filament::button color="gray" icon="heroicon-m-printer" x-on:click="window.print()">
          Print
        </x-filament::button>
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
          @if ($filtering)
            <x-filament::empty-state icon="heroicon-o-magnifying-glass" icon-color="gray" heading-tag="h3">
              <x-slot name="heading">No members found</x-slot>
              <x-slot name="description">No members match the current search and filters.</x-slot>
            </x-filament::empty-state>
          @else
            <x-filament::empty-state icon="heroicon-o-user-group" icon-color="gray" heading-tag="h3">
              <x-slot name="heading">No units in this group</x-slot>

              @if (filled(Str::of($activeGroup?->empty)->stripTags()))
                <x-slot name="footer">
                  <div class="text-sm text-gray-500 dark:text-gray-400">{!! $activeGroup->empty !!}</div>
                </x-slot>
              @endif
            </x-filament::empty-state>
          @endif
        @elseif ($display === 'chart')
          <div class="overflow-x-auto pb-4 print:overflow-visible print:pb-0">
            <div data-roster-chart class="flex min-w-max justify-center gap-6 p-2">
              @foreach ($tree as $node)
                <x-roster.chart-node :node="$node" :mode="$mode" :insights="$insights" :hidden-fields="$hiddenFields" />
              @endforeach
            </div>
          </div>
        @else
          <div class="hidden print:block">
            <x-roster.print-table :tree="$tree" :mode="$mode" :hidden-fields="$hiddenFields" />
          </div>

          <div class="flex flex-col gap-8 print:hidden">
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
