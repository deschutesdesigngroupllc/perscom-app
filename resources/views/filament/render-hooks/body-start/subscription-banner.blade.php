@if (!App::isAdmin() && !App::isDemo())
  @livewire('app.subscription-banner')
@endif
