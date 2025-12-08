<x-layouts.widget>
  @if ($resourceId)
    @livewire('widgets.forms.create', ['record' => $resourceId])
  @else
    @livewire('widgets.forms.index')
  @endif
</x-layouts.widget>
