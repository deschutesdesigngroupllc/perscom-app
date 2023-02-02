@component('mail::message')
# Hello,

{{__('A new assignment record has been added to your personnel file.')}}

**Unit**: {{ $unit }}<br>
**Position**: {{ $position }}<br>
**Specialty**: {{ $specialty }}<br>
**Text**: {{ $text }}<br>
**Date**: {{ $date }}<br>

@component('mail::button', ['url' => $url])
    View Record
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
