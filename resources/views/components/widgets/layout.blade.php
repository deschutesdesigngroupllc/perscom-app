<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth bg-gray-50 antialiased">

<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <meta name="description"
    content="Mission-critical tools built specifically to meet the unique needs of police, fire, EMS, military, and public safety agencies. Optimize your agency's communications, streamline data management, and improve overall efficiency with PERSCOM.io." />
  <meta name="perscom_request_id" content="{{ \Illuminate\Support\Facades\Context::get('request_id') }}" />
  <meta name="perscom_trace_id" content="{{ \Illuminate\Support\Facades\Context::get('trace_id') }}" />

  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <title>PERSCOM Personnel Management System</title>

  @php
    $apiKey = request()->input('apikey') ?? request()->bearerToken()
  @endphp

  <script>
    document.addEventListener('livewire:init', () => {
      Livewire.hook('request', ({ options}) => {
        @if(filled($apiKey))
          options.headers = {
            'Authorization': 'Bearer {{$apiKey}}'
          }
        @endif
      })
    })
  </script>

  @googlefonts
  @vite(['resources/js/widgets/app.js', 'resources/css/widgets/app.css'])
  @filamentStyles
</head>

<body class="font-sans bg-transparent">
  <div class="m-4">
    {{ $slot }}
  </div>
  @filamentScripts
</body>

</html>
