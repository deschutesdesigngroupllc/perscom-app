<div x-data="{ activeTab: 'tab0' }">
  <x-filament::tabs label="Group tabs">
    @forelse($groups as $index => $group)
      <x-filament::tabs.item alpine-active="activeTab === 'tab{{ $index }}'" x-on:click="activeTab = 'tab{{ $index }}'"
        icon="{{ $group->icon }}">
        {{ $group->name }}
      </x-filament::tabs.item>
    @empty
      <x-filament::tabs.item>
        No Groups
      </x-filament::tabs.item>
    @endforelse
  </x-filament::tabs>

  @forelse($groups as $index => $group)
    <div x-show="activeTab === 'tab{{ $index }}'" class="text-sm rounded-xl mt-0.5">
      @forelse($group->units as $unit)
        @include('components.roster.unit-header', ['unit' => $unit])
        <div
          class="my-1 rounded-xl shadow-xs ring-1 ring-gray-950/5 dark:ring-white/5 bg-white dark:bg-gray-900 ring-inset inset-2 overflow-hidden">
          @forelse($unit->users as $user)
            @include('components.roster.user', ['user' => $user])
          @empty
            @include('components.roster.no-personnel-assigned', [
                'structure' => 'unit',
                'message' => $unit->empty,
            ])
          @endforelse
        </div>
      @empty
        @include('components.roster.no-units-found', [
            'message' => $group->empty,
        ])
      @endforelse
    </div>
  @empty
    @include('components.roster.no-groups-found')
  @endforelse
</div>
