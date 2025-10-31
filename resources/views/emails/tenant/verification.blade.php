@component('mail::message')
# Verify Your Organization Registration

{{ __('Thank you for registering your organization with us!') }}

{{ __('Please click the button below to verify your email address and complete your registration. This link is only valid for 24 hours.') }}

{{ __('Once verification is complete, we will begin setting up your organization. You’ll receive a second email with your new account details once the process is finished.') }}

@component('mail::button', ['url' => $verificationUrl])
Verify Email Address
@endcomponent

{{ __('If you did not create an account, no further action is required.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
