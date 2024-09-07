@component('mail::message')
  # Hello,

  {{ __('A new combat record has been added to your personnel file.') }}

  @if ($text)
    **Text**: {{ $text }}<br>
  @endif
  @if ($date)
    **Date**: {{ $date }}<br>
  @endif

  @component('mail::button', ['url' => $url])
    View Record
  @endcomponent

  {{ __('Thanks,') }}<br>
  {{ config('app.name') }}
@endcomponent
