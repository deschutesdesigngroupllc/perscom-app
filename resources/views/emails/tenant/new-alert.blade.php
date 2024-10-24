@component('mail::message')
# {{ $title }}

{!! $message !!}

@if($url)
  @component('mail::button', ['url' => $url])
    {{ $link }}
  @endcomponent
@endif

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
