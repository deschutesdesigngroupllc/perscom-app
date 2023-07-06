<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth bg-gray-100 antialiased">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0"/>
    <meta name="description" content="Streamline your personnel management & improve efficiency with mission-critical tools for police, fire, EMS, military & public safety." />

    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_TAG') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('GOOGLE_ANALYTICS_TAG') }}');
    </script>

    <title>PERSCOM Personnel Management System</title>

    @viteReactRefresh
    @vite('resources/js/app.jsx')
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
