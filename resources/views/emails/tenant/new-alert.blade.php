@component('mail::message')
# {{ $title }}

{!! $message !!}

@if($url)
  @component('mail::button', ['url' => $url])
    {{ $link }}
  @endcomponent
@endif

@include('emails.partials.sign-off')
@endcomponent
