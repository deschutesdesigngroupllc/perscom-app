<x-filament-panels::page>
  <div x-data="{ activeTab: 'tab0' }">
    <x-filament::tabs label="Group tabs">
      @forelse($data as $index => $group)
        <x-filament::tabs.item alpine-active="activeTab === 'tab{{ $index }}'" x-on:click="activeTab = 'tab{{ $index }}'"
          icon="{{ $group->icon }}">
          {{ $group->name }}
        </x-filament::tabs.item>
      @empty
        <x-filament::tabs.item>
          No groups found
        </x-filament::tabs.item>
      @endforelse
    </x-filament::tabs>

    @forelse($data as $index => $group)
      <div x-show="activeTab === 'tab{{ $index }}'" class="text-sm">
        @forelse($group->units as $unit)
          <div
            class="flex flex-row space-x-2 items-center justify-center bg-gray-50 dark:bg-gray-950 rounded-md first:rounded-t-none py-2 px-4 ">
            @if ($unit->icon)
              <x-filament::icon icon="{{ $unit->icon }}" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
            @endif
            <div class="text-center text-gray-950 dark:text-white font-semibold text-sm">{{ $unit->name }}</div>
          </div>
          <div class="py-1">
            @forelse($unit->users as $user)
              <a class="flex flex-row items-center space-x-2 sm:space-x-4 p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md"
                href="{{ \App\Filament\App\Resources\UserResource::getUrl('view', ['record' => $user]) }}" target="_blank">
                <div class="w-10 overflow-hidden">
                  @if ($user->rank->image ?? null)
                    <img src="{{ Storage::disk('s3')->url($user->rank->image->path) }}" alt="{{ $user->rank->name }}"
                      class="h-8 w-auto flex-grow-0">
                  @else
                    <div class="align-middle">
                      {{ $user->rank->abbreviation ?? 'RNK' }}
                    </div>
                  @endif
                </div>
                <div class="flex-1">
                  <div class="dark:text-white font-bold">{{ $user->name }}</div>
                  <div class="flex flex-col md:flex-row md:items-center md:space-x-2">
                    <div class="text-gray-700 dark:text-gray-400 font-medium">
                      {{ optional($user->specialty, fn($specialty) => "$specialty->abbreviation - $specialty->name") }}</div>
                    <div class="hidden md:block">
                      <svg viewBox="0 0 2 2" class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
                        <circle cx="1" cy="1" r="1" />
                      </svg>
                    </div>
                    <div class="text-gray-500">{{ $user->position->name ?? 'No Position' }}</div>
                  </div>
                </div>
                <div class="flex-0">
                  <div class="flex flex-row space-x-2">
                    <div class="hidden sm:flex">
                      @if ($user->online)
                        <x-filament::badge color="success">Online</x-filament::badge>
                      @else
                        <x-filament::badge color="info">Offline</x-filament::badge>
                      @endif
                    </div>
                    <x-filament::badge :color="\Filament\Support\Colors\Color::hex($user->status->color ?? '#2563eb')">{{ $user->status->name ?? 'No Status Set' }}</x-filament::badge>
                  </div>
                </div>
              </a>
            @empty
              <div class="flex flex-row items-center justify-center p-4">
                <div class="text-gray-700 dark:text-gray-400 font-normal">No personnel assigned to this unit.</div>
              </div>
            @endforelse
          </div>
        @empty
          <div class="flex flex-row items-center justify-center p-4">
            <div class="text-gray-700 dark:text-gray-400 font-normal">No units assigned to this group. Please <a
                href="{{ \App\Filament\App\Resources\UnitResource::getUrl('create') }}" target="_blank"
                class="underline font-semibold">create</a> one.</div>
          </div>
        @endforelse
      </div>
    @empty
      <div class="flex flex-row items-center justify-center p-4">
        <div class="text-gray-700 dark:text-gray-400 font-normal">No groups found. Please <a
            href="{{ \App\Filament\App\Resources\GroupResource::getUrl('create') }}" target="_blank"
            class="underline font-semibold">create</a> one.</div>
      </div>
    @endforelse
  </div>
</x-filament-panels::page>
