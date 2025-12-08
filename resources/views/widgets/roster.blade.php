<x-layouts.widget>
  @if ($resourceId)
    @livewire('widgets.roster.create', ['record' => $resourceId])
  @else
    @livewire('widgets.roster.index')
  @endif
</x-layouts.widget>
