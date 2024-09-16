@component('mail::message')
# Hello,

{{ __('A new assignment record has been added to your personnel file.') }}

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

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
