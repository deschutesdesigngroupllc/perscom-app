<x-filament-panels::page>
  <div x-data="{ activeTab: 'tab0' }">
    <x-filament::tabs label="Group tabs">
      @forelse($groups as $index => $group)
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

    @forelse($groups as $index => $group)
      <div x-show="activeTab === 'tab{{ $index }}'" class="text-sm">
        @forelse($group->units as $unit)
          @include('filament.app.pages.roster.components.unit-header', ['unit' => $unit])
          <div class="py-1">
            @forelse($unit->slots as $slot)
              <div class="font-bold">
                {{ $slot->name }}
              </div>
              @forelse($slot->assignment_records as $assignmentRecord)
                @include('filament.app.pages.roster.components.user', ['user' => $assignmentRecord->user])
              @empty
                <div class="flex items-start">
                  @include('filament.app.pages.roster.components.no-personnel-assigned', ['structure' => 'slot'])
                </div>
              @endforelse
            @empty
              @include('filament.app.pages.roster.components.no-slots-found')
            @endforelse
          </div>
        @empty
          @include('filament.app.pages.roster.components.no-units-found')
        @endforelse
      </div>
    @empty
      @include('filament.app.pages.roster.components.no-groups-found')
    @endforelse
  </div>
</x-filament-panels::page>
