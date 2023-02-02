@component('mail::message')
# Hello,

{{__('A new task has been assigned to you.')}}

**Task**: {{ $task }}<br>
**Due At**: {{ $due }}<br>
**Expires At**: {{ $expires }}<br>
**Assigned By**: {{ $assigned }}<br>
**Assigned**: {{ $date }}<br>

@component('mail::button', ['url' => $url])
    View Tasks
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
