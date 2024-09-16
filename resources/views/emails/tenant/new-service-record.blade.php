@component('mail::message')
# Hello,

{{ __('A new service record has been added to your personnel file.') }}

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
