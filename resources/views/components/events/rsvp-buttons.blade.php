@props(['occurrence', 'status' => null, 'size' => 'sm', 'wholeSeries' => false])

@php
  use App\Models\Enums\EventRegistrationStatus;
  use App\Services\EventRegistrationService;

  $canRespond = EventRegistrationService::canRespond($occurrence);
  $eventId = $occurrence->event->getKey();
  $starts = $occurrence->starts->getTimestamp();
  $icons = [
      EventRegistrationStatus::GOING->value => 'heroicon-m-check',
      EventRegistrationStatus::TENTATIVE->value => 'heroicon-m-question-mark-circle',
      EventRegistrationStatus::NOT_GOING->value => 'heroicon-m-x-mark',
  ];
@endphp

@if ($canRespond)
  <div {{ $attributes->class('inline-flex isolate rounded-lg shadow-xs') }} role="group" aria-label="Your response">
    @foreach (EventRegistrationStatus::memberResponses() as $response)
      @php($active = $status === $response)
      <button type="button"
        wire:click="respondToEvent({{ $eventId }}, {{ $starts }}, '{{ $response->value }}', {{ $wholeSeries ? 'true' : 'false' }})"
        wire:loading.attr="disabled" wire:target="respondToEvent" aria-pressed="{{ $active ? 'true' : 'false' }}"
        @class([
            'relative inline-flex items-center gap-1 font-semibold ring-1 ring-inset transition focus-visible:z-10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:cursor-wait',
            'first:rounded-s-lg last:rounded-e-lg -ms-px first:ms-0',
            'px-2.5 py-1 text-xs' => $size === 'sm',
            'px-3.5 py-2 text-sm' => $size === 'md',
            'bg-white text-gray-700 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/20 dark:hover:bg-white/10' => !$active,
            'z-10 bg-success-600 text-white ring-success-600' => $active && $response === EventRegistrationStatus::GOING,
            'z-10 bg-warning-500 text-white ring-warning-500' => $active && $response === EventRegistrationStatus::TENTATIVE,
            'z-10 bg-danger-600 text-white ring-danger-600' => $active && $response === EventRegistrationStatus::NOT_GOING,
        ])>
        <x-filament::icon :icon="$icons[$response->value]" @class(['size-4' => $size === 'md', 'size-3.5' => $size === 'sm']) />
        {{ $response->getMemberLabel() }}
      </button>
    @endforeach
  </div>
@elseif ($status)
  <x-filament::badge :color="$status->getColor()" :size="$size">{{ $status->getMemberLabel() }}</x-filament::badge>
@endif
