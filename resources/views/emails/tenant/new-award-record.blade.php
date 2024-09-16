@component('mail::message')
# Hello,

{{ __('A new award record has been added to your personnel file.') }}

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

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
