@props(['user', 'coverHeight' => 224, 'hiddenFields' => []])

@use('App\Models\User')
@use('Filament\Support\Colors\Color')

@php
  /** @var User $user */
  /** @var array $hiddenFields */
  $coverUrl = in_array('cover_photo', $hiddenFields) ? null : $user->cover_photo_url;
  $statusColor = $user->status?->color ?? '#6b7280';
  $online = $user->online;
  $coverStyles = sprintf('height: %dpx;', (int) $coverHeight);

  $stats = collect([
      [
          'key' => 'time_in_service',
          'label' => 'Time in Service',
          'value' => $user->time_in_service?->forHumans(['parts' => 2, 'short' => true]),
      ],
      ['key' => 'time_in_grade', 'label' => 'Time in Grade', 'value' => $user->time_in_grade?->forHumans(['parts' => 2, 'short' => true])],
      [
          'key' => 'time_in_assignment',
          'label' => 'Time in Assignment',
          'value' => $user->time_in_assignment?->forHumans(['parts' => 2, 'short' => true]),
      ],
      ['key' => 'last_seen_at', 'label' => 'Last Online', 'value' => $user->last_seen_at?->diffForHumans() ?? 'Never'],
  ])
      ->reject(fn($stat) => in_array($stat['key'], $hiddenFields))
      ->values();
@endphp

<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
  <div
    class="relative w-full bg-gradient-to-br from-primary-500/30 via-primary-500/10 to-gray-200 dark:from-primary-400/20 dark:via-gray-800 dark:to-gray-900 bg-cover bg-center"
    style="{{ $coverStyles }} @if ($coverUrl) background-image: url('{{ $coverUrl }}'); @endif">
    <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent dark:from-gray-900"></div>
  </div>

  <div class="relative px-4 pb-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-6">
      @unless (in_array('profile_photo', $hiddenFields))
        <div class="relative -mt-4 shrink-0 sm:-mt-6">
          <img src="{{ $user->profile_photo_url }}" alt="{{ $user->display_name }}"
            class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-md ring-1 ring-gray-950/5 dark:border-gray-900 dark:ring-white/10 sm:h-28 sm:w-28" />
        </div>
      @endunless

      <div class="flex flex-1 flex-col gap-1 sm:pt-4">
        <h2 class="flex items-center gap-2 text-2xl font-bold leading-tight text-gray-950 dark:text-white sm:text-3xl">
          @unless (in_array('name', $hiddenFields))
            <span>{{ $user->display_name }}</span>
          @endunless
          @unless (in_array('online', $hiddenFields))
            <span
              class="inline-block h-3 w-3 shrink-0 rounded-full ring-2 ring-white dark:ring-gray-900 {{ $online ? 'bg-emerald-500' : 'bg-gray-400' }}"
              title="{{ $online ? 'Online' : 'Offline' }}"></span>
          @endunless
        </h2>

        <div class="flex flex-wrap items-center gap-2 text-sm">
          @unless (in_array('rank_id', $hiddenFields))
            <x-filament::badge color="gray">
              @if ($user->rank?->image?->image_url)
                <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" class="mr-1 inline h-4" />
              @endif
              {{ $user->rank?->name ?? 'No Rank' }}
            </x-filament::badge>
          @endunless

          @unless (in_array('position_id', $hiddenFields))
            <x-filament::badge color="gray">{{ $user->position?->name ?? 'No Position' }}</x-filament::badge>
          @endunless

          @unless (in_array('unit_id', $hiddenFields))
            <x-filament::badge color="gray">{{ $user->unit?->name ?? 'No Unit' }}</x-filament::badge>
          @endunless

          @unless (in_array('status_id', $hiddenFields))
            <x-filament::badge :color="Color::generateV3Palette($statusColor)">
              {{ $user->status?->name ?? 'No Status' }}
            </x-filament::badge>
          @endunless

          @if (!$user->approved && !in_array('approved', $hiddenFields))
            <x-filament::badge color="warning" icon="heroicon-m-exclamation-circle">
              Pending Approval
            </x-filament::badge>
          @endif
        </div>
      </div>
    </div>

    @if ($stats->isNotEmpty())
      <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-200 pt-4 text-sm dark:border-gray-700 sm:grid-cols-4">
        @foreach ($stats as $stat)
          <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
              {{ $stat['label'] }}
            </dt>
            <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
              {{ $stat['value'] ?: '—' }}
            </dd>
          </div>
        @endforeach
      </dl>
    @endif
  </div>
</div>
