@component('mail::message')
# Account Deletion Warning (1 Month)

{{ __('It has been 5 months since you last logged in to your PERSCOM.io account. We will delete your account in one month if there is no new activity on your account. If this is a mistake, please reach out to our support team immediately.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
