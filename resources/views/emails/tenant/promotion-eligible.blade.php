@component('mail::message')
# You're Eligible for Promotion

You now meet the requirements for promotion to **{{ $rank }}**.

Your leadership can see your eligibility on the promotion board.

@include('emails.partials.sign-off')
@endcomponent
