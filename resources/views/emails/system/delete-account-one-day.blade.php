@component('mail::message')
# Account Deletion Warning (1 Day)

{{ __('It has been 2 months since you last logged in to your PERSCOM account. We will delete your account tomorrow if there is no new activity on your account. This is your final notice. If this is a mistake, please reach out to our support team immediately.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
