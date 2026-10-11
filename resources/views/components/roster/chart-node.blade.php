@props(['node', 'mode', 'insights' => [], 'hiddenFields' => []])

@php
  $unit = $node['unit'];
  $members =
      $mode === \App\Models\Enums\RosterMode::MANUAL ? collect($node['slots'])->flatMap(fn(array $slot) => $slot['users']) : $node['users'];
@endphp

<div class="flex flex-col items-center" wire:key="roster-chart-{{ $unit->getKey() }}">
  <div
    class="w-60 rounded-lg bg-white shadow-xs ring-1 ring-gray-950/10 break-inside-avoid dark:bg-gray-900 dark:ring-white/10 print:shadow-none print:ring-gray-400">
    <div class="flex items-center justify-between gap-2 rounded-t-lg bg-gray-50 px-3 py-2 dark:bg-gray-950 print:bg-gray-100">
      <span class="truncate font-semibold text-gray-950 dark:text-white">{{ $unit->name }}</span>
      <span class="text-xs text-gray-500 dark:text-gray-400">{{ $node['total'] }}</span>
    </div>
    <ul class="divide-y divide-gray-100 dark:divide-white/5">
      @forelse ($members as $user)
        <li class="flex items-center gap-2 px-3 py-1.5">
          @if (!in_array('rank_id', $hiddenFields))
            @if ($user->rank?->image)
              <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" class="h-5 w-6 object-contain" loading="lazy">
            @else
              <span class="w-6 text-center text-[10px] font-semibold text-gray-500">{{ $user->rank->abbreviation ?? '' }}</span>
            @endif
          @endif
          <div class="min-w-0 flex-1">
            @if (!in_array('name', $hiddenFields))
              <div class="truncate text-xs font-medium text-gray-950 dark:text-white">{{ $user->display_name }}</div>
            @endif
            @if (!in_array('position_id', $hiddenFields) && ($position = $user->getAttribute('position')))
              <div class="truncate text-[11px] text-gray-500 dark:text-gray-400">{{ data_get($position, 'name') }}</div>
            @endif
          </div>
          @if (data_get($insights, $user->getKey() . '.promotionEligible'))
            <x-filament::icon icon="heroicon-m-chevron-double-up" class="h-3.5 w-3.5 text-success-600" />
          @endif
        </li>
      @empty
        <li class="px-3 py-2 text-center text-xs text-gray-500 dark:text-gray-400">No personnel assigned.</li>
      @endforelse
    </ul>
  </div>

  @if (filled($node['children']))
    <div class="h-4 w-px bg-gray-300 dark:bg-white/20 print:bg-gray-500"></div>
    <div class="relative flex gap-4 pt-4">
      @if (count($node['children']) > 1)
        <div class="absolute start-30 end-30 top-0 h-px bg-gray-300 dark:bg-white/20 print:bg-gray-500"></div>
      @endif
      @foreach ($node['children'] as $child)
        <div class="relative flex flex-col items-center">
          <div class="absolute -top-4 h-4 w-px bg-gray-300 dark:bg-white/20 print:bg-gray-500"></div>
          <x-roster.chart-node :node="$child" :mode="$mode" :insights="$insights" :hidden-fields="$hiddenFields" />
        </div>
      @endforeach
    </div>
  @endif
</div>
