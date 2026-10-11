@props(['node', 'mode', 'layout' => 'list', 'insights' => [], 'hiddenFields' => [], 'expanded' => true, 'depth' => 0])

@php
  $unit = $node['unit'];
  $isManual = $mode === \App\Models\Enums\RosterMode::MANUAL;
@endphp

<div x-data="{ open: @js($expanded) }" wire:key="roster-unit-{{ $unit->getKey() }}-{{ $depth }}" @class([
    'rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden' =>
        $depth === 0,
    'border-s-2 border-gray-200 dark:border-white/10 ms-3 ps-3' => $depth > 0,
])>
  <button type="button" x-on:click="open = ! open" class="flex w-full items-center gap-2 bg-gray-50 px-4 py-2 text-start dark:bg-gray-950"
    x-bind:aria-expanded="open">
    <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4 shrink-0 text-gray-400 transition-transform"
      x-bind:class="{ 'rotate-90': open }" />
    @if ($unit->icon)
      <x-filament::icon :icon="$unit->icon" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
    @endif
    <span class="flex-1 font-semibold text-gray-950 dark:text-white">{{ $unit->name }}</span>
    <x-filament::badge color="gray" size="sm">{{ $node['total'] }} {{ Str::plural('member', $node['total']) }}</x-filament::badge>
  </button>

  <div x-show="open" x-collapse>
    @if ($isManual)
      @forelse ($node['slots'] as $slotNode)
        @include('components.roster.slot-header', ['slot' => $slotNode['slot']])
        @if ($slotNode['users']->isEmpty())
          <x-roster.empty-message structure="slot" :message="$slotNode['slot']->empty" />
        @elseif ($layout === 'cards')
          <div class="grid grid-cols-1 gap-3 p-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($slotNode['users'] as $user)
              <x-roster.member-card :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
            @endforeach
          </div>
        @else
          <div class="divide-y divide-gray-100 dark:divide-white/5">
            @foreach ($slotNode['users'] as $user)
              <x-roster.member-row :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
            @endforeach
          </div>
        @endif
      @empty
        @if (blank($node['children']))
          <x-roster.empty-message structure="unit" :message="$unit->empty" />
        @endif
      @endforelse
    @else
      @if ($node['users']->isEmpty())
        @if (blank($node['children']))
          <x-roster.empty-message structure="unit" :message="$unit->empty" />
        @endif
      @elseif ($layout === 'cards')
        <div class="grid grid-cols-1 gap-3 p-3 sm:grid-cols-2 xl:grid-cols-3">
          @foreach ($node['users'] as $user)
            <x-roster.member-card :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
          @endforeach
        </div>
      @else
        <div class="divide-y divide-gray-100 dark:divide-white/5">
          @foreach ($node['users'] as $user)
            <x-roster.member-row :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
          @endforeach
        </div>
      @endif
    @endif

    @if (filled($node['children']))
      <div class="flex flex-col gap-2 py-2 pe-2">
        @foreach ($node['children'] as $child)
          <x-roster.tree-node :node="$child" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields"
            :expanded="true" :depth="$depth + 1" />
        @endforeach
      </div>
    @endif
  </div>
</div>
