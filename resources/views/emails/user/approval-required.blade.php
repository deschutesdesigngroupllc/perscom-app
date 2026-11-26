@component('mail::message')
# Admin Approval Required

{{ __('Your new account has been successfully created but requires admin approval before logging in. Please wait for your account to be approved before logging in.') }}

@include('emails.partials.sign-off')
@endcomponent
