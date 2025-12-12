@component('mail::message')
# API Key Expiration Reminder

{{ __('This is a reminder that one of your API keys will be expiring soon.') }}

**API Key**: {{ $name }}<br>
**Expiration Date**: {{ $expires_at->format('F j, Y \a\t g:i A T') }}

{{ __('To ensure uninterrupted access to the API, please generate a new API key before the expiration date. You can manage your API keys from your account settings.') }}

@component('mail::panel')
{{ __('What happens when my API key expires?') }}

{{ __('Once your API key expires, any requests using this key will be rejected. Make sure to update your applications with a new API key before the expiration date.') }}
@endcomponent

@component('mail::button', ['url' => config('app.url')])
Manage API Keys
@endcomponent

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
