@php
  use Illuminate\Support\Str;

  $event = $this->getEvent();
  $briefing = filled($event->content) ? $event->content : $event->description;
@endphp

<x-filament::section icon="heroicon-o-document-text" heading="Briefing">
  @if (filled($briefing))
    <div class="fi-prose max-w-none">
      {!! Str::sanitizeHtml($briefing) !!}
    </div>
  @else
    <p class="text-sm text-gray-500 dark:text-gray-400">No briefing has been added for this event yet.</p>
  @endif
</x-filament::section>
