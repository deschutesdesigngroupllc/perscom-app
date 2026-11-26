@component('mail::message')
# {{ $award ? 'Congratulations on the '.$award : 'You Received an Award' }}

An award was added to your profile.

@if ($award)
  **Award**: {{ $award }}<br>
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
