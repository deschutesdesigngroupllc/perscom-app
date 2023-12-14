@component('mail::message')
# Account Approved

{{__('Your account has been approved.')}}

@component('mail::button', ['url' => $url])
Go To Dashboard
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
