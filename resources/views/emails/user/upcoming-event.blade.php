@component('mail::message')
# Upcoming Event

{{ $title }}.

**Event**: {{ $name }}<br>
**Begins**: {{ $start }}

@component('mail::button', ['url' => $url])
  View Event
@endcomponent

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
