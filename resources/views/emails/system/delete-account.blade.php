@component('mail::message')
# {{ $organization }} Has Been Deleted

No one signed in to **{{ $organization }}** for two months, so the organization and its records have been removed.

You're welcome back any time. You can create a new organization at [perscom.io](https://perscom.io).

@include('emails.partials.sign-off')
@endcomponent
