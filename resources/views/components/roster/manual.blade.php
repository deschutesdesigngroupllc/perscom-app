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
      <div x-show="activeTab === 'tab{{ $index }}'" class="text-sm mt-0.5">
        @forelse($group->units as $unit)
          @include('components.roster.unit-header', ['unit' => $unit])
          <div>
            @forelse($unit->slots as $slot)
              @include('components.roster.slot-header', ['slot' => $slot])
              @forelse($slot->users as $user)
                @include('components.roster.user', ['user' => $user])
              @empty
                <div class="flex items-start">
                  @include('components.roster.no-personnel-assigned', [
                      'structure' => 'slot',
                      'message' => $slot->empty,
                  ])
                </div>
              @endforelse
            @empty
              @include('components.roster.no-slots-found', [
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
