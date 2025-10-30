@component('mail::message')
# Your Organization Is Now Ready

{{ __('Your organization has been successfully set up! Your account details are included below. If you have any questions or need help getting started, please don’t hesitate to reach out.') }}

**Dashboard URL**: [{{ $url }}]({{ $url }})<br>
**Email**: {{ $email }}<br>
**Password**: {{ $password }}

@component('mail::button', ['url' => $url])
  Go To Dashboard
@endcomponent

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
