@component('mail::message')
# Hello,

{{__("A new {$submission->form?->name} has been submitted.")}}

{!! $submission->toHtml() !!}

@component('mail::button', ['url' => $url])
View Submission
@endcomponent

{{__('Thanks,')}}<br>
{{ config('app.name') }}
@endcomponent
