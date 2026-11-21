@component('mail::message')
# {{ $daysUntilExpiry <= 0 ? 'A Record Has Expired' : 'A Record Expires Soon' }}

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

@include('emails.partials.sign-off')
@endcomponent
