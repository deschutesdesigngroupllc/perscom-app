@props(['user', 'mode' => 'automatic'])

<a class="flex flex-row items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
  href="{{ \App\Filament\App\Resources\UserResource::getUrl('view', ['record' => $user]) }}" target="_blank">
  @if (!in_array('rank_id', $hiddenFields))
    <div class="w-10 overflow-hidden">
      @if ($user->rank->image ?? null)
        <img src="{{ Storage::url($user->rank->image->path) }}" alt="{{ $user->rank->name }}" class="h-8 w-auto flex-grow-0">
      @else
        <div class="align-middle">
          {{ $user->rank->abbreviation ?? 'RNK' }}
        </div>
      @endif
    </div>
  @endif
  <div class="flex-1">
    @if (!in_array('name', $hiddenFields))
      <div class="dark:text-white font-bold">{{ $user->name }}</div>
    @endif
    @if ($mode === 'automatic')
      <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
        @if (!in_array('specialty_id', $hiddenFields))
          @if ($specialty = $user->specialty)
            <div class="text-gray-700 dark:text-gray-400 font-medium">
              {{ $specialty->abbreviation }} - {{ $specialty->name }}</div>
            @if (!in_array('position_id', $hiddenFields) && $user->position)
              <div class="hidden md:block">
                <svg viewBox="0 0 2 2" class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
                  <circle cx="1" cy="1" r="1" />
                </svg>
              </div>
            @endif
          @endif
        @endif
        @if (!in_array('position_id', $hiddenFields) && ($position = $user->position))
          <div class="text-gray-500">{{ $position->name }}</div>
        @endif
      </div>
    @endif
  </div>
  <div class="flex-0">
    <div class="flex flex-row space-x-2">
      @if (!in_array('online', $hiddenFields))
        <div class="hidden sm:flex">
          @if ($user->online)
            <x-filament::badge color="success">Online</x-filament::badge>
          @else
            <x-filament::badge color="info">Offline</x-filament::badge>
          @endif
        </div>
      @endif
      @if (!in_array('status_id', $hiddenFields))
        <x-filament::badge :color="\Filament\Support\Colors\Color::hex($user->status->color ?? '#2563eb')">{{ $user->status->name ?? 'No Status Set' }}</x-filament::badge>
      @endif
    </div>
  </div>
</a>
