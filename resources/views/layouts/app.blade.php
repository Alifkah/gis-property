<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Samarinda Properti GIS') }}</title>

        @fonts
        <style>[x-cloak]{display:none!important}</style>
        <style>
            /* Div-based modal overlay — reliable centering via flexbox */
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
                animation: modalIn .18s ease;
            }
            @keyframes modalIn {
                from { opacity: 0; transform: scale(.95) translateY(8px); }
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
                @if (session('success'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-init="setTimeout(() => show = false, 4000)"
                        x-transition:leave="transition duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mb-6 flex items-center justify-between gap-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-200"
                    >
                        <div class="flex items-center gap-2">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button @click="show = false" class="shrink-0 text-emerald-500 hover:text-emerald-700">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-transition:leave="transition duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mb-6 flex items-center justify-between gap-3 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 ring-1 ring-rose-200"
                    >
                        <div class="flex items-center gap-2">
                            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" /><path d="M12 8v4m0 4h.01" stroke-linecap="round" />
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button @click="show = false" class="shrink-0 text-rose-400 hover:text-rose-600">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                @endif

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
            // Close on backdrop click
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('modal-overlay')) {
                    e.target.classList.remove('open');
                }
            });
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    </body>
</html>

