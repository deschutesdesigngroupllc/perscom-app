@props(['node', 'mode', 'layout' => 'list', 'insights' => [], 'hiddenFields' => []])

@php
  $isManual = $mode === \App\Models\Enums\RosterMode::MANUAL;
@endphp

@if ($isManual)
  @foreach ($node['slots'] as $slotNode)
    @include('components.roster.slot-header', ['slot' => $slotNode['slot'], 'vacant' => $slotNode['users']->isEmpty()])
    @if ($slotNode['users']->isEmpty())
      @if (filled(Str::of($slotNode['slot']->empty)->stripTags()))
        <x-roster.empty-message structure="slot" :message="$slotNode['slot']->empty" />
      @endif
    @elseif ($layout === 'cards')
      <div class="grid grid-cols-1 gap-3 px-4 pb-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($slotNode['users'] as $user)
          <x-roster.member-card :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
        @endforeach
      </div>
    @else
      <div class="divide-y divide-gray-950/5 dark:divide-white/5">
        @foreach ($slotNode['users'] as $user)
          <x-roster.member-row :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
        @endforeach
      </div>
    @endif
  @endforeach
@elseif ($node['users']->isNotEmpty())
  @if ($layout === 'cards')
    <div class="grid grid-cols-1 gap-3 p-3 sm:grid-cols-2 xl:grid-cols-3">
      @foreach ($node['users'] as $user)
        <x-roster.member-card :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
      @endforeach
    </div>
  @else
    <div class="divide-y divide-gray-950/5 dark:divide-white/5">
      @foreach ($node['users'] as $user)
        <x-roster.member-row :user="$user" :insight="$insights[$user->getKey()] ?? null" :hidden-fields="$hiddenFields" />
      @endforeach
    </div>
  @endif
@endif
