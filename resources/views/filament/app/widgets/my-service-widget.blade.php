@php
  $user = $this->user();
  $awardRecords = $this->awardRecords();
  $events = $this->upcomingEvents();
  $promotion = $this->promotion();
@endphp

<x-filament-widgets::widget>
  <x-filament::section icon="heroicon-o-identification" icon-color="primary">
    <x-slot name="heading">My Service</x-slot>
    <x-slot name="description">Your service record at a glance</x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <div class="flex flex-col gap-6">
        <section aria-labelledby="my-service-summary">
          <h3 id="my-service-summary" class="text-sm font-semibold text-gray-950 dark:text-white">Service Summary</h3>
          <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
              <dt class="text-gray-500 dark:text-gray-400">Rank</dt>
              <dd class="mt-1 flex items-center gap-1.5 font-medium text-gray-950 dark:text-white">
                @if ($user->rank?->image)
                  <img src="{{ $user->rank->image->image_url }}" alt="" class="h-5 w-auto">
                @endif
                {{ $user->rank->name ?? 'No Rank' }}
              </dd>
            </div>
            <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
              <dt class="text-gray-500 dark:text-gray-400">Unit</dt>
              <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $user->unit->name ?? 'Unassigned' }}</dd>
            </div>
            <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
              <dt class="text-gray-500 dark:text-gray-400">Position</dt>
              <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $user->position->name ?? 'Unassigned' }}</dd>
            </div>
            <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
              <dt class="text-gray-500 dark:text-gray-400">Time In Service</dt>
              <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $this->timeInService() ?? '—' }}</dd>
            </div>
            <div class="col-span-2 rounded-lg bg-gray-50 p-3 dark:bg-white/5">
              <dt class="text-gray-500 dark:text-gray-400">Time In Grade</dt>
              <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $this->timeInGrade() ?? '—' }}</dd>
            </div>
          </dl>
        </section>

        <section aria-labelledby="my-service-ribbons">
          <h3 id="my-service-ribbons" class="text-sm font-semibold text-gray-950 dark:text-white">Ribbon Rack</h3>
          @if ($awardRecords->isEmpty())
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">You haven't received any awards yet.</p>
          @else
            <div class="mt-3 flex flex-wrap gap-1.5">
              @foreach ($awardRecords as $record)
                @if ($record->award->image)
                  <img src="{{ $record->award->image->image_url }}" alt="{{ $record->award->name }}"
                    title="{{ $record->award->name }} ({{ $record->created_at?->toFormattedDateString() }})"
                    class="h-6 w-auto rounded-xs ring-1 ring-gray-950/10 dark:ring-white/10" loading="lazy">
                @else
                  <x-filament::badge color="gray" size="sm" :tooltip="$record->created_at?->toFormattedDateString()">
                    {{ $record->award->name }}
                  </x-filament::badge>
                @endif
              @endforeach
            </div>
          @endif
        </section>
      </div>

      <div class="flex flex-col gap-6">
        <section aria-labelledby="my-service-events">
          <h3 id="my-service-events" class="text-sm font-semibold text-gray-950 dark:text-white">Upcoming Events</h3>
          @if (blank($events))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">There are no upcoming events.</p>
          @else
            <ul class="mt-3 divide-y divide-gray-100 text-sm dark:divide-white/5">
              @foreach ($events as $item)
                <li class="flex items-center gap-3 py-2">
                  <div class="min-w-0 flex-1">
                    <div class="truncate font-medium text-gray-950 dark:text-white">{{ $item['event']->name }}</div>
                    <div class="text-gray-500 dark:text-gray-400">{{ $item['event']->starts?->toDayDateTimeString() }}</div>
                  </div>
                  @if ($item['status'])
                    <x-filament::badge :color="$item['status']->getColor()" size="sm">{{ $item['status']->getLabel() }}</x-filament::badge>
                  @else
                    <x-filament::badge color="gray" size="sm">Not Registered</x-filament::badge>
                  @endif
                  @if ($item['canCheckIn'])
                    <x-filament::button tag="a" :href="$item['checkInUrl']" size="xs" icon="heroicon-m-check-circle">
                      Check In
                    </x-filament::button>
                  @endif
                </li>
              @endforeach
            </ul>
          @endif
        </section>

        <section aria-labelledby="my-service-promotion">
          <h3 id="my-service-promotion" class="text-sm font-semibold text-gray-950 dark:text-white">Next Promotion</h3>
          @if ($promotion->nextRank && filled($promotion->requirements))
            <div class="mt-3 flex items-center justify-between gap-2 text-sm">
              <span class="text-gray-700 dark:text-gray-300">Progress toward <span
                  class="font-medium text-gray-950 dark:text-white">{{ $promotion->nextRank->name }}</span></span>
              @if ($promotion->eligible)
                <x-filament::badge color="success" icon="heroicon-m-chevron-double-up" size="sm">Eligible</x-filament::badge>
              @else
                <span class="text-gray-500 dark:text-gray-400">
                  {{ sprintf('%d of %d met', count($promotion->metRequirements()), count($promotion->requirements)) }}
                </span>
              @endif
            </div>
            <ul class="mt-2 flex flex-col gap-1.5 text-sm">
              @foreach ($promotion->requirements as $requirement)
                <li class="flex items-start gap-2">
                  <x-filament::icon :icon="$requirement->met ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'" @class([
                      'mt-0.5 h-4 w-4 shrink-0',
                      'text-success-600' => $requirement->met,
                      'text-gray-400' => !$requirement->met,
                  ]) />
                  <span>
                    <span class="font-medium text-gray-950 dark:text-white">{{ $requirement->label }}</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ $requirement->detail }}</span>
                  </span>
                </li>
              @endforeach
            </ul>
          @else
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
              {{ $promotion->reason ?? 'There are no promotion requirements to show.' }}</p>
          @endif
        </section>
      </div>
    </div>
  </x-filament::section>
</x-filament-widgets::widget>
