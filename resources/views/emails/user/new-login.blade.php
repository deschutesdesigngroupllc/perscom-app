@component('mail::message')
# Your New Account Information

{{ __('Your new account information has been successfully set up and your account details are included below. Please reach out if you have any questions or need help getting started.') }}

**Dashboard URL**: [{{ $url }}]({{ $url }})<br>
**Email**: {{ $email }}<br>
**Password**: {{ $password }}

@component('mail::button', ['url' => $url])
  Go To Dashboard
@endcomponent

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
