@component('mail::message')
# {{ $organization }} Is Taking Longer to Set Up

We hit a problem while setting up **{{ $organization }}**. Our team has been notified and will finish the setup for you, usually within a few hours.

As soon as it's ready, we'll email you a link to set your password and sign in. If you need it sooner, just reply to this email.

@include('emails.partials.sign-off')
@endcomponent
