@props(['registrations', 'limit' => 5])

@php
  $visible = $registrations->take($limit);
  $remaining = max(0, $registrations->count() - $limit);
@endphp

@if ($registrations->isNotEmpty())
  <div {{ $attributes->class('flex items-center') }}>
    <div class="flex -space-x-2">
      @foreach ($visible as $registration)
        @php($user = $registration->getRelation('user'))
        <x-filament::avatar :src="$user->profile_photo_url" :alt="$user->display_name" :title="$user->display_name" size="size-7" class="ring-2 ring-white dark:ring-gray-900"
          loading="lazy" />
      @endforeach
    </div>
    @if ($remaining > 0)
      <span class="ms-2 text-xs font-medium text-gray-500 dark:text-gray-400">+{{ $remaining }}</span>
    @endif
  </div>
@endif
