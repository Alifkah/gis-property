<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Samarinda Properti GIS') }}</title>

        @fonts

        <style>
            /* Modal overlay — reliable centering via flexbox */
            .modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .modal-overlay.open { display: flex; }
            .modal-box {
                background: #ffffff;
                border-radius: 1.5rem;
                padding: 1.5rem;
                width: 100%;
                max-width: 28rem;
                box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.35);
                animation: modalIn .15s ease;
            }
            @keyframes modalIn {
                from { opacity: 0; transform: scale(.96) translateY(6px); }
                to   { opacity: 1; transform: scale(1) translateY(0); }
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @stack('styles')
    </head>
    <body>
        <div class="min-h-dvh">
            <x-navbar />

            <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')

        <script>
            function openModal(id) {
                var el = document.getElementById(id);
                if (el) { el.classList.add('open'); }
            }
            function closeModal(id) {
                var el = document.getElementById(id);
                if (el) { el.classList.remove('open'); }
            }
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('modal-overlay')) {
                    e.target.classList.remove('open');
                }
            });
        </script>
    </body>
</html>

