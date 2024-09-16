@component('mail::message')
# Hello,

{{ __('A new rank record has been added to your personnel file.') }}

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

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
