@php
  $user = $this->user();
  $promotion = $this->promotion();
  $events = $this->upcomingEvents();
  $showsAwards = $this->showsAwards();
  $showsPromotion = $this->hasPromotionRequirements();
@endphp

<x-filament-widgets::widget>
  <div class="flex flex-col gap-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <x-filament::section icon="heroicon-o-chevron-double-up" icon-color="warning">
        <x-slot name="heading">Current Rank</x-slot>
        <x-slot name="description">{{ $this->timeInGrade() ? 'In grade for ' . $this->timeInGrade() : 'Your current rank' }}</x-slot>

        <div class="flex items-center gap-x-4">
          <div
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
            @if ($user->rank?->image?->image_url)
              <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" class="h-12 w-12 object-contain" />
            @else
              <x-heroicon-o-chevron-double-up class="h-7 w-7 text-gray-400 dark:text-gray-500" />
            @endif
          </div>

          <div class="min-w-0 flex-1">
            @if ($user->rank)
              <p class="truncate text-base font-semibold text-gray-950 dark:text-white">{{ $user->rank->name }}</p>
              <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                @if ($user->rank->abbreviation)
                  <x-filament::badge color="gray">{{ $user->rank->abbreviation }}</x-filament::badge>
                @endif
                @if ($user->rank->paygrade)
                  <x-filament::badge color="gray">{{ $user->rank->paygrade }}</x-filament::badge>
                @endif
                @if ($showsPromotion && $promotion->eligible)
                  <x-filament::badge color="success" icon="heroicon-m-chevron-double-up">Promotion Eligible</x-filament::badge>
                @endif
              </div>
            @else
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No rank assigned</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">Contact your administrator</p>
            @endif
          </div>
        </div>

        @if ($this->getRankRecordsUrl())
          <x-slot name="footer">
            <x-filament::link :href="$this->getRankRecordsUrl()" color="gray" icon="heroicon-m-clipboard-document-list" icon-position="before"
              size="sm">
              View Rank Records
            </x-filament::link>
          </x-slot>
        @endif
      </x-filament::section>

      <x-filament::section icon="heroicon-o-rectangle-stack" icon-color="info">
        <x-slot name="heading">Current Assignment</x-slot>
        <x-slot
          name="description">{{ $this->timeInAssignment() ? 'Assigned for ' . $this->timeInAssignment() : 'Your position & unit' }}</x-slot>

        <div class="flex items-center gap-x-4">
          <div
            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
            @if ($user->unit?->image?->image_url)
              <img src="{{ $user->unit->image->image_url }}" alt="{{ $user->unit->name }}" class="h-12 w-12 object-contain" />
            @else
              <x-heroicon-o-identification class="h-7 w-7 text-gray-400 dark:text-gray-500" />
            @endif
          </div>

          <div class="min-w-0 flex-1">
            @if ($user->position || $user->unit)
              @if ($user->position)
                <p class="truncate text-base font-semibold text-gray-950 dark:text-white">{{ $user->position->name }}</p>
              @endif
              @if ($user->unit)
                <p class="mt-0.5 truncate text-sm text-gray-500 dark:text-gray-400">{{ $user->unit->name }}</p>
              @endif
              @if ($user->specialty)
                <div class="mt-1.5">
                  <x-filament::badge color="gray">{{ $user->specialty->abbreviation ?: $user->specialty->name }}</x-filament::badge>
                </div>
              @endif
            @else
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No current assignment</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">Contact your administrator</p>
            @endif
          </div>
        </div>

        <x-slot name="footer">
          <div class="flex flex-wrap items-center justify-between gap-2">
            @if ($this->getAssignmentRecordsUrl())
              <x-filament::link :href="$this->getAssignmentRecordsUrl()" color="gray" icon="heroicon-m-clipboard-document-list" icon-position="before"
                size="sm">
                View Assignment Records
              </x-filament::link>
            @endif
            @if ($this->timeInService())
              <span class="text-xs text-gray-500 dark:text-gray-400">{{ 'In service for ' . $this->timeInService() }}</span>
            @endif
          </div>
        </x-slot>
      </x-filament::section>

      <x-filament::section icon="heroicon-o-calendar-days" icon-color="success">
        <x-slot name="heading">Upcoming Events</x-slot>
        <x-slot name="description">Your next scheduled events</x-slot>

        <div class="-mx-2 space-y-1">
          @forelse ($events as $item)
            <div class="flex items-center gap-x-3 rounded-lg px-2 py-1.5 transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
              <a href="{{ $this->getEventUrl($item['event']) }}" class="group flex min-w-0 flex-1 items-center gap-x-3">
                @if ($item['event']->image?->image_url)
                  <img src="{{ $item['event']->image->image_url }}" alt="{{ $item['event']->name }}"
                    class="h-9 w-9 shrink-0 rounded-md object-cover ring-1 ring-gray-200 dark:ring-gray-700" />
                @else
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
                    <x-heroicon-o-calendar class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                  </div>
                @endif

                <div class="min-w-0 flex-1">
                  <p
                    class="truncate text-sm font-medium text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                    {{ $item['event']->name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $item['event']->all_day ? $item['event']->starts?->format('M j, Y') : $item['event']->starts?->format('M j, Y · g:i A') }}
                  </p>
                </div>
              </a>

              @if ($item['canCheckIn'])
                <x-filament::button tag="a" :href="$item['checkInUrl']" size="xs" icon="heroicon-m-check-circle">
                  Check In
                </x-filament::button>
              @elseif ($item['status'])
                <x-filament::badge :color="$item['status']->getColor()" size="sm">{{ $item['status']->getLabel() }}</x-filament::badge>
              @endif
            </div>
          @empty
            <div class="px-2 py-4 text-center">
              <div
                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-50 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:ring-gray-700">
                <x-heroicon-o-calendar class="h-5 w-5 text-gray-400 dark:text-gray-500" />
              </div>
              <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">No upcoming events</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">Check back later</p>
            </div>
          @endforelse
        </div>

        <x-slot name="footer">
          <x-filament::link :href="$this->getEventsListUrl()" color="gray" icon="heroicon-m-calendar-days" icon-position="before" size="sm">
            View All Events
          </x-filament::link>
        </x-slot>
      </x-filament::section>
    </div>

    @if ($showsAwards || $showsPromotion)
      <div @class([
          'grid grid-cols-1 gap-6',
          'lg:grid-cols-2' => $showsAwards && $showsPromotion,
      ])>
        @if ($showsAwards)
          @php($awardRecords = $this->awardRecords())
          <x-filament::section icon="heroicon-o-trophy" icon-color="warning">
            <x-slot name="heading">Ribbon Rack</x-slot>
            <x-slot
              name="description">{{ $awardRecords->isEmpty() ? 'Awards you receive will appear here' : $awardRecords->count() . ' ' . Str::plural('award', $awardRecords->count()) }}</x-slot>

            @if ($awardRecords->isEmpty())
              <p class="text-sm text-gray-500 dark:text-gray-400">You haven't received any awards yet.</p>
            @else
              <div class="flex flex-wrap gap-1.5">
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

            @if ($this->getAwardRecordsUrl())
              <x-slot name="footer">
                <x-filament::link :href="$this->getAwardRecordsUrl()" color="gray" icon="heroicon-m-clipboard-document-list" icon-position="before"
                  size="sm">
                  View Award Records
                </x-filament::link>
              </x-slot>
            @endif
          </x-filament::section>
        @endif

        @if ($showsPromotion)
          <x-filament::section icon="heroicon-o-arrow-trending-up" icon-color="success">
            <x-slot name="heading">Next Promotion</x-slot>
            <x-slot name="description">{{ 'Progress toward ' . $promotion->nextRank->name }}</x-slot>

            <x-slot name="afterHeader">
              @if ($promotion->eligible)
                <x-filament::badge color="success" icon="heroicon-m-chevron-double-up">Eligible</x-filament::badge>
              @else
                <x-filament::badge
                  color="gray">{{ sprintf('%d of %d met', count($promotion->metRequirements()), count($promotion->requirements)) }}</x-filament::badge>
              @endif
            </x-slot>

            <ul class="flex flex-col gap-1.5 text-sm">
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
          </x-filament::section>
        @endif
      </div>
    @endif
  </div>
</x-filament-widgets::widget>
