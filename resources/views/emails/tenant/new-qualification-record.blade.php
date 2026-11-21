@component('mail::message')
# {{ $qualification ? 'New Qualification: '.$qualification : 'New Qualification' }}

A qualification was added to your profile.

@if ($qualification)
  **Qualification**: {{ $qualification }}<br>
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
