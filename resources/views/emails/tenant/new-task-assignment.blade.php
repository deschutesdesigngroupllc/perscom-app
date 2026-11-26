@component('mail::message')
# You Have a New Task

A task was assigned to you.

@if ($task)
  **Task**: {{ $task }}<br>
@endif
@if ($due)
  **Due At**: {{ $due }}<br>
@endif
@if ($expires)
  **Expires At**: {{ $expires }}<br>
@endif
@if ($assigned)
  **Assigned By**: {{ $assigned }}<br>
@endif
@if ($date)
  **Assigned**: {{ $date }}<br>
@endif

@component('mail::button', ['url' => $url])
  View Tasks
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
