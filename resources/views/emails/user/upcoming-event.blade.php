@component('mail::message')
# Upcoming Event

**Event**: {{ $name }}<br>
**Begins**: {{ $start }}

@component('mail::button', ['url' => $url])
  View Event
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
