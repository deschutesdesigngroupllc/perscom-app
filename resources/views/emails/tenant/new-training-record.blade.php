@component('mail::message')
# Training Recorded

A training record was added to your profile.

@if ($event)
  **Event**: {{ $event }}<br>
@endif
@if ($instructor)
  **Instructor**: {{ $instructor }}<br>
@endif
@if ($date)
  **Date**: {{ $date }}<br>
@endif

@if ($text)
  {!! $text !!}<br>
@endif

@component('mail::button', ['url' => $url])
  View Record
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
