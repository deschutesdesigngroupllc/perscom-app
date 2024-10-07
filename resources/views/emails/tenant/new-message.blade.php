@component('mail::message')
You received a new message.

{!! $message !!}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
