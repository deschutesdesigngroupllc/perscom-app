@props(['user', 'insight' => null, 'hiddenFields' => []])

@php
  $position = $user->getAttribute('position');
  $specialty = $user->getAttribute('specialty');
  $statusColor = $user->status->color ?? \App\Support\Brand::NEUTRAL_HEX;
  $showsRank = !in_array('rank_id', $hiddenFields) && $user->rank;
  $showsName = !in_array('name', $hiddenFields);
  $showsPosition = !in_array('position_id', $hiddenFields) && $position;
  $showsSpecialty = !in_array('specialty_id', $hiddenFields) && $specialty;
  $showsStatus = !in_array('status_id', $hiddenFields);
  $showsOnline = !in_array('online', $hiddenFields);
  $specialtyLabel = trim(data_get($specialty, 'abbreviation', '') . ' ' . data_get($specialty, 'name', ''));
@endphp

<a href="{{ \App\Filament\App\Resources\UserResource::getUrl('view', ['record' => $user]) }}" target="_blank"
  class="group flex flex-col gap-2 rounded-lg bg-white p-3 ring-1 ring-gray-950/10 hover:ring-primary-500/50 dark:bg-white/5 dark:ring-white/10 dark:hover:ring-primary-400/50">
  <div class="flex items-center gap-2.5">
    <div class="relative shrink-0">
      @if ($showsRank && $user->rank->image)
        <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" title="{{ $user->rank->name }}"
          class="size-9 object-contain" loading="lazy">
      @else
        <x-filament::avatar :src="$user->profile_photo_url" :alt="$user->display_name" size="size-9" loading="lazy" />
      @endif
      @if ($showsOnline)
        <span @class([
            'absolute -end-0.5 -bottom-0.5 size-2.5 rounded-full ring-2 ring-white dark:ring-gray-900',
            'bg-success-500' => $user->online,
            'bg-gray-300 dark:bg-gray-600' => !$user->online,
        ]) title="{{ $user->online ? 'Online' : 'Offline' }}"></span>
      @endif
    </div>

    <div class="min-w-0 flex-1">
      @if ($showsName)
        <div
          class="truncate text-sm font-semibold text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
          @if ($showsRank && $user->rank->abbreviation)
            <span class="me-0.5 text-brass-700 dark:text-brass-400">{{ $user->rank->abbreviation }}</span>
          @endif
          {{ $user->display_name }}
        </div>
      @endif
      @if ($showsPosition)
        <div class="truncate text-xs text-gray-600 dark:text-gray-300">{{ data_get($position, 'name') }}</div>
      @endif
    </div>
  </div>

  @if ($showsSpecialty || $showsStatus || $insight)
    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
      @if ($showsStatus)
        <span class="inline-flex items-center gap-1.5 font-medium text-gray-700 dark:text-gray-200">
          <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $statusColor }}" aria-hidden="true"></span>
          {{ $user->status->name ?? 'No Status Set' }}
        </span>
      @endif
      @if ($showsSpecialty && $specialtyLabel !== '')
        <span class="min-w-0 truncate text-gray-500 dark:text-gray-400" title="{{ $specialtyLabel }}">{{ $specialtyLabel }}</span>
      @endif
      <x-roster.member-badges :insight="$insight" />
    </div>
  @endif
</a>
