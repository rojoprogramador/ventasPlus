<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- App Debug Mode -->
        <meta name="app-debug" content="{{ config('app.debug') ? 'true' : 'false' }}">
        
        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        
        <!-- Debug Script (solo en desarrollo) -->
        @if(config('app.debug'))
            <script src="{{ asset('js/debug.js') }}"></script>
        @endif
        
        <!-- CSRF Handler Script (siempre incluido) -->
        <script src="{{ asset('js/csrf-handler.js') }}"></script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
