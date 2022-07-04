<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth bg-gray-100 antialiased">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet" />
        <script src="{{ mix('/js/app.js') }}" defer></script>
        <title>PERSCOM Soldier Management System</title>
        @inertiaHead
        <style>
            #app {
                height: 100%;
            }
        </style>
    </head>
    <body class="flex h-full flex-col">
        @inertia
    </body>
</html>