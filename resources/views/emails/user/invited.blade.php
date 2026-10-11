@component('mail::message')
# {{ __('You\'re Invited') }}

@if ($inviter)
{{ __(':inviter has invited you to join :organization.', ['inviter' => $inviter, 'organization' => $organization]) }}
@else
{{ __('You have been invited to join :organization.', ['organization' => $organization]) }}
@endif

@if ($claim)
{{ __('A personnel profile has already been created for you. Accept the invitation below to set your password and take ownership of your profile.') }}
@else
{{ __('Accept the invitation below to create your account and set up your profile.') }}
@endif

@component('mail::button', ['url' => $url])
  {{ $claim ? __('Claim Profile') : __('Accept Invitation') }}
@endcomponent

@if ($expires)
{{ __('This invitation will expire on :date.', ['date' => $expires]) }}
@endif

{{ __('If you were not expecting this invitation, you can safely ignore this email.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
