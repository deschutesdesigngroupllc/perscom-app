<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth bg-gray-50 antialiased">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <meta name="description"
    content="Mission-critical tools built specifically to meet the unique needs of police, fire, EMS, military, and public safety agencies. Optimize your agency's communications, streamline data management, and improve overall efficiency with PERSCOM.io." />
  <meta name="perscom_request_id" content="{{ \Illuminate\Support\Facades\Context::get('request_id') }}" />
  <meta name="perscom_trace_id" content="{{ \Illuminate\Support\Facades\Context::get('trace_id') }}" />

  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <title>PERSCOM Personnel Management System</title>

  <script src="https://cdn.jsdelivr.net/npm/@iframe-resizer/child@5.3.2" type="text/javascript" async></script>

  @googlefonts
  @vite(['resources/js/widgets/app.js', 'resources/css/widgets/app.css'])
  @filamentStyles
</head>

<body class="font-sans bg-gray-100">
  {{ $slot }}
  @filamentScripts
</body>

</html>
