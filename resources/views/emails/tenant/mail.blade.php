@component('mail::message')
{{ $content }}

@foreach ($links as $text => $url)
  @component('mail::button', ['url' => $url])
    {{ $text }}
  @endcomponent
@endforeach

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
@endcomponent
