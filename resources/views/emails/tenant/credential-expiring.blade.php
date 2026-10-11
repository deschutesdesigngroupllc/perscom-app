@component('mail::message')
# Hello,

@if ($daysUntilExpiry <= 0)
{{ __('One of your records has expired and needs to be renewed.') }}
@else
{{ __('One of your records is expiring soon.') }}
@endif

**{{ ucfirst($kind) }}**: {{ $name }}<br>
**{{ __('Expires') }}**: {{ $expiresAt }}<br>

@if ($url)
@component('mail::button', ['url' => $url])
  View Record
@endcomponent
@endif

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
