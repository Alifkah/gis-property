@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'ogImage' => null,
    'ogType' => 'website',
    'canonical' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ isset($title) ? $title . ' | ' . config('app.name', 'Samarinda Properti GIS') : config('app.name', 'Samarinda Properti GIS') }}</title>
        <meta name="description" content="{{ $description ?? 'Cari properti terbaik di Kota Samarinda dengan analisis geospasial rawan banjir dan fasilitas terdekat.' }}">
        <meta name="keywords" content="{{ $keywords ?? 'properti samarinda, rumah samarinda, sig properti, bebas banjir samarinda, kpr samarinda' }}">
        <link rel="canonical" href="{{ $canonical ?? request()->url() }}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:title" content="{{ $title ?? config('app.name', 'Samarinda Properti GIS') }}">
        <meta property="og:description" content="{{ $description ?? 'Cari properti terbaik di Kota Samarinda dengan analisis geospasial rawan banjir dan fasilitas terdekat.' }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:site_name" content="{{ config('app.name', 'Samarinda Properti GIS') }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title ?? config('app.name', 'Samarinda Properti GIS') }}">
        <meta name="twitter:description" content="{{ $description ?? 'Cari properti terbaik di Kota Samarinda dengan analisis geospasial rawan banjir dan fasilitas terdekat.' }}">
        <meta name="twitter:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">


        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @stack('styles')
    </head>
    <body class="overflow-hidden">
        {{ $slot }}

        @stack('scripts')
        <script>
            // Global Ctrl+K shortcut to focus search input
            document.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.getElementById('catalogSearchInput') 
                        || document.getElementById('mapSearchInput') 
                        || document.querySelector('input[name="q"]');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });
        </script>
    </body>
</html>

