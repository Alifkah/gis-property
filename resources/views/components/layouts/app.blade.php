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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                /* Hide headers, footers, navigation, sidebars, buttons */
                nav, aside, header, footer, button, .btn, .print\:hidden, #clearCenter, #applyFilters, #layerControlPanel, .modal-overlay {
                    display: none !important;
                }
                
                /* Reset grid columns to stack nicely */
                .grid {
                    display: block !important;
                }
                
                /* Reset standard containers */
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
                
                /* Ensure maps print nicely */
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
        <div id="global-loader" class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#FAF7F2] transition-opacity duration-300">
            <div class="flex flex-col items-center gap-4">
                <div class="grid size-16 place-items-center rounded-2xl bg-[#0F4C5C] text-white shadow-lg shadow-[#0F4C5C]/20 animate-bounce">
                    <span class="text-2xl font-black tracking-wider font-display">SP</span>
                </div>
                <div class="w-36 h-1.5 bg-slate-200 rounded-full overflow-hidden relative">
                    <div class="absolute inset-y-0 left-0 bg-[#E36414] rounded-full w-24 animate-loading-bar"></div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 animate-pulse">Memuat Aplikasi...</span>
            </div>
        </div>

        <div class="min-h-dvh flex flex-col">
            <x-navbar />

            <main class="w-full px-6 py-6 sm:px-10 sm:py-8 lg:px-16 xl:px-24" style="padding-bottom: max(2rem, env(safe-area-inset-bottom))">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-slate-100 text-slate-600 border-t border-slate-200 mt-auto">
                <div class="w-full px-6 py-12 sm:px-10 lg:px-16 xl:px-24">
                    <div class="grid gap-8 sm:grid-cols-2 md:grid-cols-4">
                        {{-- Brand Column --}}
                        <div class="space-y-4">
                            <a href="{{ route('home') }}" class="flex items-center shrink-0 group">
                                <img src="{{ asset('images/logo.png') }}" alt="Samarinda Properti Logo" class="h-20 w-auto transition-transform duration-300 group-hover:scale-105" />
                            </a>
                            <p class="text-xs leading-relaxed text-slate-500">
                                Platform pencarian properti pertama di Kota Samarinda yang mengintegrasikan informasi spasial geospasial dengan pemetaan wilayah bebas banjir secara real-time.
                            </p>
                        </div>

                        {{-- Nav Links --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Navigasi Utama</h3>
                            <ul class="space-y-2 text-xs font-medium">
                                <li><a href="{{ route('home') }}" class="hover:text-brand-primary transition">Beranda</a></li>
                                <li><a href="{{ route('properties.index') }}" class="hover:text-brand-primary transition">Katalog Properti</a></li>
                                <li><a href="{{ route('explore') }}" class="hover:text-brand-primary transition">Eksplorasi Peta</a></li>
                                <li><a href="{{ route('seller.listings.create') }}" class="hover:text-brand-primary transition">Pasang Iklan Baru</a></li>
                            </ul>
                        </div>

                        {{-- Spatial features --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Fitur Spasial</h3>
                            <ul class="space-y-2.5 text-xs font-semibold">
                                <li class="flex items-center gap-1.5">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Zona Bebas Banjir</span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="size-1.5 rounded-full bg-[#E36414]"></span>
                                    <span>Rute Fasilitas Terdekat</span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="size-1.5 rounded-full bg-blue-500"></span>
                                    <span>Batas Administrasi Kecamatan</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Contacts --}}
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Hubungi Kami</h3>
                            <ul class="space-y-2.5 text-xs font-semibold">
                                <li class="flex items-center gap-2">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Samarinda, Kalimantan Timur</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate">support@samarindaproperti.gis</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-[10px] text-slate-400">&copy; {{ date('Y') }} Samarinda Properti GIS. Hak Cipta Dilindungi.</p>
                        <p class="text-[10px] text-slate-400">Dibuat dengan dedikasi untuk tata kota Samarinda yang cerdas.</p>
                    </div>
                </div>
            </footer>
        </div>

        {{-- Scroll to Top Button --}}
        <button id="scrollToTopBtn" type="button" class="fixed bottom-6 right-6 z-[1000] size-11 rounded-full bg-[#0F4C5C] text-white shadow-lg shadow-[#0F4C5C]/20 flex items-center justify-center transition-all duration-300 opacity-0 translate-y-4 pointer-events-none hover:bg-[#0b3945] hover:-translate-y-1 hover:shadow-xl cursor-pointer border-0">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>
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

            // Loading Screen Handler with Minimum Duration (1200ms)
            window.addEventListener('load', function() {
                const elapsedTime = Date.now() - _loaderStartTime;
                const minDuration = 1200;
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

