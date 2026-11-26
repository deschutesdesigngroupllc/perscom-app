@component('mail::message')
# New Service Record

A service record was added to your profile.

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
