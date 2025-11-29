@component('mail::message')
# Account Deletion Warning (1 Month)

{{ __('It has been 1 month since you last logged in to your PERSCOM.io account. We will delete your account in 30 days if there is no new activity on your account. If this is a mistake, please reach out to our support team immediately.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
