<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Samarinda Properti') }}</title>

        @fonts
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

        <style>[x-cloak]{display:none!important}</style>

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

            @media print {
                nav, aside, header, footer, button, .btn, .print\:hidden, #clearCenter, #applyFilters, #layerControlPanel, .modal-overlay {
                    display: none !important;
                }
                .grid {
                    display: block !important;
                }
                main {
                    padding: 0 !important;
                    margin: 0 !important;
                    max-width: 100% !important;
                    width: 100% !important;
                }
                .card {
                    border: none !important;
                    box-shadow: none !important;
                    padding: 0 !important;
                    margin-bottom: 2rem !important;
                    page-break-inside: avoid;
                }
                #map, .leaflet-container {
                    height: 400px !important;
                    width: 100% !important;
                    page-break-inside: avoid;
                }
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @stack('styles')
    </head>
    <body>
        {{-- Loading Screen --}}
        <div id="global-loader" class="fixed inset-0 z-[99999] flex items-center justify-center bg-[#FAF7F2] transition-opacity duration-300">
            <div class="flex flex-col items-center justify-center">
                <div class="grid size-16 place-items-center rounded-2xl bg-[#0F4C5C] text-white shadow-lg shadow-[#0F4C5C]/20 animate-pulse">
                    <span class="text-2xl font-black tracking-wider font-display">SP</span>
                </div>
            </div>
        </div>

        <div class="min-h-dvh flex flex-col">
            <x-navbar />

            {{-- Floating Flash Notifications (Toast) --}}
            <div class="fixed top-24 right-6 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
                @if (session('success'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                        x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-12"
                        class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-xl border border-emerald-100 ring-1 ring-emerald-500/10"
                    >
                        <div class="flex items-center gap-3">
                            <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                                <i class="ti ti-circle-check text-xl"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-black text-slate-900 uppercase">Sukses</div>
                                <div class="text-[11px] text-slate-500 font-semibold mt-0.5 leading-normal">{{ session('success') }}</div>
                            </div>
                        </div>
                        <button @click="show = false" class="shrink-0 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="ti ti-x text-base"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                        x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-12"
                        class="pointer-events-auto flex items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow-xl border border-rose-100 ring-1 ring-rose-500/10"
                    >
                        <div class="flex items-center gap-3">
                            <span class="grid size-9 place-items-center rounded-xl bg-rose-50 text-rose-600">
                                <i class="ti ti-alert-circle text-xl"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-black text-slate-900 uppercase">Error</div>
                                <div class="text-[11px] text-slate-500 font-semibold mt-0.5 leading-normal">{{ session('error') }}</div>
                            </div>
                        </div>
                        <button @click="show = false" class="shrink-0 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <i class="ti ti-x text-base"></i>
                        </button>
                    </div>
                @endif
            </div>

            <main class="w-full px-6 py-6 sm:px-10 sm:py-8 lg:px-16 xl:px-24" style="padding-bottom: max(2rem, env(safe-area-inset-bottom))">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-slate-950 text-slate-400 mt-auto relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-brand-primary via-brand-accent to-brand-primary"></div>
                <div class="w-full px-6 py-12 sm:px-10 lg:px-16 xl:px-24">
                    <div class="grid gap-12 md:grid-cols-3">
                        {{-- Col 1: Brand Column --}}
                        <div class="space-y-4">
                            <a href="{{ route('home') }}" class="flex items-center shrink-0 group">
                                <img src="{{ asset('images/logo.png') }}" alt="Samarinda Properti Logo" class="h-16 w-auto brightness-0 invert transition-transform duration-300 group-hover:scale-105" />
                            </a>
                            <p class="text-xs leading-relaxed text-slate-400">
                                Platform pencarian properti pertama di Kota Samarinda yang mengintegrasikan informasi spasial geospasial dengan pemetaan wilayah bebas banjir secara real-time.
                            </p>
                        </div>

                        {{-- Col 2: Navigation & Features Combined Links --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 font-mono">Tautan</h3>
                                <ul class="space-y-2 text-xs font-medium">
                                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a></li>
                                    <li><a href="{{ route('properties.index') }}" class="hover:text-white transition">Katalog</a></li>
                                    <li><a href="{{ route('explore') }}" class="hover:text-white transition">Peta Eksplorasi</a></li>
                                    <li><a href="{{ route('seller.listings.create') }}" class="hover:text-white transition">Pasang Iklan</a></li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 font-mono">Fitur GIS</h3>
                                <ul class="space-y-2 text-xs font-medium text-slate-400">
                                    <li class="flex items-center gap-1.5">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Bebas Banjir</span>
                                    </li>
                                    <li class="flex items-center gap-1.5">
                                        <span class="size-1.5 rounded-full bg-brand-accent"></span>
                                        <span>Rute Terdekat</span>
                                    </li>
                                    <li class="flex items-center gap-1.5">
                                        <span class="size-1.5 rounded-full bg-blue-500"></span>
                                        <span>Administrasi</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Col 3: Contacts --}}
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono">Hubungi Kami</h3>
                            <ul class="space-y-2.5 text-xs font-semibold">
                                <li class="flex items-center gap-2">
                                    <i class="ti ti-map-pin text-slate-400 text-base"></i>
                                    <span>Samarinda, Kalimantan Timur</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ti ti-mail text-slate-400 text-base"></i>
                                    <span class="truncate">support@samarindaproperti.gis</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-[10px] text-slate-500 font-mono">&copy; {{ date('Y') }} Samarinda Properti GIS. Hak Cipta Dilindungi.</p>
                        <p class="text-[10px] text-slate-500 font-mono">Dibuat dengan dedikasi untuk kota Samarinda yang cerdas.</p>
                    </div>
                </div>
            </footer>
        </div>

        {{-- Scroll to Top Button --}}
        <button id="scrollToTopBtn" type="button" class="fixed bottom-6 right-6 z-[1000] size-10 rounded-full bg-brand-primary/90 backdrop-blur-md text-white shadow-lg shadow-brand-primary/20 flex items-center justify-center transition-all duration-300 opacity-0 translate-y-4 pointer-events-none hover:bg-brand-primary hover:-translate-y-1 hover:shadow-xl cursor-pointer border-0">
            <i class="ti ti-chevron-up text-lg"></i>
        </button>

        @stack('scripts')

        <script>
            const _loaderStartTime = Date.now();

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

            // Loading Screen Handler with Minimum Duration (500ms)
            window.addEventListener('load', function() {
                const elapsedTime = Date.now() - _loaderStartTime;
                const minDuration = 500;
                const remainingTime = Math.max(0, minDuration - elapsedTime);

                setTimeout(function() {
                    const loader = document.getElementById('global-loader');
                    if (loader) {
                        loader.classList.add('opacity-0', 'pointer-events-none');
                        setTimeout(() => loader.remove(), 350);
                    }
                }, remainingTime);
            });

            // Scroll to Top Handler
            document.addEventListener('DOMContentLoaded', function() {
                const scrollBtn = document.getElementById('scrollToTopBtn');
                if (scrollBtn) {
                    window.addEventListener('scroll', () => {
                        if (window.scrollY > 300) {
                            scrollBtn.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
                            scrollBtn.classList.add('opacity-100', 'translate-y-0');
                        } else {
                            scrollBtn.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
                            scrollBtn.classList.remove('opacity-100', 'translate-y-0');
                        }
                    });
                    scrollBtn.addEventListener('click', () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
            });
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    </body>
</html>
