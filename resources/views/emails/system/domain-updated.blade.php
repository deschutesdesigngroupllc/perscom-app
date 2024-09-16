@component('mail::message')
# Your Organization Domain Has Been Updated

{{ __('You may now access your Dashboard using the URL below. If you have any questions, please reach out for additional support.') }}

**Dashboard URL**: [{{ $url }}]({{ $url }})<br>
@if ($show_fallback)
  **Fallback URL**: [{{ $fallback_url }}]({{ $fallback_url }})<br>
@endif

@component('mail::button', ['url' => $url])
  Go To Dashboard
@endcomponent

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
