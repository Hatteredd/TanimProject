<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tanim — Philippine Agricultural Marketplace')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|outfit:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        @php
            $fallbackCss = file_get_contents(resource_path('css/app.css')) ?: '';
            $fallbackCss = preg_replace('/^\s*@import\s+"tailwindcss";\s*$/m', '', $fallbackCss);
        @endphp
        <style>{!! $fallbackCss !!}</style>
    @endif
</head>
<body class="transition-theme">
    <x-navbar />
    <main>
        @yield('content')
    </main>
</body>
</html>
