@component('mail::message')
# Your Organization Domain Has Been Removed

{{__('We have removed a domain from your organization. You may now access your Dashboard using the URL below. If you have any questions, please reach out for additional support.')}}

**Dashboard URL**: [{{ $url }}]({{ $url }})<br>
**Domain Removed**: [{{ $removed_url }}]({{ $removed_url }})<br>

@component('mail::button', ['url' => $url])
    Go To Dashboard
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
