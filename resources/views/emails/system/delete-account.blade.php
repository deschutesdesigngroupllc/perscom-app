@component('mail::message')
# Your Account Has Been Deleted

{{__('It has been 6 months since you last logged in to your PERSCOM.io account. We have since removed your account and it can no longer be accessed. If you would like to start using PERSCOM.io again, please register and sign up for a new account.')}}

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
