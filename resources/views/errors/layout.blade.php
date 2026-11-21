@php
  $requestId = \Illuminate\Support\Facades\Context::get('request_id');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <title>@yield('title') · PERSCOM</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <style>
    :root {
      --ink: #09090B;
      --muted: #52525B;
      --surface: #FAFAFA;
      --card: #FFFFFF;
      --rule: #E4E4E7;
      --primary: #2563EB;
      --primary-ink: #FFFFFF;
      color-scheme: light dark;
    }

    @media (prefers-color-scheme: dark) {
      :root {
        --ink: #F4F4F5;
        --muted: #A1A1AA;
        --surface: #090B0F;
        --card: #18181B;
        --rule: #27272A;
        --primary: #60A5FA;
        --primary-ink: #0B1220;
      }

      #logo .s2 {
        fill: #E4C77F
      }
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px 16px;
      background: var(--surface);
      color: var(--ink);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
      line-height: 1.5;
    }

    main {
      width: 100%;
      max-width: 480px;
      background: var(--card);
      border: 1px solid var(--rule);
      border-radius: 12px;
      padding: 32px;
    }

    .logo {
      color: var(--ink);
      margin-bottom: 32px;
    }

    .code {
      margin: 0 0 4px;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--muted);
    }

    h1 {
      margin: 0 0 12px;
      font-family: 'Barlow Semi Condensed', Inter, ui-sans-serif, system-ui, sans-serif;
      font-size: 30px;
      font-weight: 700;
      line-height: 1.15;
    }

    p {
      margin: 0 0 24px;
      color: var(--muted);
      font-size: 16px;
    }

    .actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
    }

    .button {
      display: inline-flex;
      align-items: center;
      min-height: 44px;
      padding: 0 16px;
      border-radius: 6px;
      background: var(--primary);
      color: var(--primary-ink);
      font-weight: 600;
      text-decoration: none;
    }

    .link {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }

    .button:focus-visible,
    .link:focus-visible {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }

    .meta {
      margin-top: 32px;
      padding-top: 16px;
      border-top: 1px solid var(--rule);
      font-size: 12px;
      color: var(--muted);
      font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
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
    <div class="actions">
      @hasSection('action')
        @yield('action')
      @else
        <a class="button" href="{{ url('/') }}">Go to Dashboard</a>
      @endif
      <a class="link" href="https://docs.perscom.io" rel="noopener">Help Center</a>
      <a class="link" href="https://status.perscom.io" rel="noopener">System Status</a>
    </div>
    @if (filled($requestId))
      <div class="meta">Request ID: {{ $requestId }}</div>
    @endif
  </main>
</body>

</html>
