@component('mail::message')
# {{ $organization }} Will Be Deleted in 30 Days

No one has signed in to **{{ $organization }}** for a month. To keep your organization and its records, sign in before **{{ $deletionDate }}**.

@component('mail::button', ['url' => $url])
  Sign In to Keep {{ $organization }}
@endcomponent

If you no longer need it, you don't have to do anything. Questions? Reply to this email.

@include('emails.partials.sign-off')
@endcomponent
