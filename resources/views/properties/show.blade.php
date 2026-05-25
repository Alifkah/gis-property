<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div>
            {{-- Galeri foto --}}
            <div class="relative">
                @if ($property->images->isNotEmpty())
                    <section class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_280px]">
                        <div class="group overflow-hidden rounded-2xl bg-slate-100 aspect-[16/10] sm:aspect-auto sm:h-[350px] shadow-xs hover:shadow-md transition duration-300">
                            <img src="{{ Storage::url($property->images->first()->path) }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                        </div>
                        @if ($property->images->count() > 1)
                            <div class="grid grid-cols-2 gap-3 lg:grid-cols-1">
                                @foreach ($property->images->skip(1)->take(2) as $image)
                                    <div class="group overflow-hidden rounded-2xl bg-slate-100 aspect-[16/10] lg:aspect-auto lg:h-[168px] shadow-xs hover:shadow-md transition duration-300">
                                        <img src="{{ Storage::url($image->path) }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                    <div class="absolute bottom-4 right-4 z-10 rounded-xl bg-slate-900/80 backdrop-blur-xs px-3 py-1.5 text-xs font-bold text-white shadow-xs flex items-center gap-1.5">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <span>{{ $property->images->count() }} Foto</span>
                    </div>
                @else
                    @php
                        $svg1 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4f46e5" stop-opacity="0.15"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0.05"/></linearGradient></defs><path d="M220 240 l80-60 80 60 M240 220 v60h120v-60 M280 280 v-30h40v30" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">EKSTERIOR</text></svg>';
                        $svg2 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#06b6d4" stop-opacity="0.15"/><stop offset="100%" stop-color="#4f46e5" stop-opacity="0.05"/></linearGradient></defs><path d="M220 260 h160 M240 260 v-30 h120 v30 M250 210 h100" stroke="#0891b2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#0891b2" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">INTERIOR</text></svg>';
                        $svg3 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.15"/><stop offset="100%" stop-color="#ec4899" stop-opacity="0.05"/></linearGradient></defs><path d="M220 280 v-50 h160 v50 M250 230 h100" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#7c3aed" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">RUANGAN</text></svg>';
                        $img1 = 'data:image/svg+xml;base64,'.base64_encode($svg1);
                        $img2 = 'data:image/svg+xml;base64,'.base64_encode($svg2);
                        $img3 = 'data:image/svg+xml;base64,'.base64_encode($svg3);
                    @endphp
                    <section class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_280px]">
                        <div class="group overflow-hidden rounded-2xl bg-slate-100 h-[350px] shadow-xs hover:shadow-md transition duration-300">
                            <img src="{{ $img1 }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                        </div>
                        <div class="grid grid-cols-2 gap-3 lg:grid-cols-1">
                            <div class="group overflow-hidden rounded-2xl bg-slate-100 lg:h-[168px] shadow-xs hover:shadow-md transition duration-300">
                                <img src="{{ $img2 }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                            </div>
                            <div class="group overflow-hidden rounded-2xl bg-slate-100 lg:h-[168px] shadow-xs hover:shadow-md transition duration-300">
                                <img src="{{ $img3 }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" />
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            <section class="mt-8">
                <div class="flex flex-wrap items-center gap-2">
                    @if (($property->status ?? 'Tersedia') === 'Terjual')
                        <span class="inline-flex items-center rounded-full bg-slate-700 px-2.5 py-1 text-xs font-semibold text-white shadow-xs">Terjual</span>
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
                    <svg class="size-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>Kecamatan {{ $districtName ?? 'Kota Samarinda' }}, Samarinda, Kalimantan Timur</span>
                </div>

                <div class="mt-6 grid gap-4 grid-cols-2 sm:grid-cols-4">
                    <div class="flex flex-col items-start rounded-2xl bg-white p-4 shadow-xs ring-1 ring-slate-200/60 transition hover:shadow-sm">
                        <svg class="size-5 text-indigo-500 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h7v7H4z" /><path d="M13 13h7v7h-7z" /><path d="M13 4h7v7h-7z" /><path d="M4 13h7v7H4z" />
                        </svg>
                        <div class="text-xs font-semibold text-slate-500">Luas Tanah</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->land_area }} m²</div>
                    </div>
                    <div class="flex flex-col items-start rounded-2xl bg-white p-4 shadow-xs ring-1 ring-slate-200/60 transition hover:shadow-sm">
                        <svg class="size-5 text-indigo-500 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <div class="text-xs font-semibold text-slate-500">Luas Bangunan</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->building_area }} m²</div>
                    </div>
                    <div class="flex flex-col items-start rounded-2xl bg-white p-4 shadow-xs ring-1 ring-slate-200/60 transition hover:shadow-sm">
                        <svg class="size-5 text-indigo-500 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 4v16" />
                            <path d="M2 17h20" />
                            <path d="M22 8v12" />
                            <path d="M2 8h20" />
                            <path d="M6 12h4a2 2 0 0 0 2-2V8H4v2a2 2 0 0 0 2 2Z" />
                        </svg>
                        <div class="text-xs font-semibold text-slate-500">Kamar Tidur</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->bedroom }} Ruang</div>
                    </div>
                    <div class="flex flex-col items-start rounded-2xl bg-white p-4 shadow-xs ring-1 ring-slate-200/60 transition hover:shadow-sm">
                        <svg class="size-5 text-indigo-500 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-1C4.3 2.5 3 4.3 3.5 5.5l2 2" />
                            <path d="M3 11h18a2 2 0 0 1 2 2v2a6 6 0 0 1-6 6H7a6 6 0 0 1-6-6v-2a2 2 0 0 1-2-2Z" />
                            <path d="M7 21v2M17 21v2" />
                        </svg>
                        <div class="text-xs font-semibold text-slate-500">Kamar Mandi</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->bathroom }} Ruang</div>
                    </div>
                </div>
            </section>

            <section class="mt-8 card p-6">
                <div class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.89-1.63a1.875 1.875 0 001.108-1.723V1.35c0-.847-.788-1.54-1.618-1.328l-4.708 1.205M9.623 3.328L3.3 5.4a1.875 1.875 0 00-1.29 1.783v12.285c0 .762.583 1.417 1.34 1.328l6.233-1.205m0-14.542L15.5 1.3M9 6.75L15.5 4.5m-.5 10.5L9 15" />
                    </svg>
                    <span>Analisis Geospasial & Lingkungan</span>
                </div>
                <div class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <div class="rounded-2xl overflow-hidden shadow-xs ring-1 ring-slate-200/50" style="height:280px;min-height:280px">
                        <div id="miniMap" class="relative z-0" style="height:280px;width:100%"></div>
                    </div>
                    <div class="flex flex-col gap-4">
                        @if ($isFloodSafe)
                            <div class="rounded-2xl bg-emerald-50/60 p-4 ring-1 ring-emerald-500/20 shadow-xs">
                                <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Status Banjir</div>
                                <div class="mt-2.5 flex items-center gap-2 text-sm font-extrabold text-emerald-950">
                                    <span class="grid size-7 place-items-center rounded-full bg-emerald-500 text-white shadow-xs">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M20 6 9 17l-5-5" />
                                        </svg>
                                    </span>
                                    <span>Bebas Banjir (Aman)</span>
                                </div>
                                <p class="mt-2 text-xs text-emerald-700 leading-relaxed font-semibold">Properti ini berada di luar zona rawan genangan banjir Samarinda berdasarkan analisis peta kontur spasial terbaru.</p>
                            </div>
                        @else
                            <div class="rounded-2xl bg-rose-50/60 p-4 ring-1 ring-rose-500/20 shadow-xs">
                                <div class="text-xs font-bold text-rose-800 uppercase tracking-wider">Status Banjir</div>
                                <div class="mt-2.5 flex items-center gap-2 text-sm font-extrabold text-rose-950">
                                    <span class="grid size-7 place-items-center rounded-full bg-rose-500 text-white shadow-xs">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M18 6 6 18M6 6l12 12" />
                                        </svg>
                                    </span>
                                    <span>Zona Rawan Banjir</span>
                                </div>
                                <p class="mt-2 text-xs text-rose-700 leading-relaxed font-semibold">Perhatian: Properti terdeteksi berada di dalam atau dekat zona genangan air tinggi. Disarankan memeriksa ketinggian fondasi bangunan.</p>
                            </div>
                        @endif

                        <div class="rounded-2xl bg-slate-50/70 p-4 ring-1 ring-slate-200/70 shadow-xs flex-1 flex flex-col justify-between">
                            <div>
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aksesibilitas Fasilitas Terdekat</div>
                                <div class="mt-3 grid gap-2">
                                    @foreach ($nearestAmenities as $amenity)
                                        @php
                                            $amenityType = strtolower($amenity->type ?? '');
                                            $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />'; // map-pin
                                            $iconColor = 'text-indigo-500 bg-indigo-50';

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
                                        <div class="flex items-center justify-between gap-4 rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200/50 shadow-2xs hover:ring-slate-300/60 transition">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="grid size-8 shrink-0 place-items-center rounded-lg {{ $iconColor }}">
                                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        {!! $iconPath !!}
                                                    </svg>
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="truncate text-xs font-bold text-slate-800">{{ $amenity->name }}</div>
                                                    <div class="truncate text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $amenity->type }}</div>
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-xs font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md">
                                                {{ number_format(((float) $amenity->distance_m) / 1000, 1) }} km
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
                <div class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
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
                <div class="text-2xl font-black text-indigo-700 mt-1">Rp {{ number_format((float) $property->price, 0, ',', '.') }}</div>

                @if (($property->status ?? 'Tersedia') === 'Terjual')
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-extrabold text-slate-600 ring-1 ring-slate-200">
                        <span class="size-2 rounded-full bg-slate-400"></span>
                        Properti Sudah Terjual
                    </div>
                @else
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700 ring-1 ring-emerald-200">
                        <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Tersedia & Siap Huni
                    </div>
                @endif

                <div class="mt-5 grid gap-2.5">
                    @if ($property->user?->phone)
                        @php
                            $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $property->user->phone));
                        @endphp
                        <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo, saya tertarik dengan properti: '.$property->title) }}" target="_blank" rel="noopener" class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold transition shadow-xs hover:shadow-md">
                            <svg class="size-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <span>Hubungi via WhatsApp</span>
                        </a>
                    @else
                        <button type="button" class="btn btn-primary w-full py-3" disabled>Hubungi via WhatsApp</button>
                    @endif
                    <button type="button" class="btn btn-outline w-full py-3">Jadwalkan Kunjungan</button>
                </div>

                <div class="mt-6 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/50 shadow-2xs">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Kontak Pengiklan</div>
                    <div class="flex items-center gap-3">
                        <div class="grid size-11 shrink-0 place-items-center rounded-2xl bg-indigo-600 font-extrabold text-white shadow-xs">
                            {{ strtoupper(substr($property->user?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-extrabold text-slate-900">{{ $property->user?->name ?? 'Penjual' }}</div>
                            <div class="truncate text-xs font-semibold text-slate-500">{{ $property->user?->phone ?? 'Nomor tidak tersedia' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 card p-6 shadow-sm">
                <div class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-2.5 flex items-center gap-2">
                    <svg class="size-4.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    <span>Simulasi KPR</span>
                </div>
                <div class="mt-4 grid gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-600">Uang Muka (DP)</label>
                            <span id="dpPercentLabel" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">20%</span>
                        </div>
                        <input id="dpInputDisplay" type="text" class="input" placeholder="100.000.000" />
                        <input id="dpInput" type="hidden" value="0" />
                        <input id="dpSlider" type="range" min="10" max="90" step="5" value="20" class="w-full accent-indigo-600 h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer mt-2.5" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-600">Jangka Waktu (Tenor)</label>
                            <span id="termLabel" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">15 Tahun</span>
                        </div>
                        <input id="termSlider" type="range" min="1" max="30" step="1" value="15" class="w-full accent-indigo-600 h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer mt-1" />
                        <input id="termInput" type="hidden" value="15" />
                    </div>
                    <div class="rounded-2xl bg-indigo-50/50 p-4 ring-1 ring-indigo-500/10 shadow-3xs">
                        <div class="text-[10px] font-bold text-indigo-950 uppercase tracking-wider">Estimasi Cicilan / Bulan</div>
                        <div id="installment" class="mt-1 text-xl font-black text-indigo-700">-</div>
                        <div class="mt-1.5 text-[10px] font-bold text-slate-400">Asumsi bunga tetap KPR 8% per tahun</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const point = @json($point);
            const miniMap = L.map('miniMap', { zoomControl: false, attributionControl: false }).setView([point.lat, point.lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(miniMap);
            L.marker([point.lat, point.lng]).addTo(miniMap);

            // Force correct size: immediately + after layout settles
            miniMap.invalidateSize({ animate: false });
            setTimeout(() => miniMap.invalidateSize({ animate: false }), 200);
            setTimeout(() => miniMap.invalidateSize({ animate: false }), 600);
            window.addEventListener('load', () => miniMap.invalidateSize({ animate: false }));

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
        </script>
    @endpush
</x-layouts.app>
