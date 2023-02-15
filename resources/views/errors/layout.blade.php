<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    @vite('resources/js/app.js')
</head>
<body class="h-full">
<div class="min-h-full pt-16 pb-12 flex flex-col bg-white">
    <main class="flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex-shrink-0 flex justify-center">
            <a href="/" class="inline-flex">
                <span class="sr-only">PERSCOM</span>
                <img class="h-12 w-auto"
                     src="{{ \Illuminate\Support\Facades\Vite::asset('resources/svg/logo.svg') }}" alt="">
            </a>
        </div>
        <div class="py-16">
            <div class="text-center">
                <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">@yield('code') error</p>
                <h1 class="mt-2 text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">@yield('header')</h1>
                <p class="mt-2 text-base text-gray-600">@yield('message')</p>
                @if(!isset($showLink) || $showLink)
                    <div class="mt-6">
                        <a href="{{ route('web.landing.home') }}"
                           class="text-base font-medium text-blue-600 hover:text-blue-600">Go back home<span
                                aria-hidden="true"> &rarr;</span></a>
                    </div>
                @endif
                @yield('extra')
            </div>
        </div>
    </main>
    <footer class="flex-shrink-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex flex-col sm:flex-row justify-center items-center space-x-4 text-sm">
            <a href="https://community.deschutesdesigngroup.com/"
               class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100 hover:text-gray-800" target='_blank'>Community Forums</a>
            <span class="inline-block border-l border-gray-300" aria-hidden="true"></span>
            <a href="https://docs.perscom.io"
               class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100 hover:text-gray-800" target='_blank'>Documentation</a>
            <span class="inline-block border-l border-gray-300" aria-hidden="true"></span>
            <a href="https://support.deschutesdesigngroup.com/"
               class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100 hover:text-gray-800" target='_blank'>Help Desk</a>
            <span class="inline-block border-l border-gray-300" aria-hidden="true"></span>
            <a href="https://support.deschutesdesigngroup.com/hc/en-us/requests/new"
               class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100 hover:text-gray-800" target='_blank'>Submit A Ticket</a>
        </nav>
    </footer>
</div>
</body>
</html>
