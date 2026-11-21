@component('mail::message')
# Tenant Deleted

{{ __('A tenant has been deleted.') }}

**Organization**: {{ $organization }}<br>
**Email**: {{ $email }}<br>

@component('mail::button', ['url' => $url])
  Go To Tenants
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
