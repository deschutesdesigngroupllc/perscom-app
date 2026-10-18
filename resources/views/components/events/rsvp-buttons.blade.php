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
  $activeColors = [
      EventRegistrationStatus::GOING->value => 'success',
      EventRegistrationStatus::TENTATIVE->value => 'warning',
      EventRegistrationStatus::NOT_GOING->value => 'danger',
  ];
@endphp

@if ($canRespond)
  <x-filament::button.group :attributes="$attributes->class('inline-grid')->merge(['role' => 'group', 'aria-label' => 'Your response'])">
    @foreach (EventRegistrationStatus::memberResponses() as $response)
      @php($active = $status === $response)
      <x-filament::button :color="$active ? $activeColors[$response->value] : 'gray'" :icon="$icons[$response->value]" :size="$size === 'md' ? 'md' : 'xs'"
        wire:click="respondToEvent({{ $eventId }}, {{ $starts }}, '{{ $response->value }}', {{ $wholeSeries ? 'true' : 'false' }})"
        wire:loading.attr="disabled" aria-pressed="{{ $active ? 'true' : 'false' }}">
        {{ $response->getMemberLabel() }}
      </x-filament::button>
    @endforeach
  </x-filament::button.group>
@elseif ($status)
  <x-filament::badge :color="$status->getColor()" :size="$size">{{ $status->getMemberLabel() }}</x-filament::badge>
@endif
