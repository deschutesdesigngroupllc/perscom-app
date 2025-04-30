<x-widgets.layout>
  @if ($resourceId)
    @livewire('widgets.forms.create', ['record' => $resourceId])
  @else
    @livewire('widgets.forms.index')
  @endif
</x-widgets.layout>
