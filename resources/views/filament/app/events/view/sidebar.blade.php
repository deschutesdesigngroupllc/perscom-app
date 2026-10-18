@php
  use App\Services\UserSettingsService;

  $event = $this->getEvent();
  $occurrence = $this->getSelectedOccurrence();
  $repeatRule = $this->getRepeatRule();
  $duration = $occurrence ? $this->formatDuration($occurrence) : null;
  $externalUrl = $this->getExternalUrl();
  $author = $event->author;
  $attendees = $this->getAttendees();
  $lists = [
      'going' => ['label' => 'Going', 'empty' => 'No one has said they are going yet.'],
      'maybe' => ['label' => 'Maybe', 'empty' => 'No one has said maybe yet.'],
      'not_going' => ['label' => "Can't Go", 'empty' => "No one has said they can't go."],
  ];
@endphp

<div class="flex flex-col gap-6">
  <x-filament::section icon="heroicon-o-clock" heading="When" compact>
    <dl class="flex flex-col gap-3 text-sm">
      @if ($occurrence)
        <div>
          <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Date</dt>
          <dd class="font-medium text-gray-950 dark:text-white">{{ $occurrence->localStarts()->format('l, F j, Y') }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Time</dt>
          <dd class="text-gray-950 dark:text-white">{{ $this->formatTimeRange($occurrence) }}</dd>
        </div>
      @endif
      @if (filled($duration))
        <div>
          <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Duration</dt>
          <dd class="text-gray-950 dark:text-white">{{ $duration }}</dd>
        </div>
      @endif
      @if (filled($repeatRule))
        <div>
          <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Repeats</dt>
          <dd class="text-gray-950 dark:text-white">{{ $repeatRule }}</dd>
        </div>
      @endif
      <div>
        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">RSVP Deadline</dt>
        <dd class="text-gray-950 dark:text-white">
          @if (!$event->registration_enabled)
            RSVPs are not being collected
          @elseif (filled($event->registration_deadline))
            {{ $event->registration_deadline->copy()->setTimezone(UserSettingsService::timezone())->format('M j, Y g:i A T') }}
            @if ($event->registration_deadline->isPast())
              <x-filament::badge color="danger" size="sm" class="ms-1 inline-flex">Closed</x-filament::badge>
            @endif
          @else
            No deadline
          @endif
        </dd>
      </div>
    </dl>
  </x-filament::section>

  @if (filled($event->location) || filled($externalUrl))
    <x-filament::section icon="heroicon-o-map-pin" heading="Where" compact>
      <div class="flex flex-col gap-3 text-sm">
        @if (filled($event->location))
          <p class="whitespace-pre-line text-gray-950 dark:text-white">{{ $event->location }}</p>
          @include('filament.app.infolists.event-map', ['location' => $event->location])
        @endif
        @if (filled($externalUrl))
          <x-filament::link :href="$externalUrl" target="_blank" rel="noopener" icon="heroicon-m-arrow-top-right-on-square"
            icon-position="after" size="sm" class="self-start">
            Open event link
          </x-filament::link>
        @endif
      </div>
    </x-filament::section>
  @endif

  @if ($author)
    <x-filament::section icon="heroicon-o-user" heading="Host" compact>
      <div class="flex items-center gap-3">
        <x-filament::avatar :src="$author->profile_photo_url" alt="" size="lg" loading="lazy" />
        <div class="min-w-0">
          <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">{{ $author->display_name }}</p>
          @if ($author->rank)
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $author->rank->name }}</p>
          @endif
        </div>
      </div>
    </x-filament::section>
  @endif

  @if ($event->registration_enabled || collect($attendees)->flatten()->isNotEmpty())
    <div x-data="{ attendeeTab: 'going' }">
      <x-filament::section icon="heroicon-o-user-group" heading="Who's Going" compact>
        <x-filament::tabs label="Responses" class="-mx-2 -mt-2 mb-3" role="tablist">
          @foreach ($lists as $key => $list)
            <x-filament::tabs.item :alpine-active="'attendeeTab === \'' . $key . '\''" x-on:click="attendeeTab = '{{ $key }}'" role="tab"
              x-bind:aria-selected="attendeeTab === '{{ $key }}' ? 'true' : 'false'" :badge="$attendees[$key]->count()">
              {{ $list['label'] }}
            </x-filament::tabs.item>
          @endforeach
        </x-filament::tabs>

        @foreach ($lists as $key => $list)
          <div x-show="attendeeTab === '{{ $key }}'" @if ($key !== 'going') x-cloak @endif role="tabpanel"
            aria-label="{{ $list['label'] }}">
            @if ($attendees[$key]->isEmpty())
              <p class="py-2 text-sm text-gray-500 dark:text-gray-400">{{ $list['empty'] }}</p>
            @else
              <ul class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                @foreach ($attendees[$key] as $attendee)
                  <li class="flex min-w-0 items-center gap-2">
                    <x-filament::avatar :src="$attendee->profile_photo_url" alt="" class="shrink-0" loading="lazy" />
                    <span class="truncate text-sm text-gray-950 dark:text-white">{{ $attendee->display_name }}</span>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        @endforeach
      </x-filament::section>
    </div>
  @endif
</div>
