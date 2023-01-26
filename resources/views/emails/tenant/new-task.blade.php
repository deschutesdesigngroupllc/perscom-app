@component('mail::message')
# Hello,

{{__('A new task has been assigned to you.')}}

**Task**: {{ $task }}<br>
**Due At**: {{ $due }}<br>
**Assigned By**: {{ $assigned }}

@component('mail::button', ['url' => $url])
    View Tasks
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
