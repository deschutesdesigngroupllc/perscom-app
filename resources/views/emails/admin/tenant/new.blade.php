@component('mail::message')
# New Tenant Created

{{ __('A new tenant has been created.') }}

**Organization**: {{ $organization }}<br>
**Email**: {{ $email }}<br>
**Domain**: [{{ $domain }}]({{ $domain }})

@component('mail::button', ['url' => $url])
  View Tenant
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
