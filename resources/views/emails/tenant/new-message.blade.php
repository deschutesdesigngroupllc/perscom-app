@component('mail::message')
You received a new message.

{!! $message !!}

@include('emails.partials.sign-off')
@endcomponent
