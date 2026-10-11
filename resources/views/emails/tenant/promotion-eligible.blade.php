@component('mail::message')
# Hello,

You now meet the requirements for promotion to **{{ $rank }}**.

Your leadership can see your eligibility on the promotion board.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
