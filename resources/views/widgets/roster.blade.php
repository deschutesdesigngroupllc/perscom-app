<x-widgets.layout>
  @if ($resourceId)
    @livewire('widgets.roster.create', ['record' => $resourceId])
  @else
    @livewire('widgets.roster.index')
  @endif
</x-widgets.layout>
