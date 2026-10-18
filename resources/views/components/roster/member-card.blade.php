@props(['user', 'insight' => null, 'hiddenFields' => []])

@php
  $position = $user->getAttribute('position');
  $specialty = $user->getAttribute('specialty');
  $statusColor = $user->status->color ?? '#2563eb';
  $showsRank = !in_array('rank_id', $hiddenFields) && $user->rank;
  $showsPosition = !in_array('position_id', $hiddenFields) && $position;
  $showsSpecialty = !in_array('specialty_id', $hiddenFields) && $specialty;
  $showsStatus = !in_array('status_id', $hiddenFields);
  $hasInsightBadges = $insight && ($insight->promotionEligible || $insight->hasExpired || $insight->hasExpiring);
@endphp

<a href="{{ \App\Filament\App\Resources\UserResource::getUrl('view', ['record' => $user]) }}" target="_blank"
  class="group relative flex flex-col overflow-hidden rounded-xl bg-white shadow-xs ring-1 ring-gray-950/5 transition duration-150 hover:-translate-y-0.5 hover:shadow-md hover:ring-gray-950/10 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/[0.07] dark:hover:ring-white/20">
  @if ($showsStatus)
    <span class="absolute inset-x-0 top-0 h-1" style="background-color: {{ $statusColor }}" aria-hidden="true"></span>
  @endif

  <div class="flex items-start gap-3 p-4 pb-3">
    <div class="relative shrink-0">
      <x-filament::avatar :src="$user->profile_photo_url" :alt="$user->display_name" size="size-14" class="ring-2 ring-white dark:ring-gray-900" loading="lazy" />
      @if (!in_array('online', $hiddenFields))
        <span @class([
            'absolute bottom-0.5 end-0.5 size-3.5 rounded-full ring-2 ring-white dark:ring-gray-900',
            'bg-success-500' => $user->online,
            'bg-gray-300 dark:bg-gray-600' => !$user->online,
        ]) title="{{ $user->online ? 'Online' : 'Offline' }}"></span>
      @endif
    </div>

    <div class="min-w-0 flex-1 pt-0.5">
      @if (!in_array('name', $hiddenFields))
        <div
          class="truncate text-base font-semibold text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
          {{ $user->display_name }}
        </div>
      @endif
      @if ($showsRank)
        <div class="mt-0.5 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
          @if ($user->rank->abbreviation)
            <span
              class="shrink-0 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $user->rank->abbreviation }}</span>
            <span class="text-gray-300 dark:text-gray-600" aria-hidden="true">&middot;</span>
          @endif
          <span class="truncate">{{ $user->rank->name }}</span>
        </div>
      @endif
    </div>

    @if ($showsRank && $user->rank->image)
      <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" title="{{ $user->rank->name }}"
        class="h-9 w-9 shrink-0 object-contain" loading="lazy">
    @endif
  </div>

  @if ($showsPosition || $showsSpecialty)
    <dl class="mx-4 flex flex-col gap-1.5 border-t border-gray-950/5 py-3 text-sm dark:border-white/10">
      @if ($showsPosition)
        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
          <dt class="sr-only">Position</dt>
          <x-filament::icon icon="heroicon-m-briefcase" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
          <dd class="truncate">{{ data_get($position, 'name') }}</dd>
        </div>
      @endif
      @if ($showsSpecialty)
        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
          <dt class="sr-only">Specialty</dt>
          <x-filament::icon icon="heroicon-m-identification" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
          <dd class="truncate">
            @if (data_get($specialty, 'abbreviation'))
              <span class="font-medium text-gray-700 dark:text-gray-300">{{ data_get($specialty, 'abbreviation') }}</span>
            @endif
            {{ data_get($specialty, 'name') }}
          </dd>
        </div>
      @endif
    </dl>
  @endif

  @if ($showsStatus || $hasInsightBadges)
    <div
      class="mt-auto flex flex-wrap items-center gap-1.5 border-t border-gray-950/5 bg-gray-50/60 px-4 py-2.5 dark:border-white/10 dark:bg-white/[0.02]">
      @if ($showsStatus)
        <x-filament::badge :color="\Filament\Support\Colors\Color::hex($statusColor)" size="sm">
          {{ $user->status->name ?? 'No Status Set' }}
        </x-filament::badge>
      @endif
      <x-roster.member-badges :insight="$insight" />
    </div>
  @endif
</a>
