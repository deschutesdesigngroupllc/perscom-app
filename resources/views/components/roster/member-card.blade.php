@props(['user', 'insight' => null, 'hiddenFields' => []])

@php
  $position = $user->getAttribute('position');
  $specialty = $user->getAttribute('specialty');
@endphp

<a href="{{ \App\Filament\App\Resources\UserResource::getUrl('view', ['record' => $user]) }}" target="_blank"
  class="flex flex-col gap-3 rounded-lg p-3 ring-1 ring-gray-950/5 hover:bg-gray-50 dark:ring-white/10 dark:hover:bg-white/5">
  <div class="flex items-start gap-3">
    <div class="relative shrink-0">
      <img src="{{ $user->profile_photo_url }}" alt="{{ $user->display_name }}" class="h-12 w-12 rounded-full object-cover" loading="lazy">
      @if (!in_array('online', $hiddenFields))
        <span @class([
            'absolute bottom-0 end-0 h-3 w-3 rounded-full ring-2 ring-white dark:ring-gray-900',
            'bg-success-500' => $user->online,
            'bg-gray-300 dark:bg-gray-600' => !$user->online,
        ]) title="{{ $user->online ? 'Online' : 'Offline' }}"></span>
      @endif
    </div>
    <div class="min-w-0 flex-1">
      @if (!in_array('name', $hiddenFields))
        <div class="truncate font-semibold text-gray-950 dark:text-white">{{ $user->display_name }}</div>
      @endif
      @if (!in_array('rank_id', $hiddenFields) && $user->rank)
        <div class="mt-0.5 flex items-center gap-1.5 text-gray-600 dark:text-gray-300">
          @if ($user->rank->image)
            <img src="{{ $user->rank->image->image_url }}" alt="" class="h-4 w-auto shrink-0 object-contain" loading="lazy">
          @endif
          <span class="truncate">{{ $user->rank->name }}</span>
        </div>
      @endif
      @if (!in_array('position_id', $hiddenFields) && $position)
        <div class="truncate text-gray-500 dark:text-gray-400">{{ data_get($position, 'name') }}</div>
      @endif
      @if (!in_array('specialty_id', $hiddenFields) && $specialty)
        <div class="truncate text-xs text-gray-500 dark:text-gray-400">{{ data_get($specialty, 'abbreviation') }}
          {{ data_get($specialty, 'name') }}</div>
      @endif
    </div>
  </div>

  <div class="flex flex-wrap items-center gap-1.5">
    @if (!in_array('status_id', $hiddenFields))
      <x-filament::badge :color="\Filament\Support\Colors\Color::hex($user->status->color ?? '#2563eb')" size="sm">
        {{ $user->status->name ?? 'No Status Set' }}
      </x-filament::badge>
    @endif
    <x-roster.member-badges :insight="$insight" />
  </div>
</a>
