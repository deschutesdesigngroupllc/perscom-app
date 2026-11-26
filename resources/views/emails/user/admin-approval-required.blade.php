@component('mail::message')
# User Approval Required

{{ __('A user account requires approval.') }}

**User**: {{ $user }}<br>
**Email**: {{ $email }}

@component('mail::button', ['url' => $url])
  Approve Account
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
