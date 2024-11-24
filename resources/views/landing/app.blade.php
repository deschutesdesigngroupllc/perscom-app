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

  @googlefonts
  @viteReactRefresh
  {{ Vite::useHotFile('landing.hot')->useBuildDirectory('landing')->withEntryPoints(['resources/js/landing/app.jsx', 'resources/css/landing/app.css']) }}
  @routes
  @inertiaHead

  <style>
    #app {
      min-height: 100vh;
    }
  </style>
</head>

<body class="font-sans text-gray-700">
  @inertia
</body>

</html>
