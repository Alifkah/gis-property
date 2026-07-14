<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            @keyframes pulse-marker {
                0% {
                    transform: scale(0.8);
                    opacity: 0.5;
                }

                70% {
                    transform: scale(1.5);
                    opacity: 0;
                }

                100% {
                    transform: scale(0.8);
                    opacity: 0;
                }
            }
        </style>
    @endpush

    @php
        $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
        $existingImages = $property->images->filter(function ($img) use ($isLocalDisk) {
            return !$isLocalDisk || Storage::disk('public')->exists($img->path);
        });
        $firstImage = $existingImages->first();
        $imageUrl = $firstImage ? Storage::disk('public')->url($firstImage->path) : null;
        if (!$imageUrl) {
            $placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" fill="none">'
                . '<rect width="400" height="250" fill="url(#g)"/>'
                . '<defs>'
                . '<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
                . '<stop offset="0%" stop-color="#4f46e5" stop-opacity="0.12"/>'
                . '<stop offset="100%" stop-color="#6366f1" stop-opacity="0.04"/>'
                . '</linearGradient>'
                . '</defs>'
                . '<path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/>'
                . '<text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">'
                . strtoupper($property->type)
                . '</text>'
                . '</svg>';
            $imageUrl = 'data:image/svg+xml;base64,' . base64_encode($placeholderSvg);
        }
    @endphp

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div>
            {{-- Galeri foto --}}
            <div class="relative">
                @if ($existingImages->isNotEmpty())
                    <div
                        class="relative group overflow-hidden rounded-2xl bg-slate-900 shadow-xs w-full aspect-[16/10] sm:aspect-[16/9] lg:aspect-[21/10]">
                        {{-- Slider Viewport --}}
                        <div id="gallerySlider" class="flex h-full w-full transition-transform duration-500 ease-out">
                            @foreach ($existingImages as $image)
                                <div class="w-full h-full shrink-0 cursor-pointer overflow-hidden flex items-center justify-center bg-slate-950"
                                    onclick="openLightbox({{ $loop->index }})">
                                    <img src="{{ Storage::disk('public')->url($image->path) }}"
                                        alt="{{ $property->title }} - Foto {{ $loop->iteration }}"
                                        class="h-full w-full object-cover transition duration-500 hover:scale-[1.02]" />
                                </div>
                            @endforeach
                        </div>

                        {{-- Navigation Arrows --}}
                        @if ($existingImages->count() > 1)
                            <button type="button" onclick="prevSlide(event)"
                                class="absolute left-4 top-1/2 -translate-y-1/2 z-10 size-10 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 shadow-sm cursor-pointer border-0">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <button type="button" onclick="nextSlide(event)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 size-10 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 shadow-sm cursor-pointer border-0">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        @endif

                        {{-- Photo Badge Counter --}}
                        <div
                            class="absolute bottom-4 right-4 z-10 rounded-xl bg-slate-900/80 backdrop-blur-xs px-3 py-1.5 text-xs font-bold text-white shadow-xs flex items-center gap-1.5 pointer-events-none">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <span id="sliderCounter">1 / {{ $existingImages->count() }} Foto</span>
                        </div>
                    </div>

                    {{-- Thumbnails Scroll Strip --}}
                    @if ($existingImages->count() > 1)
                        <div class="mt-3 flex gap-2 overflow-x-auto pb-2 scrollbar-thin max-w-full">
                            @foreach ($existingImages as $image)
                                <button type="button" onclick="goToSlide({{ $loop->index }})"
                                    class="thumbnail-btn relative shrink-0 aspect-[16/10] w-[70px] sm:w-[90px] rounded-xl overflow-hidden bg-slate-100 ring-2 ring-transparent transition hover:ring-brand-primary/40 cursor-pointer border-0 p-0">
                                    <img src="{{ Storage::disk('public')->url($image->path) }}"
                                        alt="Miniatur {{ $loop->iteration }}" class="h-full w-full object-cover" />
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    @php
                        $svg1 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4f46e5" stop-opacity="0.15"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0.05"/></linearGradient></defs><path d="M220 240 l80-60 80 60 M240 220 v60h120v-60 M280 280 v-30h40v30" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">EKSTERIOR</text></svg>';
                        $svg2 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#06b6d4" stop-opacity="0.15"/><stop offset="100%" stop-color="#4f46e5" stop-opacity="0.05"/></linearGradient></defs><path d="M220 260 h160 M240 260 v-30 h120 v30 M250 210 h100" stroke="#0891b2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#0891b2" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">INTERIOR</text></svg>';
                        $svg3 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.15"/><stop offset="100%" stop-color="#ec4899" stop-opacity="0.05"/></linearGradient></defs><path d="M220 280 v-50 h160 v50 M250 230 h100" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#7c3aed" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">RUANGAN</text></svg>';
                        $img1 = 'data:image/svg+xml;base64,' . base64_encode($svg1);
                        $img2 = 'data:image/svg+xml;base64,' . base64_encode($svg2);
                        $img3 = 'data:image/svg+xml;base64,' . base64_encode($svg3);
                    @endphp
                    <section class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_280px]">
                        <div
                            class="group overflow-hidden rounded-2xl bg-slate-100 h-[350px] shadow-xs hover:shadow-md transition duration-300">
                            <img src="{{ $img1 }}" alt="{{ $property->title }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                        </div>
                        <div class="grid grid-cols-2 gap-3 lg:grid-cols-1">
                            <div
                                class="group overflow-hidden rounded-2xl bg-slate-100 lg:h-[168px] shadow-xs hover:shadow-md transition duration-300">
                                <img src="{{ $img2 }}" alt="{{ $property->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                            </div>
                            <div
                                class="group overflow-hidden rounded-2xl bg-slate-100 lg:h-[168px] shadow-xs hover:shadow-md transition duration-300">
                                <img src="{{ $img3 }}" alt="{{ $property->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            <section class="mt-8">
                <div class="flex flex-wrap items-center gap-2">
                    @if (($property->status ?? 'Tersedia') === 'Terjual')
                        <span
                            class="inline-flex items-center rounded-full bg-slate-700 px-2.5 py-1 text-xs font-semibold text-white shadow-xs">Terjual</span>
                    @endif
                    @if ($isNew)
                        <x-badge variant="new">Rumah Baru</x-badge>
                    @endif
                    @if ($isFloodSafe)
                        <x-badge variant="safe">Bebas Banjir</x-badge>
                    @endif
                </div>

                <h1 class="mt-3 text-2xl font-extrabold text-slate-900 tracking-tight">{{ $property->title }}</h1>
                <div class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-slate-600">
                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>Kecamatan {{ $districtName ?? 'Kota Samarinda' }}, Samarinda, Kalimantan Timur</span>
                </div>

                <div class="mt-6 grid gap-4 grid-cols-2 sm:grid-cols-4">
                    <div
                        class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                        <span
                            class="grid size-9 place-items-center rounded-xl bg-brand-primary/8 text-brand-primary mb-3">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h7v7H4z" />
                                <path d="M13 13h7v7h-7z" />
                                <path d="M13 4h7v7h-7z" />
                                <path d="M4 13h7v7H4z" />
                            </svg>
                        </span>
                        <div class="text-xs font-semibold text-slate-500">Luas Tanah</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->land_area }} m²
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                        <span
                            class="grid size-9 place-items-center rounded-xl bg-brand-primary/8 text-brand-primary mb-3">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                        </span>
                        <div class="text-xs font-semibold text-slate-500">Luas Bangunan</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->building_area }} m²
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                        <span
                            class="grid size-9 place-items-center rounded-xl bg-brand-primary/8 text-brand-primary mb-3">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 4v16" />
                                <path d="M2 17h20" />
                                <path d="M22 8v12" />
                                <path d="M2 8h20" />
                                <path d="M6 12h4a2 2 0 0 0 2-2V8H4v2a2 2 0 0 0 2 2Z" />
                            </svg>
                        </span>
                        <div class="text-xs font-semibold text-slate-500">Kamar Tidur</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->bedroom }} Ruang
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                        <span
                            class="grid size-9 place-items-center rounded-xl bg-brand-primary/8 text-brand-primary mb-3">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-1C4.3 2.5 3 4.3 3.5 5.5l2 2" />
                                <path d="M3 11h18a2 2 0 0 1 2 2v2a6 6 0 0 1-6 6H7a6 6 0 0 1-6-6v-2a2 2 0 0 1-2-2Z" />
                                <path d="M7 21v2M17 21v2" />
                            </svg>
                        </span>
                        <div class="text-xs font-semibold text-slate-500">Kamar Mandi</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->bathroom }} Ruang
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-8 card p-6">
                <div
                    class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="size-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 6.75V15m6-6v8.25m.503 3.498l4.89-1.63a1.875 1.875 0 001.108-1.723V1.35c0-.847-.788-1.54-1.618-1.328l-4.708 1.205M9.623 3.328L3.3 5.4a1.875 1.875 0 00-1.29 1.783v12.285c0 .762.583 1.417 1.34 1.328l6.233-1.205m0-14.542L15.5 1.3M9 6.75L15.5 4.5m-.5 10.5L9 15" />
                    </svg>
                    <span>Analisis Geospasial & Lingkungan</span>
                </div>
                <div class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <div class="flex flex-col">
                        <div class="rounded-2xl overflow-hidden shadow-xs ring-1 ring-slate-200/50 relative"
                            style="height:320px;min-height:320px">
                            <div id="miniMap" class="relative z-0" style="height:320px;width:100%"></div>

                            {{-- MiniMap Route Overlay Info Panel --}}
                            <div id="miniRoutePanel" style="display:none;"
                                class="absolute top-3 right-3 z-[400] w-[210px] bg-white/95 backdrop-blur-md rounded-xl p-3 shadow-md border border-slate-200/60 transition">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                                    id="miniRouteLabel">Petunjuk Rute</div>
                                <div class="mt-1 text-xs font-black text-slate-800" id="miniRouteDist">-</div>
                                <div class="mt-1 text-[10px] font-bold text-brand-primary bg-brand-primary/5 px-1.5 py-0.5 rounded inline-block"
                                    id="miniRouteTime">-</div>
                                <div
                                    class="mt-1.5 text-[9px] font-semibold text-slate-500 flex items-center justify-between">
                                    <span>Lalu Lintas:</span>
                                    <span id="miniTrafficStatus" class="font-black px-1.5 py-0.5 rounded"></span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="mt-3 flex items-center justify-between gap-3 rounded-2xl bg-slate-50/80 p-4 ring-1 ring-slate-200/50 shadow-2xs">
                            <div class="min-w-0">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Koordinat
                                    Properti</div>
                                <div class="mt-0.5 text-xs font-semibold text-slate-700 truncate">
                                    {{ number_format((float) $point['lat'], 6) }},
                                    {{ number_format((float) $point['lng'], 6) }}
                                </div>
                            </div>
                            <button type="button" id="directionsBtn" onclick="toggleDirections()"
                                class="btn btn-outline py-2 px-3 text-xs flex items-center gap-1.5 shrink-0 shadow-3xs hover:bg-brand-primary/5 hover:text-brand-primary hover:ring-brand-primary/20 transition cursor-pointer">
                                <svg class="size-4 text-brand-primary" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span id="directionsBtnText">Petunjuk Arah</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 min-w-0">
                        @if ($isFloodSafe)
                            <div class="rounded-2xl bg-emerald-50/60 p-4 ring-1 ring-emerald-500/20 shadow-xs">
                                <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Status Banjir</div>
                                <div class="mt-2.5 flex items-center gap-2 text-sm font-extrabold text-emerald-950">
                                    <span
                                        class="grid size-7 place-items-center rounded-full bg-emerald-500 text-white shadow-xs">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path d="M20 6 9 17l-5-5" />
                                        </svg>
                                    </span>
                                    <span>Bebas Banjir (Aman)</span>
                                </div>
                                <p class="mt-2 text-xs text-emerald-700 leading-relaxed font-semibold">Properti ini berada
                                    di luar zona rawan genangan banjir Samarinda berdasarkan analisis peta kontur spasial
                                    terbaru.</p>
                            </div>
                        @else
                            <div class="rounded-2xl bg-rose-50/60 p-4 ring-1 ring-rose-500/20 shadow-xs">
                                <div class="text-xs font-bold text-rose-800 uppercase tracking-wider">Status Banjir</div>
                                <div class="mt-2.5 flex items-center gap-2 text-sm font-extrabold text-rose-950">
                                    <span
                                        class="grid size-7 place-items-center rounded-full bg-rose-500 text-white shadow-xs">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path d="M18 6 6 18M6 6l12 12" />
                                        </svg>
                                    </span>
                                    <span>Zona Rawan Banjir</span>
                                </div>
                                <p class="mt-2 text-xs text-rose-700 leading-relaxed font-semibold">Perhatian: Properti
                                    terdeteksi berada di dalam atau dekat zona genangan air tinggi. Disarankan memeriksa
                                    ketinggian fondasi bangunan.</p>
                            </div>
                        @endif

                        <div
                            class="rounded-2xl bg-slate-50/70 p-4 ring-1 ring-slate-200/70 shadow-xs flex-1 flex flex-col justify-between min-w-0">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aksesibilitas
                                    Fasilitas Terdekat</div>
                                <div class="mt-3 grid gap-2 min-w-0">
                                    @foreach ($nearestAmenities as $amenity)
                                        @php
                                            $amenityType = strtolower($amenity->type ?? '');
                                            $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />'; // map-pin
                                            $iconColor = 'text-brand-primary bg-brand-primary/8';

                                            if (str_contains($amenityType, 'sekolah') || str_contains($amenityType, 'universitas') || str_contains($amenityType, 'pendidikan') || str_contains($amenityType, 'education')) {
                                                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M12 21v-8.25" />';
                                                $iconColor = 'text-amber-600 bg-amber-50';
                                            } elseif (str_contains($amenityType, 'sakit') || str_contains($amenityType, 'klinik') || str_contains($amenityType, 'puskesmas') || str_contains($amenityType, 'medis') || str_contains($amenityType, 'medical') || str_contains($amenityType, 'hospital')) {
                                                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />';
                                                $iconColor = 'text-rose-600 bg-rose-50';
                                            } elseif (str_contains($amenityType, 'pasar') || str_contains($amenityType, 'mall') || str_contains($amenityType, 'belanja') || str_contains($amenityType, 'supermarket') || str_contains($amenityType, 'minimarket') || str_contains($amenityType, 'shopping') || str_contains($amenityType, 'market')) {
                                                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />';
                                                $iconColor = 'text-emerald-600 bg-emerald-50';
                                            } elseif (str_contains($amenityType, 'taman') || str_contains($amenityType, 'wisata') || str_contains($amenityType, 'rekreasi') || str_contains($amenityType, 'park') || str_contains($amenityType, 'nature')) {
                                                $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />';
                                                $iconColor = 'text-teal-600 bg-teal-50';
                                            }
                                        @endphp
                                        <div onclick="showAmenityRoute({{ $loop->index }}, {{ $amenity->lat ?? 0 }}, {{ $amenity->lng ?? 0 }}, '{{ addslashes($amenity->name) }}', '{{ $amenity->type }}')"
                                            class="flex items-center justify-between gap-4 rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200/50 shadow-2xs hover:ring-slate-400/40 hover:bg-slate-50/50 transition min-w-0 cursor-pointer"
                                            title="Klik untuk lihat rute jalan di peta">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span
                                                    class="grid size-8 shrink-0 place-items-center rounded-lg {{ $iconColor }}">
                                                    <svg class="size-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        {!! $iconPath !!}
                                                    </svg>
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="truncate text-xs font-bold text-slate-800">
                                                        {{ $amenity->name }}</div>
                                                    <div
                                                        class="truncate text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                        {{ $amenity->type }}</div>
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <div
                                                    class="text-xs font-extrabold text-brand-primary bg-brand-primary/8 px-2 py-0.5 rounded-md inline-block">
                                                    {{ number_format(((float) $amenity->distance_m) / 1000, 1) }} km
                                                </div>
                                                <div id="amenity-time-{{ $loop->index }}"
                                                    class="text-[9px] font-bold text-emerald-600 mt-1 hidden text-right leading-none">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-8 card p-6">
                <div
                    class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="size-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span>Deskripsi Properti</span>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-slate-700">
                    {{ $property->description ?? 'Properti ini berada di area Kota Samarinda dan cocok untuk hunian maupun investasi. Jelajahi detail lokasi melalui peta mini dan gunakan analisis fasilitas terdekat untuk mengambil keputusan.' }}
                </p>
            </section>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6 shadow-sm">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Penawaran</div>
                <div class="text-2xl font-black text-brand-accent mt-1">Rp
                    {{ number_format((float) $property->price, 0, ',', '.') }}</div>

                @if (($property->status ?? 'Tersedia') === 'Terjual')
                    <div
                        class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-extrabold text-slate-600 ring-1 ring-slate-200">
                        <span class="size-2 rounded-full bg-slate-400"></span>
                        Properti Sudah Terjual
                    </div>
                @else
                    <div
                        class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700 ring-1 ring-emerald-200">
                        <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Tersedia & Siap Huni
                    </div>
                @endif

                <div class="mt-5 grid gap-2.5">
                    @if ($property->user?->phone)
                        @php
                            $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $property->user->phone));
                        @endphp
                        <a id="whatsappBtn"
                            href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo, saya tertarik dengan properti: ' . $property->title) }}"
                            target="_blank" rel="noopener"
                            class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold transition shadow-xs hover:shadow-md">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <span>Hubungi via WhatsApp</span>
                        </a>
                    @else
                        <button type="button" class="btn btn-primary w-full py-3" disabled>Hubungi via WhatsApp</button>
                    @endif
                    <button type="button" class="btn btn-outline w-full py-3">Jadwalkan Kunjungan</button>
                </div>

                <div class="mt-6 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/50 shadow-2xs">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Kontak Pengiklan
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="grid size-11 shrink-0 place-items-center rounded-2xl bg-brand-primary font-extrabold text-white shadow-xs">
                            {{ strtoupper(substr($property->user?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-extrabold text-slate-900">
                                {{ $property->user?->name ?? 'Penjual' }}</div>
                            <div class="truncate text-xs font-semibold text-slate-500">
                                {{ $property->user?->phone ?? 'Nomor tidak tersedia' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 card p-6 shadow-sm">
                <div
                    class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-2.5 flex items-center gap-2">
                    <svg class="size-4.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    <span>Simulasi KPR</span>
                </div>
                <div class="mt-4 grid gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-600">Uang Muka (DP)</label>
                            <span id="dpPercentLabel"
                                class="text-xs font-bold text-brand-primary bg-brand-primary/8 px-1.5 py-0.5 rounded">20%</span>
                        </div>
                        <input id="dpInputDisplay" type="text" class="input" placeholder="100.000.000" />
                        <input id="dpInput" type="hidden" value="0" />
                        <input id="dpSlider" type="range" min="10" max="90" step="5" value="20"
                            class="w-full accent-brand-primary h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer mt-2.5" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-600">Jangka Waktu (Tenor)</label>
                            <span id="termLabel"
                                class="text-xs font-bold text-brand-primary bg-brand-primary/8 px-1.5 py-0.5 rounded">15
                                Tahun</span>
                        </div>
                        <input id="termSlider" type="range" min="1" max="30" step="1" value="15"
                            class="w-full accent-brand-primary h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer mt-1" />
                        <input id="termInput" type="hidden" value="15" />
                    </div>
                    <div class="rounded-2xl bg-brand-primary/5 p-4 ring-1 ring-brand-primary/10 shadow-3xs">
                        <div class="text-[10px] font-bold text-brand-primary uppercase tracking-wider">Estimasi Cicilan
                            / Bulan</div>
                        <div id="installment" class="mt-1 text-xl font-black text-brand-accent">-</div>
                        <div class="mt-1.5 text-[10px] font-bold text-slate-400">Asumsi bunga tetap KPR 8% per tahun
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagikan Properti Card --}}
            <div class="mt-4 card p-6 shadow-sm">
                <div
                    class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-2.5 flex items-center gap-2">
                    <svg class="size-4.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    <span>Bagikan Properti</span>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2">
                    {{-- WhatsApp --}}
                    @php
                        $shareText = rawurlencode($property->title . ' - Temukan properti menarik ini di Samarinda: ' . request()->url());
                    @endphp
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" rel="noopener"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 bg-[#25D366]/5 hover:bg-[#25D366]/10 text-[#25D366] transition group shadow-3xs cursor-pointer">
                        <svg class="size-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span class="text-[10px] font-bold">WhatsApp</span>
                    </a>

                    {{-- Facebook --}}
                    @php
                        $shareUrl = rawurlencode(request()->url());
                    @endphp
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                        rel="noopener"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 bg-[#1877F2]/5 hover:bg-[#1877F2]/10 text-[#1877F2] transition group shadow-3xs cursor-pointer">
                        <svg class="size-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z" />
                        </svg>
                        <span class="text-[10px] font-bold">Facebook</span>
                    </a>

                    {{-- Copy Link --}}
                    <button onclick="copyToClipboard()"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 bg-brand-primary/5 hover:bg-brand-primary/10 text-brand-primary transition group shadow-3xs cursor-pointer">
                        <svg id="copyIcon" class="size-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <span id="copyText" class="text-[10px] font-bold">Salin Link</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- Lightbox Modal --}}
    @if ($existingImages->isNotEmpty())
        <div id="lightboxModal" style="display:none;"
            class="fixed inset-0 z-[1000] bg-black/95 flex flex-col justify-between p-4 transition-all duration-300">
            {{-- Header --}}
            <div class="flex items-center justify-between text-white pb-2">
                <span id="lightboxCounter" class="text-xs font-bold pointer-events-none">1 / 1</span>
                <button type="button" onclick="closeLightbox()"
                    class="size-10 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 flex items-center justify-center transition cursor-pointer bg-transparent border-0">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Main Viewport --}}
            <div class="flex-1 relative flex items-center justify-center overflow-hidden">
                <div id="lightboxSlider" class="flex h-full w-full transition-transform duration-300 ease-out">
                    @foreach ($existingImages as $image)
                        <div class="w-full h-full shrink-0 flex items-center justify-center bg-transparent select-none">
                            <img src="{{ Storage::disk('public')->url($image->path) }}"
                                alt="{{ $property->title }} - Zoom {{ $loop->iteration }}"
                                class="max-h-full max-w-full object-contain pointer-events-none" />
                        </div>
                    @endforeach
                </div>

                @if ($existingImages->count() > 1)
                    {{-- Arrow controls --}}
                    <button type="button" onclick="prevLightbox(event)"
                        class="absolute left-2 top-1/2 -translate-y-1/2 size-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition shadow-lg cursor-pointer border-0">
                        <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button type="button" onclick="nextLightbox(event)"
                        class="absolute right-2 top-1/2 -translate-y-1/2 size-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition shadow-lg cursor-pointer border-0">
                        <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Bottom spacer --}}
            <div class="h-6"></div>
        </div>
    @endif

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const point = @json($point);
            const miniMap = L.map('miniMap', { zoomControl: false, attributionControl: false }).setView([point.lat, point.lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(miniMap);

            const markerIcon = L.divIcon({
                className: '',
                html: '<div style="width:16px;height:16px;border-radius:9999px;background:#E36414;border:3.5px solid #ffffff;box-shadow:0 4px 10px rgba(15,23,42,.35)"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
            L.marker([point.lat, point.lng], { icon: markerIcon }).addTo(miniMap);

            // Force correct size: immediately + after layout settles
            miniMap.invalidateSize({ animate: false });
            setTimeout(() => miniMap.invalidateSize({ animate: false }), 200);
            setTimeout(() => miniMap.invalidateSize({ animate: false }), 600);
            window.addEventListener('load', () => miniMap.invalidateSize({ animate: false }));

            // GPS and Routing Variables
            window.userLocation = null;
            let userMarker = null;
            let userAccuracyCircle = null;
            let routeLines = [];

            // Real-time Traffic Calculation Helper functions
            function getSamarindaTimeDecimal() {
                try {
                    const options = {
                        timeZone: 'Asia/Makassar',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    };
                    const formatter = new Intl.DateTimeFormat('en-US', options);
                    const parts = formatter.formatToParts(new Date());
                    let hour = 0;
                    let minute = 0;
                    for (const part of parts) {
                        if (part.type === 'hour') hour = parseInt(part.value, 10);
                        if (part.type === 'minute') minute = parseInt(part.value, 10);
                    }
                    return hour + (minute / 60);
                } catch (e) {
                    const now = new Date();
                    return now.getHours() + (now.getMinutes() / 60);
                }
            }

            function getTrafficData() {
                const time = getSamarindaTimeDecimal();
                let multiplier = 1.0;
                let status = "Lancar";
                let statusClass = "text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/30";

                if ((time >= 7.0 && time < 9.0) || (time >= 16.5 && time < 19.0)) {
                    multiplier = 2.3;
                    status = "Padat (Jam Sibuk)";
                    statusClass = "text-rose-600 bg-rose-50 dark:text-rose-400 dark:bg-rose-950/30";
                } else if (time >= 9.0 && time < 16.5) {
                    multiplier = 1.4;
                    status = "Sedang (Jam Kerja)";
                    statusClass = "text-amber-600 bg-amber-50 dark:text-amber-400 dark:bg-amber-950/30";
                } else {
                    multiplier = 1.0;
                    status = "Lancar";
                    statusClass = "text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/30";
                }

                const hours = Math.floor(time);
                const minutes = Math.floor((time - hours) * 60);
                const timeStr = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;

                return { multiplier, status, statusClass, timeStr };
            }

            async function getRoute(fromLat, fromLng, toLat, toLng) {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error("Gagal memuat rute jalan.");
                }
                const data = await response.json();
                if (!data.routes || data.routes.length === 0) {
                    throw new Error("Rute tidak ditemukan.");
                }
                return data.routes[0];
            }

            function clearRoutes() {
                routeLines.forEach(line => miniMap.removeLayer(line));
                routeLines = [];
                document.getElementById('miniRoutePanel').style.display = 'none';
            }

            window.toggleDirections = function () {
                if (!navigator.geolocation) {
                    alert("Geolokasi tidak didukung oleh browser Anda.");
                    return;
                }

                const btn = document.getElementById('directionsBtn');
                const btnText = document.getElementById('directionsBtnText');

                btn.disabled = true;
                btnText.textContent = "Mencari Lokasi...";

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        window.userLocation = { lat, lng };

                        // Draw user marker
                        if (userMarker) {
                            userMarker.setLatLng([lat, lng]);
                        } else {
                            const userIcon = L.divIcon({
                                className: '',
                                html: `<div style="position:relative;width:20px;height:20px;">
                                             <div style="position:absolute;width:20px;height:20px;background:#0F4C5C;border:3px solid #ffffff;border-radius:50%;box-shadow:0 0 8px rgba(0,0,0,0.3);z-index:2"></div>
                                             <div style="position:absolute;width:30px;height:30px;background:#0F4C5C;border-radius:50%;opacity:0.3;top:-5px;left:-5px;animation:pulse-marker 2s infinite;z-index:1"></div>
                                           </div>`,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });
                            userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(miniMap);
                        }

                        if (userAccuracyCircle) {
                            userAccuracyCircle.setLatLng([lat, lng]).setRadius(position.coords.accuracy);
                        } else {
                            userAccuracyCircle = L.circle([lat, lng], {
                                radius: position.coords.accuracy,
                                color: '#0F4C5C',
                                fillColor: '#0F4C5C',
                                fillOpacity: 0.05,
                                weight: 1
                            }).addTo(miniMap);
                        }

                        // Clear old route lines
                        clearRoutes();

                        try {
                            const route = await getRoute(lat, lng, point.lat, point.lng);
                            const polyline = L.geoJSON(route.geometry, {
                                style: {
                                    color: '#E36414',
                                    weight: 5,
                                    opacity: 0.8
                                }
                            }).addTo(miniMap);
                            routeLines.push(polyline);

                            const distKm = (route.distance / 1000).toFixed(1);
                            const traffic = getTrafficData();
                            const driveTime = Math.round((route.duration * traffic.multiplier) / 60);
                            const walkTime = Math.round((route.distance / 1.39) / 60);

                            document.getElementById('miniRouteLabel').textContent = "Rute Anda ke Properti";
                            document.getElementById('miniRouteDist').textContent = `${distKm} km`;
                            document.getElementById('miniRouteTime').textContent = `${driveTime} mnt berkendara / ${walkTime} mnt jalan kaki`;

                            const trafficStatusEl = document.getElementById('miniTrafficStatus');
                            if (trafficStatusEl) {
                                trafficStatusEl.textContent = traffic.status;
                                trafficStatusEl.className = `font-black px-1.5 py-0.5 rounded text-[8px] ${traffic.statusClass}`;
                            }

                            document.getElementById('miniRoutePanel').style.display = 'block';

                            miniMap.fitBounds(polyline.getBounds(), { padding: [40, 40] });

                            btnText.textContent = "Petunjuk Arah Aktif";
                        } catch (err) {
                            console.error("Routing error:", err);
                            // Fallback straight line
                            const fallbackLine = L.polyline([[lat, lng], [point.lat, point.lng]], {
                                color: '#E36414',
                                dashArray: '5, 10',
                                weight: 4
                            }).addTo(miniMap);
                            routeLines.push(fallbackLine);

                            // Distance calculation
                            const R = 6371e3;
                            const dLat = (point.lat - lat) * Math.PI / 180;
                            const dLng = (point.lng - lng) * Math.PI / 180;
                            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat * Math.PI / 180) * Math.cos(point.lat * Math.PI / 180) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                            const dist = R * c;

                            document.getElementById('miniRouteLabel').textContent = "Jarak Garis Lurus";
                            document.getElementById('miniRouteDist').textContent = `${(dist / 1000).toFixed(1)} km`;
                            document.getElementById('miniRouteTime').textContent = "Rute jalan gagal dimuat";
                            document.getElementById('miniRoutePanel').style.display = 'block';

                            miniMap.fitBounds(fallbackLine.getBounds(), { padding: [40, 40] });
                            btnText.textContent = "Jarak Garis Lurus";
                        }

                        btn.disabled = false;
                    },
                    (error) => {
                        console.warn(error);
                        let msg = "Gagal mendeteksi lokasi GPS Anda.";
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = "Akses lokasi ditolak browser. Mohon izinkan akses lokasi untuk situs ini di pengaturan browser Anda (biasanya di ikon gembok sebelah alamat web), lalu coba lagi.";
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            msg = "Lokasi GPS tidak tersedia. Pastikan GPS perangkat Anda aktif.";
                        } else if (error.code === error.TIMEOUT) {
                            msg = "Waktu pencarian lokasi habis. Silakan coba lagi.";
                        }
                        alert(msg);
                        btn.disabled = false;
                        btnText.textContent = "Petunjuk Arah";
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            };

            window.showAmenityRoute = async function (index, destLat, destLng, name, type) {
                if (destLat === 0 || destLng === 0) return;

                // Clear previous routing lines
                clearRoutes();

                // Clear styling active on other items
                const timeElements = document.querySelectorAll('[id^="amenity-time-"]');
                timeElements.forEach(el => {
                    el.style.display = 'none';
                    el.textContent = '';
                });

                const timeEl = document.getElementById(`amenity-time-${index}`);
                if (timeEl) {
                    timeEl.style.display = 'block';
                    timeEl.textContent = 'Memuat rute...';
                }

                try {
                    const route = await getRoute(point.lat, point.lng, destLat, destLng);
                    const polyline = L.geoJSON(route.geometry, {
                        style: {
                            color: '#10B981', // Emerald green
                            weight: 5,
                            opacity: 0.8
                        }
                    }).addTo(miniMap);
                    routeLines.push(polyline);

                    const distKm = (route.distance / 1000).toFixed(1);
                    const traffic = getTrafficData();
                    const driveTime = Math.round((route.duration * traffic.multiplier) / 60);
                    const walkTime = Math.round((route.distance / 1.39) / 60);

                    document.getElementById('miniRouteLabel').textContent = `Ke: ${name}`;
                    document.getElementById('miniRouteDist').textContent = `${distKm} km`;
                    document.getElementById('miniRouteTime').textContent = `${driveTime} mnt berkendara / ${walkTime} mnt jalan kaki`;

                    const trafficStatusEl = document.getElementById('miniTrafficStatus');
                    if (trafficStatusEl) {
                        trafficStatusEl.textContent = traffic.status;
                        trafficStatusEl.className = `font-black px-1.5 py-0.5 rounded text-[8px] ${traffic.statusClass}`;
                    }

                    document.getElementById('miniRoutePanel').style.display = 'block';

                    if (timeEl) {
                        timeEl.textContent = `${driveTime} mnt (mobil) / ${walkTime} mnt (jalan)`;
                    }

                    miniMap.fitBounds(polyline.getBounds(), { padding: [40, 40] });
                } catch (err) {
                    console.error("OSRM routing to facility failed:", err);
                    // Draw direct straight line fallback
                    const fallbackLine = L.polyline([[point.lat, point.lng], [destLat, destLng]], {
                        color: '#10B981',
                        dashArray: '5, 10',
                        weight: 4
                    }).addTo(miniMap);
                    routeLines.push(fallbackLine);

                    // Estimate direct time
                    const R = 6371e3;
                    const dLat = (destLat - point.lat) * Math.PI / 180;
                    const dLng = (destLng - point.lng) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(point.lat * Math.PI / 180) * Math.cos(destLat * Math.PI / 180) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    const dist = R * c;

                    const distKm = (dist / 1000).toFixed(1);
                    const walkTime = Math.round((dist / 1.39) / 60);

                    document.getElementById('miniRouteLabel').textContent = `Ke: ${name}`;
                    document.getElementById('miniRouteDist').textContent = `${distKm} km (garis lurus)`;
                    document.getElementById('miniRouteTime').textContent = `~${walkTime} mnt jalan kaki`;
                    document.getElementById('miniRoutePanel').style.display = 'block';

                    if (timeEl) {
                        timeEl.textContent = `~${walkTime} mnt jalan`;
                    }

                    miniMap.fitBounds(fallbackLine.getBounds(), { padding: [40, 40] });
                }
            };

            const price = {{ (float) $property->price }};
            const dpInput = document.getElementById('dpInput');
            const dpInputDisplay = document.getElementById('dpInputDisplay');
            const dpSlider = document.getElementById('dpSlider');
            const dpPercentLabel = document.getElementById('dpPercentLabel');
            const termSlider = document.getElementById('termSlider');
            const termInput = document.getElementById('termInput');
            const termLabel = document.getElementById('termLabel');
            const out = document.getElementById('installment');

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID').format(Math.round(value));
            }

            function calc() {
                const dp = Math.max(0, Number(dpInput.value || 0));
                const years = Math.max(1, Number(termInput.value || 1));
                const principal = Math.max(0, price - dp);
                const r = 0.08 / 12;
                const n = years * 12;
                const m = principal === 0 ? 0 : (principal * r) / (1 - Math.pow(1 + r, -n));
                out.textContent = `Rp ${formatCurrency(m)}`;
            }

            function formatNumberString(str) {
                return str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Sync inputs from manual changes
            function handleManualDpInput() {
                let cleanVal = dpInputDisplay.value.replace(/\D/g, '');
                if (cleanVal === '') {
                    dpInput.value = '0';
                    dpInputDisplay.value = '';
                    dpSlider.value = 0;
                    dpPercentLabel.textContent = '0%';
                    calc();
                    return;
                }
                const num = parseInt(cleanVal, 10);
                dpInput.value = num;
                dpInputDisplay.value = formatNumberString(num);

                // Sync slider percentage
                const pct = Math.min(100, Math.max(0, Math.round((num / price) * 100)));
                dpSlider.value = pct;
                dpPercentLabel.textContent = `${pct}%`;

                calc();
            }

            // Sync inputs from slider change
            function handleDpSliderInput() {
                const pct = parseInt(dpSlider.value, 10);
                const num = Math.round((pct / 100) * price);
                dpInput.value = num;
                dpInputDisplay.value = formatNumberString(num);
                dpPercentLabel.textContent = `${pct}%`;
                calc();
            }

            // Sync inputs from term slider change
            function handleTermSliderInput() {
                const years = parseInt(termSlider.value, 10);
                termInput.value = years;
                termLabel.textContent = `${years} Tahun`;
                calc();
            }

            dpInputDisplay.addEventListener('input', handleManualDpInput);
            dpSlider.addEventListener('input', handleDpSliderInput);
            termSlider.addEventListener('input', handleTermSliderInput);

            // Initialize values
            if (!dpInput.value || dpInput.value === '0') {
                const defaultDp = Math.round(price * 0.20);
                dpInput.value = defaultDp;
                dpInputDisplay.value = formatNumberString(defaultDp);
                dpSlider.value = 20;
                dpPercentLabel.textContent = '20%';
            } else {
                const initialVal = parseInt(dpInput.value, 10);
                dpInputDisplay.value = formatNumberString(initialVal);
                const pct = Math.min(100, Math.max(0, Math.round((initialVal / price) * 100)));
                dpSlider.value = pct;
                dpPercentLabel.textContent = `${pct}%`;
            }

            calc();

            document.getElementById('whatsappBtn')?.addEventListener('click', function () {
                fetch('/properties/{{ $property->id }}/whatsapp-click', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).catch(err => console.error(err));
            });

            // Copy Link Functionality
            function copyToClipboard() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    const copyText = document.getElementById('copyText');
                    const copyIcon = document.getElementById('copyIcon');
                    if (copyText) copyText.textContent = 'Tersalin!';
                    if (copyIcon) {
                        copyIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />';
                        copyIcon.classList.add('text-emerald-500');
                    }
                    setTimeout(() => {
                        if (copyText) copyText.textContent = 'Salin Link';
                        if (copyIcon) {
                            copyIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />';
                            copyIcon.classList.remove('text-emerald-500');
                        }
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            }

            // Save to recently viewed
            try {
                const propertyData = {
                    id: {{ $property->id }},
                    title: {!! json_encode($property->title) !!},
                    price: "Rp {{ number_format((float) $property->price, 0, ',', '.') }}",
                    type: "{{ $property->type }}",
                    imageUrl: "{{ $imageUrl }}",
                    districtName: "{{ $districtName ?? 'Samarinda' }}",
                    url: "{{ route('properties.show', $property->id) }}",
                    bedroom: {{ (int) $property->bedroom }},
                    bathroom: {{ (int) $property->bathroom }},
                    landArea: {{ (int) $property->land_area }}
                    };

                let list = JSON.parse(localStorage.getItem('recentlyViewedProperties') || '[]');
                list = list.filter(item => item.id !== propertyData.id);
                list.unshift(propertyData);
                list = list.slice(0, 8);
                localStorage.setItem('recentlyViewedProperties', JSON.stringify(list));
            } catch (e) {
                console.error('Error saving recently viewed property', e);
            }

            // Slider and Lightbox State Logic
            (function () {
                let currentSlideIndex = 0;
                const totalSlides = {{ $existingImages->count() }};
                if (totalSlides === 0) return;

                const slider = document.getElementById('gallerySlider');
                const lightboxSlider = document.getElementById('lightboxSlider');
                const lightboxModal = document.getElementById('lightboxModal');
                const sliderCounter = document.getElementById('sliderCounter');
                const lightboxCounter = document.getElementById('lightboxCounter');
                const thumbs = document.querySelectorAll('.thumbnail-btn');

                window.goToSlide = function (index) {
                    if (index < 0 || index >= totalSlides) return;
                    currentSlideIndex = index;
                    if (slider) {
                        slider.style.transform = `translateX(-${index * 100}%)`;
                    }
                    if (sliderCounter) {
                        sliderCounter.textContent = `${index + 1} / ${totalSlides} Foto`;
                    }
                    thumbs.forEach((t, idx) => {
                        if (idx === index) {
                            t.classList.add('ring-brand-primary', 'active-thumb');
                        } else {
                            t.classList.remove('ring-brand-primary', 'active-thumb');
                        }
                    });
                };

                window.nextSlide = function (e) {
                    if (e) e.stopPropagation();
                    let nextIdx = (currentSlideIndex + 1) % totalSlides;
                    goToSlide(nextIdx);
                };

                window.prevSlide = function (e) {
                    if (e) e.stopPropagation();
                    let prevIdx = (currentSlideIndex - 1 + totalSlides) % totalSlides;
                    goToSlide(prevIdx);
                };

                // Touch support for main slider
                if (slider) {
                    let startX = 0;
                    let endX = 0;
                    slider.addEventListener('touchstart', (e) => {
                        startX = e.touches[0].clientX;
                    }, { passive: true });
                    slider.addEventListener('touchend', (e) => {
                        endX = e.changedTouches[0].clientX;
                        let diffX = startX - endX;
                        if (Math.abs(diffX) > 50) {
                            if (diffX > 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        }
                    }, { passive: true });
                }

                // Lightbox functions
                let currentLightboxIndex = 0;

                window.openLightbox = function (index) {
                    if (!lightboxModal) return;
                    currentLightboxIndex = index;
                    lightboxModal.style.display = 'flex';
                    updateLightboxSlide();
                };

                window.closeLightbox = function () {
                    if (lightboxModal) lightboxModal.style.display = 'none';
                };

                window.nextLightbox = function (e) {
                    if (e) e.stopPropagation();
                    currentLightboxIndex = (currentLightboxIndex + 1) % totalSlides;
                    updateLightboxSlide();
                };

                window.prevLightbox = function (e) {
                    if (e) e.stopPropagation();
                    currentLightboxIndex = (currentLightboxIndex - 1 + totalSlides) % totalSlides;
                    updateLightboxSlide();
                };

                function updateLightboxSlide() {
                    if (lightboxSlider) {
                        lightboxSlider.style.transform = `translateX(-${currentLightboxIndex * 100}%)`;
                    }
                    if (lightboxCounter) {
                        lightboxCounter.textContent = `${currentLightboxIndex + 1} / ${totalSlides} Foto`;
                    }
                }

                // Touch support for Lightbox
                if (lightboxSlider) {
                    let startX = 0;
                    let endX = 0;
                    lightboxSlider.addEventListener('touchstart', (e) => {
                        startX = e.touches[0].clientX;
                    }, { passive: true });
                    lightboxSlider.addEventListener('touchend', (e) => {
                        endX = e.changedTouches[0].clientX;
                        let diffX = startX - endX;
                        if (Math.abs(diffX) > 50) {
                            if (diffX > 0) {
                                nextLightbox();
                            } else {
                                prevLightbox();
                            }
                        }
                    }, { passive: true });
                }

                // Keyboard navigation for lightbox
                document.addEventListener('keydown', (e) => {
                    if (lightboxModal && lightboxModal.style.display === 'flex') {
                        if (e.key === 'ArrowRight') nextLightbox();
                        else if (e.key === 'ArrowLeft') prevLightbox();
                        else if (e.key === 'Escape') closeLightbox();
                    }
                });
            })();
        </script>
    @endpush
</x-layouts.app>