@component('mail::message')
# Hello,

{{__('A new qualification record has been added to your personnel file.')}}

**Qualification**: {{ $qualification }}<br>
**Text**: {{ $text }}<br>
**Date**: {{ $date }}<br>

@component('mail::button', ['url' => $url])
    View Record
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
