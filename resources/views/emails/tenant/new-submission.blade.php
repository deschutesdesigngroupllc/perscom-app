@component('mail::message')
# New Submission: {{ $submission->form?->name ?? 'Form' }}

A new form submission is ready for review.

{!! $submission->toHtml() !!}

@component('mail::button', ['url' => $url])
  View Submission
@endcomponent

@include('emails.partials.sign-off')
@endcomponent
