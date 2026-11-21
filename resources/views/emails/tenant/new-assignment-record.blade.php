@component('mail::message')
# New Assignment

An assignment record was added to your profile.

@if ($status)
  **Status**: {{ $status }}<br>
@endif
@if ($unit)
  **Unit**: {{ $unit }}<br>
@endif
@if ($position)
  **Position**: {{ $position }}<br>
@endif
@if ($specialty)
  **Specialty**: {{ $specialty }}<br>
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
