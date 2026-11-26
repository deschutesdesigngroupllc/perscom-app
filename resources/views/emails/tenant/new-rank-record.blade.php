@component('mail::message')
# {{ $rank ? 'New Rank: '.$rank : 'New Rank Record' }}

A rank record was added to your profile.

@if ($rank)
  **Rank**: {{ $rank }}<br>
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
