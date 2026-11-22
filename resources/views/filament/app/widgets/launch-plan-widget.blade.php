<x-filament-widgets::widget class="fi-launch-plan-widget">
  @php
    $steps = $this->getSteps();
    $done = collect($steps)->where('done', true)->count();
    $total = count($steps);
  @endphp
  <x-filament::section :heading="'Launch Plan'" :description="sprintf('%d of %d done. Get your organization ready for its members.', $done, $total)"
    :icon="\Filament\Support\Icons\Heroicon::OutlinedRocketLaunch" icon-color="brass">
    <x-slot name="afterHeader">
      <x-filament::link tag="button" color="gray" size="sm" wire:click="dismiss">Hide</x-filament::link>
    </x-slot>

    <div class="mb-4 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10" role="progressbar" aria-valuemin="0"
      aria-valuemax="{{ $total }}" aria-valuenow="{{ $done }}" aria-label="Launch plan progress">
      <div class="h-full rounded-full bg-brass-500" style="width: {{ $total > 0 ? round($done / $total * 100) : 0 }}%"></div>
    </div>

    <ol class="divide-y divide-gray-950/5 dark:divide-white/10">
      @foreach ($steps as $step)
        <li class="flex items-center gap-3 py-3">
          @if ($step['done'])
            <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::CheckCircle" class="size-5 shrink-0 text-success-600 dark:text-success-400" />
          @else
            <span class="size-5 shrink-0 rounded-full ring-2 ring-gray-300 ring-inset dark:ring-white/20" aria-hidden="true"></span>
          @endif
          <span class="min-w-0 flex-1">
            <span @class([
                'block text-sm font-semibold',
                'text-gray-500 line-through decoration-gray-400 dark:text-gray-400' => $step['done'],
                'text-gray-950 dark:text-white' => !$step['done'],
            ])>
              {{ $step['label'] }}
              <span class="sr-only">{{ $step['done'] ? '(done)' : '(to do)' }}</span>
            </span>
            @unless ($step['done'])
              <span class="mt-0.5 block text-sm text-gray-600 dark:text-gray-400">{{ $step['description'] }}</span>
            @endunless
          </span>
          @unless ($step['done'])
            <x-filament::button tag="a" :href="$step['url']" size="sm" color="gray">{{ $step['action'] }}</x-filament::button>
          @endunless
        </li>
      @endforeach
    </ol>
  </x-filament::section>
</x-filament-widgets::widget>
