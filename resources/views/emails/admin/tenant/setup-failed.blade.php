@component('mail::message')
# Setup Failed for {{ $organization }}

The setup job for **{{ $organization }}** ({{ $email }}) failed. The founder was told setup is delayed and that the team is on it.

**Error**: {{ $error }}

@component('mail::button', ['url' => $url])
  Open Tenant and Retry
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
