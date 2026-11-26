@component('mail::message')
# {{ $organization }} Will Be Deleted Tomorrow

This is your final notice. No one has signed in to **{{ $organization }}** for two months, so it will be deleted on **{{ $deletionDate }}** along with all of its records.

@component('mail::button', ['url' => $url])
  Sign In to Keep {{ $organization }}
@endcomponent

If this is a mistake, reply to this email right away.

@include('emails.partials.sign-off')
@endcomponent
