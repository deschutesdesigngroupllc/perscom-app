@props(['user', 'insight' => null, 'hiddenFields' => []])

@php
  $position = $user->getAttribute('position');
  $specialty = $user->getAttribute('specialty');

  $details = array_values(
      array_filter([
          !in_array('position_id', $hiddenFields) && $position ? data_get($position, 'name') : null,
          !in_array('specialty_id', $hiddenFields) && $specialty
              ? trim(data_get($specialty, 'abbreviation') . ' ' . data_get($specialty, 'name'))
              : null,
          $insight?->daysInGrade !== null && !in_array('rank_id', $hiddenFields)
              ? number_format($insight->daysInGrade) . ' ' . Str::plural('day', $insight->daysInGrade) . ' in grade'
              : null,
      ]),
  );
@endphp

<a href="{{ \App\Filament\App\Resources\UserResource::getUrl('view', ['record' => $user]) }}" target="_blank"
  class="flex items-start gap-3 px-4 py-2.5 hover:bg-gray-50 sm:items-center dark:hover:bg-white/5">
  @if (!in_array('rank_id', $hiddenFields))
    <div class="flex h-9 w-10 shrink-0 items-center justify-center">
      @if ($user->rank?->image)
        <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" class="max-h-9 max-w-full object-contain" loading="lazy">
      @else
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $user->rank->abbreviation ?? '—' }}</span>
      @endif
    </div>
  @endif

  <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
    <div class="min-w-0 flex-1">
      <div class="flex items-center gap-2">
        @if (!in_array('online', $hiddenFields))
          <span @class([
              'h-2 w-2 shrink-0 rounded-full',
              'bg-success-500' => $user->online,
              'bg-gray-300 dark:bg-gray-600' => !$user->online,
          ]) title="{{ $user->online ? 'Online' : 'Offline' }}"></span>
        @endif
        @if (!in_array('name', $hiddenFields))
          <span class="truncate font-semibold text-gray-950 dark:text-white">
            @if (!in_array('rank_id', $hiddenFields) && $user->rank?->abbreviation)
              <span class="text-gray-500 dark:text-gray-400">{{ $user->rank->abbreviation }}</span>
            @endif
            {{ $user->display_name }}
          </span>
        @endif
      </div>
      @if ($details !== [])
        <div
          class="mt-0.5 flex flex-col text-gray-500 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-2 dark:text-gray-400">
          @foreach ($details as $detail)
            @unless ($loop->first)
              <span class="hidden text-gray-400 sm:inline dark:text-gray-500">&middot;</span>
            @endunless
            <span class="truncate">{{ $detail }}</span>
          @endforeach
        </div>
      @endif
    </div>

    <div class="flex flex-wrap items-center gap-1.5 sm:shrink-0 sm:justify-end">
      <x-roster.member-badges :insight="$insight" />
      @if (!in_array('status_id', $hiddenFields))
        <x-filament::badge :color="\Filament\Support\Colors\Color::hex($user->status->color ?? '#2563eb')">
          {{ $user->status->name ?? 'No Status Set' }}
        </x-filament::badge>
      @endif
    </div>
  </div>
</a>
