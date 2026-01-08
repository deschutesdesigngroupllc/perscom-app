@component('mail::message')
# Account Deletion Warning (1 Week)

{{ __('It has been nearly 2 months since you last logged in to your PERSCOM account. We will delete your account in 1 week if there is no new activity on your account. If this is a mistake, please reach out to our support team immediately.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
