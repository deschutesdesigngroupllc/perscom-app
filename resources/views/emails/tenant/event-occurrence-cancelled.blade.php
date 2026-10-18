@component('mail::message')
# Event Cancelled

The following event date you planned to attend has been cancelled.

**Event**: {{ $name }}<br>
**Date**: {{ $date }}<br>
@if ($reason)
  **Reason**: {{ $reason }}<br>
@endif

@component('mail::button', ['url' => $url])
  View Event
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
