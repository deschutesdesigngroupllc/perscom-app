<div>
  @if ($mode === 'automatic')
    @include('components.roster.automatic', ['groups' => $groups])
  @else
    @include('components.roster.manual', ['groups' => $groups])
  @endif
</div>
