<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth bg-gray-100 antialiased">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <title>PERSCOM Personnel Management System</title>

        @viteReactRefresh
        @vite('resources/js/app.js')
        @routes
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