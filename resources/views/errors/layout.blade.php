@php
  $requestId = \Illuminate\Support\Facades\Context::get('request_id');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full antialiased">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>@yield('title') · PERSCOM</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  {{-- Follow the theme chosen in PERSCOM (light, dark or system), the same way the panels do. --}}
  <script>
    (() => {
      const theme = localStorage.getItem('theme') ?? 'system';
      const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
      document.documentElement.classList.toggle('dark', dark);
    })();
  </script>
  @googlefonts
  {{-- Self-contained so the page still renders when the error comes from a build or asset problem.
       Values mirror the app theme: Filament's simple page card, zinc neutrals and brand blue. --}}
  <style>
    :root {
      --page: #fafafa;
      --card: #ffffff;
      --ring: rgb(9 9 11 / 0.05);
      --heading: #09090b;
      --text: #71717a;
      --meta: #a1a1aa;
      --rule: #e4e4e7;
      --primary: #2563eb;
      --primary-hover: #3b82f6;
      --link: #52525b;
      --link-hover: #09090b;
    }

    .dark {
      --page: #09090b;
      --card: #18181b;
      --ring: rgb(255 255 255 / 0.1);
      --heading: #ffffff;
      --text: #a1a1aa;
      --meta: #71717a;
      --rule: rgb(255 255 255 / 0.1);
      --link: #a1a1aa;
      --link-hover: #ffffff;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--page);
      color: var(--heading);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
      font-size: 14px;
      line-height: 1.5;
    }

    main {
      width: 100%;
      max-width: 32rem;
      margin: 4rem 0;
      padding: 3rem 1.5rem;
      background: var(--card);
      box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05), 0 0 0 1px var(--ring);
      text-align: center;
    }

    @media (min-width: 640px) {
      main {
        border-radius: 0.75rem;
        padding: 3rem;
      }
    }

    .logo {
      display: flex;
      justify-content: center;
      margin-bottom: 1.5rem;
      color: var(--heading);
    }

    .logo svg {
      height: 1.75rem;
      width: auto;
    }

    .code {
      margin: 0 0 0.5rem;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--meta);
    }

    h1 {
      margin: 0;
      font-family: 'Barlow Semi Condensed', Inter, ui-sans-serif, system-ui, sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: 0.01em;
      line-height: 2rem;
    }

    p {
      margin: 0.5rem 0 0;
      color: var(--text);
    }

    .button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      margin-top: 1.5rem;
      padding: 0.5rem 0.75rem;
      border-radius: 0.5rem;
      background: var(--primary);
      color: #ffffff;
      font-size: 0.875rem;
      font-weight: 600;
      text-decoration: none;
      box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      transition: background-color 75ms;
    }

    .button:hover {
      background: var(--primary-hover);
    }

    .links {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1rem;
    }

    .links a {
      color: var(--link);
      font-size: 0.875rem;
      font-weight: 500;
      text-decoration: none;
    }

    .links a:hover {
      color: var(--link-hover);
      text-decoration: underline;
    }

    .button:focus-visible,
    .links a:focus-visible {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }

    .meta {
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 1px solid var(--rule);
      color: var(--meta);
      font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
      font-size: 0.75rem;
      word-break: break-all;
    }
  </style>
</head>

<body>
  <main>
    <div class="logo">
      <x-logo />
    </div>
    <p class="code">Error @yield('code')</p>
    <h1>@yield('heading')</h1>
    <p>@yield('message')</p>
    @hasSection('action')
      @yield('action')
    @else
      <a class="button" href="{{ url('/') }}">Go to Dashboard</a>
    @endif
    <div class="links">
      <a href="https://docs.perscom.io" rel="noopener">Help Center</a>
      <a href="https://status.perscom.io" rel="noopener">System Status</a>
    </div>
    @if (filled($requestId))
      <div class="meta">Request ID: {{ $requestId }}</div>
    @endif
  </main>
</body>

</html>
