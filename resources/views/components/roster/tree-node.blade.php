@props(['node', 'mode', 'layout' => 'list', 'insights' => [], 'hiddenFields' => [], 'expanded' => true, 'depth' => 0])

@php
  $unit = $node['unit'];
  $isManual = $mode === \App\Models\Enums\RosterMode::MANUAL;
  $hasChildren = filled($node['children']);
  $hasMembers = $isManual ? collect($node['slots'])->isNotEmpty() : $node['users']->isNotEmpty();
  $customEmpty = filled(Str::of($unit->empty)->stripTags()) ? $unit->empty : null;
  $members = $node['total'];
@endphp

@if ($depth === 0)
  {{-- Top level: a page section heading, with its sub-units as cards beneath it. --}}
  <section x-data="{ open: {{ $expanded ? 'true' : 'false' }} }" wire:key="roster-unit-{{ $unit->getKey() }}-0" class="flex flex-col gap-3">
    <button type="button" x-on:click="open = ! open" x-bind:aria-expanded="open.toString()"
      class="group flex items-center gap-2.5 self-start rounded-lg text-start">
      @if ($unit->icon)
        <x-filament::icon :icon="$unit->icon" class="size-5 shrink-0 text-brass-600 dark:text-brass-400" />
      @endif
      <h2 class="font-display text-lg font-semibold text-gray-950 dark:text-white">{{ $unit->name }}</h2>
      <span class="text-sm text-gray-500 tabular-nums dark:text-gray-400">
        {{ $members > 0 ? $members . ' ' . Str::plural('member', $members) : 'No members' }}
      </span>
      <x-filament::icon icon="heroicon-m-chevron-down"
        class="size-4 shrink-0 text-gray-400 transition-transform duration-150 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300"
        x-bind:class="!open && '-rotate-90'" />
    </button>

    <div x-show="open" @class([
        'grid grid-cols-1 gap-4',
        'xl:grid-cols-2' => $layout === 'list',
    ])>
      @if ($hasMembers)
        <div class="col-span-full overflow-hidden rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
          <x-roster.unit-members :node="$node" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields" />
        </div>
      @endif

      @foreach ($node['children'] as $child)
        <x-roster.tree-node :node="$child" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields" :depth="1" />
      @endforeach

      @if (!$hasMembers && !$hasChildren)
        <p class="col-span-full text-sm text-gray-500 dark:text-gray-400">
          {!! $customEmpty ?? 'No members assigned to this unit.' !!}
        </p>
      @endif
    </div>
  </section>
@elseif ($depth === 1)
  {{-- Second level: a card. --}}
  <div x-data="{ open: true }" wire:key="roster-unit-{{ $unit->getKey() }}-1"
    class="self-start overflow-hidden rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <button type="button" x-on:click="open = ! open" x-bind:aria-expanded="open.toString()"
      class="flex w-full items-center gap-2.5 px-4 py-3 text-start hover:bg-gray-50 dark:hover:bg-white/5">
      @if ($unit->icon)
        <x-filament::icon :icon="$unit->icon" class="size-5 shrink-0 text-gray-400 dark:text-gray-500" />
      @endif
      <h3 class="min-w-0 flex-1 truncate font-display text-base font-semibold text-gray-950 dark:text-white">{{ $unit->name }}</h3>
      @if ($members > 0)
        <span
          class="shrink-0 rounded-md bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-700 tabular-nums dark:bg-white/10 dark:text-gray-300">
          {{ $members }}
        </span>
      @else
        <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">No members</span>
      @endif
      <x-filament::icon icon="heroicon-m-chevron-down"
        class="size-4 shrink-0 text-gray-400 transition-transform duration-150 dark:text-gray-500" x-bind:class="!open && '-rotate-90'" />
    </button>

    <div x-show="open">
      @if ($hasMembers)
        <div class="border-t border-gray-950/5 dark:border-white/5">
          <x-roster.unit-members :node="$node" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields" />
        </div>
      @elseif (!$hasChildren && $customEmpty)
        <x-roster.empty-message structure="unit" :message="$customEmpty" />
      @endif

      @foreach ($node['children'] as $child)
        <x-roster.tree-node :node="$child" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields"
          :depth="2" />
      @endforeach
    </div>
  </div>
@else
  {{-- Deeper levels: quiet sub-rows inside the card. --}}
  <div wire:key="roster-unit-{{ $unit->getKey() }}-{{ $depth }}" class="border-t border-gray-950/5 dark:border-white/5">
    <div class="flex items-center gap-2 px-4 py-2.5" style="padding-inline-start: {{ 1 + ($depth - 2) * 1.25 }}rem">
      @if ($unit->icon)
        <x-filament::icon :icon="$unit->icon" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
      @endif
      <h4 @class([
          'min-w-0 flex-1 truncate text-sm',
          'font-medium text-gray-800 dark:text-gray-200' => $members > 0,
          'text-gray-500 dark:text-gray-400' => $members === 0,
      ])>{{ $unit->name }}</h4>
      @if ($members > 0)
        <span class="shrink-0 text-xs text-gray-500 tabular-nums dark:text-gray-400">{{ $members }}</span>
      @endif
    </div>

    @if ($hasMembers)
      <div style="padding-inline-start: {{ ($depth - 2) * 1.25 }}rem">
        <x-roster.unit-members :node="$node" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields" />
      </div>
    @elseif (!$hasChildren && $customEmpty)
      <x-roster.empty-message structure="unit" :message="$customEmpty" />
    @endif

    @foreach ($node['children'] as $child)
      <x-roster.tree-node :node="$child" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields"
        :depth="$depth + 1" />
    @endforeach
  </div>
@endif
