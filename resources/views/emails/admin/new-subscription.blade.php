@component('mail::message')
# New Subscription

{{__('A new subscription has been created.')}}

**Tenant**: {{ $tenant }}<br>
**Plan**: {{ $plan }}<br>
**Term**: {{ $interval }}<br>

@component('mail::button', ['url' => $url])
View Tenant
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
