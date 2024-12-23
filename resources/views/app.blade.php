<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="keywords" content="@yield('keywords')" />
    <link rel="icon" href="{{ asset('storage/logos/logo_mini.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('storage/logos/logo_mini.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('storage/logos/logo_mini.webp') }}" type="image/webp">
    <link rel="icon" href="{{ asset('storage/logos/logo_mini.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('storage/logos/logo_mini.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('storage/logos/logo_mini.png') }}">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/views/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="antialiased w-screen h-screen" style="margin-bottom:0px!important;">
    @inertia
</body>

</html>
