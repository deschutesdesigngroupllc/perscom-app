@component('mail::message')
# Your New Organization Domain Has Been Added

{{__('You may now access your Dashboard using the URL below. You may also access your Dashboard using the fallback URL in the event your custom domain does not work. If you have any questions, please reach out for additional support.')}}

**Dashboard URL**: [{{ $url }}]({{ $url }})<br>
**Fallback URL**: [{{ $fallback_url }}]({{ $fallback_url }})<br>

@component('mail::button', ['url' => $url])
    Go To Dashboard
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
