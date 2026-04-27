@props(['user', 'coverHeight' => 224])

@use('App\Models\User')
@use('Filament\Support\Colors\Color')

@php
  /** @var User $user */
  $coverUrl = $user->cover_photo_url;
  $statusColor = $user->status?->color ?? '#6b7280';
  $online = $user->online;
  $coverStyles = sprintf('height: %dpx;', (int) $coverHeight);
@endphp

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
  <div
    class="relative w-full bg-gradient-to-br from-primary-500/30 via-primary-500/10 to-gray-200 dark:from-primary-400/20 dark:via-gray-800 dark:to-gray-900 bg-cover bg-center"
    style="{{ $coverStyles }} @if ($coverUrl) background-image: url('{{ $coverUrl }}'); @endif">
    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
  </div>

  <div class="relative px-4 pb-5 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-6">
      <div class="relative -mt-12 shrink-0 sm:-mt-14">
        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->display_name }}"
          class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-md ring-1 ring-gray-200 dark:border-gray-900 dark:ring-gray-700 sm:h-28 sm:w-28" />
      </div>

      <div class="flex flex-1 flex-col gap-1 sm:pt-4">
        <h2 class="flex items-center gap-2 text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100 sm:text-3xl">
          <span>{{ $user->display_name }}</span>
          <span
            class="inline-block h-3 w-3 shrink-0 rounded-full ring-2 ring-white dark:ring-gray-900 {{ $online ? 'bg-emerald-500' : 'bg-gray-400' }}"
            title="{{ $online ? 'Online' : 'Offline' }}"></span>
        </h2>

        <div class="flex flex-wrap items-center gap-2 text-sm">
          @if ($user->rank)
            <x-filament::badge color="gray">
              @if ($user->rank->image?->image_url)
                <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" class="mr-1 inline h-4" />
              @endif
              {{ $user->rank->name }}
            </x-filament::badge>
          @endif

          @if ($user->position)
            <x-filament::badge color="gray">{{ $user->position->name }}</x-filament::badge>
          @endif

          @if ($user->unit)
            <x-filament::badge color="gray">{{ $user->unit->name }}</x-filament::badge>
          @endif

          @if ($user->status)
            <x-filament::badge :color="Color::generateV3Palette($statusColor)">
              {{ $user->status->name }}
            </x-filament::badge>
          @endif

          @if (!$user->approved)
            <x-filament::badge color="warning" icon="heroicon-m-exclamation-circle">
              Pending Approval
            </x-filament::badge>
          @endif
        </div>
      </div>
    </div>

    <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-200 pt-4 text-sm dark:border-gray-700 sm:grid-cols-4">
      @php
        $stats = [
            ['label' => 'Time in Service', 'value' => $user->time_in_service?->forHumans(['parts' => 2, 'short' => true])],
            ['label' => 'Time in Grade', 'value' => $user->time_in_grade?->forHumans(['parts' => 2, 'short' => true])],
            ['label' => 'Time in Assignment', 'value' => $user->time_in_assignment?->forHumans(['parts' => 2, 'short' => true])],
            ['label' => 'Last Online', 'value' => $user->last_seen_at?->diffForHumans() ?? 'Never'],
        ];
      @endphp

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
  </div>
</div>
