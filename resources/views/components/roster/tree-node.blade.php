@props(['node', 'mode', 'layout' => 'list', 'insights' => [], 'hiddenFields' => [], 'expanded' => true, 'depth' => 0])

@php
  $unit = $node['unit'];
  $isManual = $mode === \App\Models\Enums\RosterMode::MANUAL;
@endphp

<x-filament::section collapsible :collapsed="!$expanded" compact :heading="$unit->name" :icon="$unit->icon ?: null" icon-size="md" :has-content-el="false"
  wire:key="roster-unit-{{ $unit->getKey() }}-{{ $depth }}">
  <x-slot name="afterHeader">
    <x-filament::badge color="gray" size="sm">{{ $node['total'] }} {{ Str::plural('member', $node['total']) }}</x-filament::badge>
  </x-slot>

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
    <div class="flex flex-col gap-2 p-2 ps-4">
      @foreach ($node['children'] as $child)
        <x-roster.tree-node :node="$child" :mode="$mode" :layout="$layout" :insights="$insights" :hidden-fields="$hiddenFields"
          :expanded="true" :depth="$depth + 1" />
      @endforeach
    </div>
  @endif
</x-filament::section>
