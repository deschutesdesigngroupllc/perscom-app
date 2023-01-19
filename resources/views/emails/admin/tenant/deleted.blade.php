@component('mail::message')
# Tenant Deleted

{{__('A tenant has been deleted.')}}

**Organization**: {{ $organization }}<br>
**Email**: {{ $email }}<br>
**Domain**: [{{ $domain }}]({{ $url }})

@component('mail::button', ['url' => $url])
    Go To Tenants
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
