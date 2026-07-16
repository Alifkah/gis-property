<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            @keyframes pulse-marker {
                0% { transform: scale(0.8); opacity: 0.5; }
                70% { transform: scale(1.5); opacity: 0; }
                100% { transform: scale(0.8); opacity: 0; }
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
                . '<stop offset="0%" stop-color="#0F4C5C" stop-opacity="0.12"/>'
                . '<stop offset="100%" stop-color="#0F4C5C" stop-opacity="0.04"/>'
                . '</linearGradient>'
                . '</defs>'
                . '<path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#0F4C5C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/>'
                . '<text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#0F4C5C" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">'
                . strtoupper($property->type)
                . '</text>'
                . '</svg>';
            $imageUrl = 'data:image/svg+xml;base64,' . base64_encode($placeholderSvg);
        }
        $waNumber = $property->user?->phone 
            ? preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $property->user->phone))
            : '';
    @endphp

    {{-- Breadcrumbs & Back Button --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <nav class="flex text-xs font-semibold text-slate-400 tracking-wide font-mono items-center gap-1.5">
            <a href="{{ route('home') }}" class="hover:text-brand-primary transition">Beranda</a>
            <i class="ti ti-chevron-right text-[10px]"></i>
            <a href="{{ route('properties.index') }}" class="hover:text-brand-primary transition">Properti</a>
            <i class="ti ti-chevron-right text-[10px]"></i>
            <span class="text-slate-600 truncate max-w-[200px]">{{ $property->title }}</span>
        </nav>
        
        <a href="javascript:history.back()" class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-brand-primary transition cursor-pointer">
            <i class="ti ti-arrow-left text-sm"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Image Gallery Section --}}
    <div class="mb-8">
        @if ($existingImages->isNotEmpty())
            {{-- Desktop Layout: 1 large (aspect-16/10) + 4 thumbnails (2x2 grid aspect-square) --}}
            <div class="hidden lg:grid grid-cols-[1.6fr_1fr] gap-3">
                @php
                    $mainImg = $existingImages->first();
                    $mainImgUrl = $mainImg ? Storage::disk('public')->url($mainImg->path) : $imageUrl;
                @endphp
                <div class="relative aspect-[16/10] rounded-2xl overflow-hidden group cursor-zoom-in bg-slate-900 border border-slate-100" onclick="openLightbox(0)">
                    <img src="{{ $mainImgUrl }}" alt="{{ $property->title }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.01]" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                    <div class="absolute bottom-4 right-4 z-10 rounded-full bg-black/60 backdrop-blur-md px-3.5 py-1.5 text-xs font-bold text-white">
                        <i class="ti ti-photo mr-1"></i>
                        <span>1 / {{ $existingImages->count() }} Foto</span>
                    </div>
                </div>

                {{-- Thumbnails grid (2x2) --}}
                <div class="grid grid-cols-2 gap-3">
                    @for ($i = 1; $i <= 4; $i++)
                        @php
                            $thumb = $existingImages->get($i);
                            $thumbUrl = $thumb ? Storage::disk('public')->url($thumb->path) : null;
                        @endphp
                        <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 border border-slate-100/50 group {{ $thumbUrl ? 'cursor-pointer' : 'opacity-40' }}"
                             @if($thumbUrl) onclick="openLightbox({{ $i }})" @endif>
                            @if($thumbUrl)
                                <img src="{{ $thumbUrl }}" alt="Foto {{ $i + 1 }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
                                @if($i === 4 && $existingImages->count() > 5)
                                    <div class="absolute inset-0 bg-black/55 backdrop-blur-xs flex flex-col items-center justify-center text-white">
                                        <span class="text-xl font-black">+{{ $existingImages->count() - 5 }}</span>
                                        <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5">Lainnya</span>
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
                                    <i class="ti ti-photo-off text-3xl"></i>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Mobile Layout: Horizontal swipe-snap slider --}}
            <div class="lg:hidden relative">
                <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none rounded-2xl gap-3">
                    @foreach ($existingImages as $idx => $img)
                        <div class="w-full shrink-0 aspect-[16/10] snap-start rounded-2xl overflow-hidden relative bg-slate-100" onclick="openLightbox({{ $idx }})">
                            <img src="{{ Storage::disk('public')->url($img->path) }}" alt="Foto {{ $idx + 1 }}" class="w-full h-full object-cover" />
                            <div class="absolute bottom-4 right-4 z-10 rounded-full bg-black/60 backdrop-blur-md px-3 py-1.5 text-xs font-bold text-white">
                                <span>{{ $idx + 1 }} / {{ $existingImages->count() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Default placeholders when no images exist --}}
            <div class="grid gap-3 lg:grid-cols-[1.6fr_1fr]">
                <div class="aspect-[16/10] rounded-2xl overflow-hidden bg-slate-100 flex items-center justify-center text-slate-300 border border-slate-100">
                    <i class="ti ti-photo-off text-5xl"></i>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="aspect-square rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 border border-slate-100/50"><i class="ti ti-photo-off text-3xl"></i></div>
                    <div class="aspect-square rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 border border-slate-100/50"><i class="ti ti-photo-off text-3xl"></i></div>
                    <div class="aspect-square rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 border border-slate-100/50"><i class="ti ti-photo-off text-3xl"></i></div>
                    <div class="aspect-square rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 border border-slate-100/50"><i class="ti ti-photo-off text-3xl"></i></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Main 2-column Grid --}}
    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px] items-start">
        {{-- Left Column (Details) --}}
        <div class="space-y-8">
            {{-- Title, location, badges --}}
            <section class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @if (($property->status ?? 'Tersedia') === 'Terjual')
                        <span class="inline-flex items-center rounded-full bg-slate-700 px-2.5 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">Terjual</span>
                    @endif
                    @if ($isNew)
                        <x-badge variant="new">Rumah Baru</x-badge>
                    @endif
                    @if ($isFloodSafe)
                        <x-badge variant="safe">Bebas Banjir</x-badge>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">{{ $property->title }}</h1>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-slate-500">
                    <i class="ti ti-map-pin text-slate-400 text-lg"></i>
                    <span>Kecamatan {{ $districtName ?? 'Kota Samarinda' }}, Samarinda, Kalimantan Timur</span>
                </div>
            </section>

            {{-- Stats row (4 cards) --}}
            <section class="grid gap-4 grid-cols-2 sm:grid-cols-4">
                <div class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                    <i class="ti ti-maximize text-brand-primary text-xl mb-3"></i>
                    <div class="text-xs font-semibold text-slate-500">Luas Tanah</div>
                    <div class="mt-1 text-lg font-extrabold text-slate-900 leading-none">{{ (int) $property->land_area }} m²</div>
                </div>
                <div class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                    <i class="ti ti-building text-brand-primary text-xl mb-3"></i>
                    <div class="text-xs font-semibold text-slate-500">Luas Bangunan</div>
                    <div class="mt-1 text-lg font-extrabold text-slate-900 leading-none">{{ (int) $property->building_area }} m²</div>
                </div>
                <div class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                    <i class="ti ti-bed text-brand-primary text-xl mb-3"></i>
                    <div class="text-xs font-semibold text-slate-500">Kamar Tidur</div>
                    <div class="mt-1 text-lg font-extrabold text-slate-900 leading-none">{{ (int) $property->bedroom }} Ruang</div>
                </div>
                <div class="flex flex-col items-start rounded-2xl bg-white p-4.5 border border-slate-200/50 hover:border-brand-primary/20 shadow-xs transition hover:shadow-md hover:-translate-y-0.5 duration-300">
                    <i class="ti ti-bath text-brand-primary text-xl mb-3"></i>
                    <div class="text-xs font-semibold text-slate-500">Kamar Mandi</div>
                    <div class="mt-1 text-lg font-extrabold text-slate-900 leading-none">{{ (int) $property->bathroom }} Ruang</div>
                </div>
            </section>

            {{-- Description --}}
            <section class="card p-6 bg-white border border-slate-200/50">
                <h3 class="text-base font-extrabold text-slate-950 border-b border-slate-100 pb-3 flex items-center gap-2 font-display">
                    <i class="ti ti-file-text text-brand-primary text-lg"></i>
                    <span>Deskripsi Properti</span>
                </h3>
                <div class="prose max-w-none text-sm text-slate-700 leading-relaxed mt-4 font-semibold">
                    {!! nl2br(e($property->description ?? 'Properti strategis yang dipasarkan di Kota Samarinda. Hubungi agen pengiklan untuk informasi ketersediaan lebih lanjut.')) !!}
                </div>
            </section>

            {{-- Map section (height 350px) --}}
            <section class="card p-6 bg-white border border-slate-200/50">
                <h3 class="text-base font-extrabold text-slate-950 border-b border-slate-100 pb-3 flex items-center gap-2 font-display">
                    <i class="ti ti-map-2 text-brand-primary text-lg"></i>
                    <span>Peta & Analisis Fasilitas Terdekat</span>
                </h3>
                
                <div class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <div class="flex flex-col">
                        <div class="rounded-2xl overflow-hidden shadow-xs ring-1 ring-slate-200/50 relative" style="height:350px;min-height:350px">
                            <div id="miniMap" class="relative z-0" style="height:350px;width:100%"></div>

                            {{-- MiniMap Route Overlay Info Panel --}}
                            <div id="miniRoutePanel" style="display:none;"
                                class="absolute top-3 right-3 z-[400] w-[215px] bg-white/95 backdrop-blur-md rounded-xl p-3 shadow-md border border-slate-200/60 transition">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider" id="miniRouteLabel">Petunjuk Rute</div>
                                <div class="mt-1 text-xs font-black text-slate-800" id="miniRouteDist">-</div>
                                <div class="mt-1 text-[10px] font-bold text-brand-primary bg-brand-primary/5 px-1.5 py-0.5 rounded inline-block" id="miniRouteTime">-</div>
                                <div class="mt-1.5 text-[9px] font-semibold text-slate-500 flex items-center justify-between">
                                    <span>Lalu Lintas:</span>
                                    <span id="miniTrafficStatus" class="font-black px-1.5 py-0.5 rounded"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl bg-slate-50/80 p-4 ring-1 ring-slate-200/50 shadow-2xs">
                            <div class="min-w-0">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Koordinat Properti</div>
                                <div class="mt-0.5 text-xs font-semibold text-slate-700 truncate">
                                    {{ number_format((float) $point['lat'], 6) }}, {{ number_format((float) $point['lng'], 6) }}
                                </div>
                            </div>
                            <button type="button" id="directionsBtn" onclick="toggleDirections()"
                                class="btn btn-outline py-2 px-3 text-xs flex items-center gap-1.5 shrink-0 shadow-3xs hover:bg-brand-primary/5 hover:text-brand-primary transition cursor-pointer">
                                <i class="ti ti-directions text-base text-brand-primary"></i>
                                <span id="directionsBtnText">Petunjuk Arah</span>
                            </button>
                        </div>
                    </div>

                    {{-- Proximities list --}}
                    <div class="flex flex-col gap-4 min-w-0">
                        @if ($isFloodSafe)
                            <div class="rounded-2xl bg-emerald-50/60 p-4 ring-1 ring-emerald-500/20 shadow-xs">
                                <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Status Mitigasi Banjir</div>
                                <div class="mt-2.5 flex items-center gap-2 text-sm font-extrabold text-emerald-950">
                                    <span class="grid size-7 place-items-center rounded-full bg-emerald-500 text-white shadow-xs">
                                        <i class="ti ti-check text-xs"></i>
                                    </span>
                                    <span>Bebas Banjir</span>
                                </div>
                                <p class="mt-2 text-xs text-emerald-700 leading-relaxed font-semibold">Properti berada di luar batas historis wilayah genangan air Kota Samarinda.</p>
                            </div>
                        @else
                            <div class="rounded-2xl bg-rose-50/60 p-4 ring-1 ring-rose-500/20 shadow-xs">
                                <div class="text-xs font-bold text-rose-800 uppercase tracking-wider">Status Mitigasi Banjir</div>
                                <div class="mt-2.5 flex items-center gap-2 text-sm font-extrabold text-rose-950">
                                    <span class="grid size-7 place-items-center rounded-full bg-rose-500 text-white shadow-xs">
                                        <i class="ti ti-alert-triangle text-xs"></i>
                                    </span>
                                    <span>Dekat Zona Genangan</span>
                                </div>
                                <p class="mt-2 text-xs text-rose-700 leading-relaxed font-semibold">Perhatian: Teridentifikasi berada dekat batas genangan air. Dianjurkan memeriksa ketinggian pondasi.</p>
                            </div>
                        @endif

                        <div class="rounded-2xl bg-slate-50/70 p-4 ring-1 ring-slate-200/70 shadow-xs flex-1 flex flex-col justify-between min-w-0">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Fasilitas Terdekat</div>
                                <div class="mt-3 grid gap-2 min-w-0">
                                    @foreach ($nearestAmenities as $amenity)
                                        @php
                                            $amenityType = strtolower($amenity->type ?? '');
                                            $iconClass = 'ti ti-map-pin';
                                            $iconColor = 'text-brand-primary bg-brand-primary/8';

                                            if (str_contains($amenityType, 'sekolah') || str_contains($amenityType, 'universitas') || str_contains($amenityType, 'pendidikan') || str_contains($amenityType, 'education')) {
                                                $iconClass = 'ti ti-school';
                                                $iconColor = 'text-amber-600 bg-amber-50';
                                            } elseif (str_contains($amenityType, 'sakit') || str_contains($amenityType, 'klinik') || str_contains($amenityType, 'puskesmas') || str_contains($amenityType, 'medis') || str_contains($amenityType, 'hospital')) {
                                                $iconClass = 'ti ti-building-hospital';
                                                $iconColor = 'text-rose-600 bg-rose-50';
                                            } elseif (str_contains($amenityType, 'pasar') || str_contains($amenityType, 'mall') || str_contains($amenityType, 'belanja') || str_contains($amenityType, 'supermarket') || str_contains($amenityType, 'market')) {
                                                $iconClass = 'ti ti-shopping-cart';
                                                $iconColor = 'text-emerald-600 bg-emerald-50';
                                            }
                                        @endphp
                                        <div onclick="showAmenityRoute({{ $loop->index }}, {{ $amenity->lat ?? 0 }}, {{ $amenity->lng ?? 0 }}, '{{ addslashes($amenity->name) }}', '{{ $amenity->type }}')"
                                            class="flex items-center justify-between gap-4 rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200/50 shadow-2xs hover:ring-slate-400/45 hover:bg-slate-50 transition min-w-0 cursor-pointer"
                                            title="Klik untuk melihat rute jalan">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="grid size-8 shrink-0 place-items-center rounded-lg {{ $iconColor }}">
                                                    <i class="{{ $iconClass }} text-base"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="truncate text-xs font-bold text-slate-800">{{ $amenity->name }}</div>
                                                    <div class="truncate text-[9px] font-bold text-slate-400 uppercase tracking-wider">{{ $amenity->type }}</div>
                                                </div>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <div class="text-xs font-extrabold text-brand-primary bg-brand-primary/8 px-2 py-0.5 rounded-md inline-block">
                                                    {{ number_format(((float) $amenity->distance_m) / 1000, 1) }} km
                                                </div>
                                                <div id="amenity-time-{{ $loop->index }}" class="text-[9px] font-bold text-emerald-600 mt-1 hidden text-right leading-none"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Right Column (Sticky price card, seller card, calculator) --}}
        <aside class="lg:sticky lg:top-24 lg:self-start space-y-4">
            {{-- Price Card --}}
            <div class="card p-6 bg-white border border-slate-200/50 shadow-lg">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Penawaran</div>
                <div class="text-3xl font-extrabold text-brand-accent mt-1">
                    Rp {{ number_format((float) $property->price, 0, ',', '.') }}
                </div>
                
                @if (($property->status ?? 'Tersedia') === 'Terjual')
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-600 border border-slate-200">
                        <span class="size-1.5 rounded-full bg-slate-400"></span>
                        Terjual
                    </div>
                @else
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700 border border-emerald-200">
                        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Tersedia & Siap Huni
                    </div>
                @endif

                <div class="mt-5 grid gap-2.5">
                    @if ($waNumber)
                        <a id="whatsappBtn"
                            href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo, saya tertarik dengan properti: ' . $property->title) }}"
                            target="_blank" rel="noopener"
                            class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 py-3 rounded-xl font-bold transition shadow-xs hover:shadow-md border-0 cursor-pointer">
                            <i class="ti ti-brand-whatsapp text-lg"></i>
                            <span>Hubungi via WhatsApp</span>
                        </a>
                    @else
                        <button type="button" class="btn btn-primary w-full py-3" disabled>Hubungi via WhatsApp</button>
                    @endif
                    <button type="button" onclick="openScheduleModal()" class="btn btn-outline w-full py-3">Jadwalkan Kunjungan</button>
                </div>
            </div>

            {{-- Seller Card --}}
            <div class="card p-5 bg-white border border-slate-200/50 shadow-sm">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Kontak Pengiklan</div>
                <div class="flex items-center gap-3">
                    @if($property->user?->logo_path)
                        <img src="{{ Storage::disk('public')->url($property->user->logo_path) }}" alt="{{ $property->user->company_name ?? $property->user->name }}" class="size-14 object-cover rounded-2xl border" />
                    @else
                        <div class="grid size-14 shrink-0 place-items-center rounded-2xl bg-brand-primary font-extrabold text-white text-lg shadow-xs">
                            {{ strtoupper(substr($property->user?->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1">
                            <div class="truncate text-sm font-extrabold text-slate-900">{{ $property->user?->company_name ?? $property->user?->name ?? 'Penjual' }}</div>
                            @if($property->user?->company_name)
                                <i class="ti ti-circle-check-filled text-brand-primary text-base" title="Terverifikasi"></i>
                            @endif
                        </div>
                        <div class="truncate text-xs font-semibold text-slate-500 mt-0.5">{{ $property->user?->phone ?? 'Nomor tidak tersedia' }}</div>
                    </div>
                </div>
                @if($property->user)
                    <a href="{{ route('sellers.show', $property->user->id) }}" class="inline-flex items-center justify-center w-full mt-4 text-xs font-bold text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 rounded-xl py-2.5 transition">
                        <span>Lihat Profil</span>
                        <i class="ti ti-arrow-narrow-right ml-1"></i>
                    </a>
                @endif
            </div>

            {{-- KPR Calculator --}}
            <div class="card p-6 bg-slate-50 border border-slate-200/30">
                <div class="text-sm font-extrabold text-slate-900 border-b border-slate-200/50 pb-2.5 flex items-center gap-2 font-display">
                    <i class="ti ti-calculator text-brand-primary text-base"></i>
                    <span>Simulasi KPR</span>
                </div>
                <div class="mt-4 grid gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-600">Uang Muka (DP)</label>
                            <span id="dpPercentLabel" class="text-xs font-bold text-brand-primary bg-brand-primary/8 px-1.5 py-0.5 rounded">20%</span>
                        </div>
                        <input id="dpInputDisplay" type="text" class="input" placeholder="100.000.000" />
                        <input id="dpInput" type="hidden" value="0" />
                        <input id="dpSlider" type="range" min="10" max="90" step="5" value="20"
                            class="w-full accent-brand-primary h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer mt-2.5" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-slate-600">Jangka Waktu (Tenor)</label>
                            <span id="termLabel" class="text-xs font-bold text-brand-primary bg-brand-primary/8 px-1.5 py-0.5 rounded">15 Tahun</span>
                        </div>
                        <input id="termSlider" type="range" min="1" max="30" step="1" value="15"
                            class="w-full accent-brand-primary h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer mt-1" />
                        <input id="termInput" type="hidden" value="15" />
                    </div>
                    <div class="rounded-2xl bg-brand-primary/5 p-4 ring-1 ring-brand-primary/10 shadow-3xs">
                        <div class="text-[10px] font-bold text-brand-primary uppercase tracking-wider">Estimasi Cicilan / Bulan</div>
                        <div id="installment" class="mt-1 text-2xl font-extrabold text-brand-primary">-</div>
                        <div class="mt-1.5 text-[9px] font-bold text-slate-400">Asumsi bunga tetap KPR 8% per tahun</div>
                    </div>
                </div>
            </div>

            {{-- Share --}}
            <div class="card p-6 bg-white border border-slate-200/50 shadow-sm">
                <div class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-2.5 flex items-center gap-2 font-display">
                    <i class="ti ti-share text-brand-primary text-base"></i>
                    <span>Bagikan Properti</span>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @php
                        $shareText = rawurlencode($property->title . ' - Temukan properti menarik ini di Samarinda: ' . request()->url());
                        $shareUrl = rawurlencode(request()->url());
                    @endphp
                    <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" rel="noopener"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 bg-[#25D366]/5 hover:bg-[#25D366]/10 text-[#25D366] transition group shadow-3xs cursor-pointer">
                        <i class="ti ti-brand-whatsapp text-2xl mb-1"></i>
                        <span class="text-[10px] font-bold">WhatsApp</span>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 bg-[#1877F2]/5 hover:bg-[#1877F2]/10 text-[#1877F2] transition group shadow-3xs cursor-pointer">
                        <i class="ti ti-brand-facebook text-2xl mb-1"></i>
                        <span class="text-[10px] font-bold">Facebook</span>
                    </a>
                    <button onclick="copyToClipboard()"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 bg-brand-primary/5 hover:bg-brand-primary/10 text-brand-primary transition group shadow-3xs cursor-pointer">
                        <i id="copyIcon" class="ti ti-copy text-2xl mb-1"></i>
                        <span id="copyText" class="text-[10px] font-bold">Salin Link</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- Mobile Sticky Bottom Bar --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-[999] bg-white border-t border-slate-200/80 p-4 flex items-center justify-between gap-4 safe-bottom">
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Harga Penawaran</div>
            <div class="text-xl font-extrabold text-brand-accent">Rp {{ number_format((float) $property->price, 0, ',', '.') }}</div>
        </div>
        <div class="flex-1 max-w-[200px]">
            @if ($waNumber)
                <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo, saya tertarik dengan properti: ' . $property->title) }}"
                    target="_blank" rel="noopener"
                    class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 rounded-xl text-sm font-bold py-2 shadow-xs cursor-pointer border-0">
                    <i class="ti ti-brand-whatsapp text-lg"></i>
                    <span>WhatsApp</span>
                </a>
            @else
                <button type="button" class="btn btn-primary w-full py-2 text-sm" disabled>Hubungi</button>
            @endif
        </div>
    </div>

    {{-- Lightbox Modal --}}
    @if ($existingImages->isNotEmpty())
        <div id="lightboxModal" style="display:none;" onclick="closeLightbox(event)"
            class="fixed inset-0 z-[2000] bg-black/95 flex items-center justify-center p-4 transition-all duration-300">
            
            <span id="lightboxCounter" class="absolute top-6 left-6 z-[2010] rounded-full bg-black/50 backdrop-blur-md px-3.5 py-1.5 text-xs font-bold text-white shadow-sm pointer-events-none">1 / 1</span>
            
            <button type="button" onclick="closeLightbox(event)"
                class="absolute top-6 right-6 z-[2010] size-11 rounded-full bg-black/50 backdrop-blur-md text-white hover:bg-black/80 flex items-center justify-center transition shadow-md border border-white/10 cursor-pointer">
                <i class="ti ti-x text-lg"></i>
            </button>

            <div class="w-full h-full relative flex items-center justify-center overflow-hidden">
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
                    <button type="button" onclick="prevLightbox(event)"
                        class="lightbox-nav-btn absolute left-4 top-1/2 -translate-y-1/2 size-12 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition shadow-lg cursor-pointer border-0">
                        <i class="ti ti-chevron-left text-2xl"></i>
                    </button>
                    <button type="button" onclick="nextLightbox(event)"
                        class="lightbox-nav-btn absolute right-4 top-1/2 -translate-y-1/2 size-12 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition shadow-lg cursor-pointer border-0">
                        <i class="ti ti-chevron-right text-2xl"></i>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Jadwalkan Kunjungan Modal --}}
    <div id="scheduleModal" style="display:none;" class="fixed inset-0 z-[2000] items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs flex" onclick="closeScheduleModal()">
        <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 relative ring-1 ring-slate-100 animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
            <button type="button" onclick="closeScheduleModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 cursor-pointer bg-transparent border-0">
                <i class="ti ti-x text-lg"></i>
            </button>
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-4">Jadwalkan Kunjungan</h3>
            <p class="text-xs font-semibold text-slate-500 mb-4 leading-relaxed">Pilih tanggal dan waktu kunjungan Anda. Kami akan mengirimkan notifikasi ke penjual dan menghubungkan Anda via WhatsApp.</p>
            
            <form id="scheduleForm" onsubmit="submitSchedule(event)">
                @csrf
                <div class="grid gap-4">
                    <div>
                        <label for="scheduleDate" class="text-xs font-bold text-slate-700 block mb-1">Tanggal Kunjungan</label>
                        <input type="date" id="scheduleDate" required min="{{ date('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-200/80 px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden" />
                    </div>
                    <div>
                        <label for="scheduleTime" class="text-xs font-bold text-slate-700 block mb-1">Waktu Kunjungan</label>
                        <input type="time" id="scheduleTime" required
                            class="w-full rounded-xl border border-slate-200/80 px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden" />
                    </div>
                    <div>
                        <label for="scheduleNote" class="text-xs font-bold text-slate-700 block mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea id="scheduleNote" rows="3" placeholder="Halo, saya ingin meninjau lokasi..."
                            class="w-full rounded-xl border border-slate-200/80 px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden resize-none"></textarea>
                    </div>
                </div>
                
                <button type="submit" id="submitScheduleBtn" class="mt-5 btn btn-primary w-full py-3.5 flex items-center justify-center gap-2 font-bold cursor-pointer border-0">
                    <i class="ti ti-calendar-event text-lg"></i>
                    <span>Kirim & Hubungi Penjual</span>
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const point = @json($point);
            const miniMap = L.map('miniMap', { zoomControl: false, attributionControl: false }).setView([point.lat, point.lng], 15);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd'
            }).addTo(miniMap);

            const markerIcon = L.divIcon({
                className: '',
                html: '<div style="width:16px;height:16px;border-radius:9999px;background:#E36414;border:3.5px solid #ffffff;box-shadow:0 4px 10px rgba(15,23,42,.35)"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });
            L.marker([point.lat, point.lng], { icon: markerIcon }).addTo(miniMap);

            // Force correct size
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
                
                if (routeLines.length > 0) {
                    clearRoutes();
                    if (userMarker) {
                        miniMap.removeLayer(userMarker);
                        userMarker = null;
                    }
                    if (userAccuracyCircle) {
                        miniMap.removeLayer(userAccuracyCircle);
                        userAccuracyCircle = null;
                    }
                    btnText.textContent = "Petunjuk Arah";
                    btn.classList.remove('bg-brand-primary/10', 'text-brand-primary');
                    miniMap.setView([point.lat, point.lng], 15);
                    return;
                }

                btnText.textContent = "Mencari Lokasi...";

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const uLat = position.coords.latitude;
                        const uLng = position.coords.longitude;
                        window.userLocation = { lat: uLat, lng: uLng };

                        // Draw user marker
                        const uIcon = L.divIcon({
                            className: '',
                            html: `<div style="position:relative;width:20px;height:20px;">
                                     <div style="position:absolute;width:20px;height:20px;background:#0F4C5C;border:3px solid #ffffff;border-radius:50%;box-shadow:0 0 8px rgba(0,0,0,0.3);z-index:2"></div>
                                     <div style="position:absolute;width:30px;height:30px;background:#0F4C5C;border-radius:50%;opacity:0.3;top:-5px;left:-5px;animation:pulse-marker 2s infinite;z-index:1"></div>
                                   </div>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        });
                        userMarker = L.marker([uLat, uLng], { icon: uIcon }).addTo(miniMap);
                        userAccuracyCircle = L.circle([uLat, uLng], {
                            radius: position.coords.accuracy,
                            color: '#0F4C5C',
                            fillColor: '#0F4C5C',
                            fillOpacity: 0.05,
                            weight: 1
                        }).addTo(miniMap);

                        try {
                            const route = await getRoute(uLat, uLng, point.lat, point.lng);
                            const polyline = L.geoJSON(route.geometry, {
                                style: { color: '#E36414', weight: 5, opacity: 0.8 }
                            }).addTo(miniMap);
                            routeLines.push(polyline);

                            const distKm = (route.distance / 1000).toFixed(1);
                            const traffic = getTrafficData();
                            const driveTime = Math.round((route.duration * traffic.multiplier) / 60);

                            document.getElementById('miniRouteLabel').textContent = "Rute Anda Ke Sini";
                            document.getElementById('miniRouteDist').textContent = `${distKm} km`;
                            document.getElementById('miniRouteTime').textContent = `${driveTime} mnt berkendara`;

                            const trafficStatusEl = document.getElementById('miniTrafficStatus');
                            if (trafficStatusEl) {
                                trafficStatusEl.textContent = traffic.status;
                                trafficStatusEl.className = `font-black px-1.5 py-0.5 rounded text-[8px] ${traffic.statusClass}`;
                            }
                            document.getElementById('miniRoutePanel').style.display = 'block';

                            miniMap.fitBounds(polyline.getBounds(), { padding: [40, 40] });
                            btnText.textContent = "Matikan Rute";
                            btn.classList.add('bg-brand-primary/10', 'text-brand-primary');
                        } catch (err) {
                            console.error("OSRM routing failed:", err);
                            const fallbackLine = L.polyline([[uLat, uLng], [point.lat, point.lng]], {
                                color: '#E36414', dashArray: '5, 10', weight: 4
                            }).addTo(miniMap);
                            routeLines.push(fallbackLine);
                            
                            const distKm = (distanceMeters(uLat, uLng, point.lat, point.lng) / 1000).toFixed(1);
                            document.getElementById('miniRouteLabel').textContent = "Rute Anda (Garis Lurus)";
                            document.getElementById('miniRouteDist').textContent = `${distKm} km`;
                            document.getElementById('miniRouteTime').textContent = "Rute jalan gagal";
                            document.getElementById('miniRoutePanel').style.display = 'block';

                            miniMap.fitBounds(fallbackLine.getBounds(), { padding: [40, 40] });
                            btnText.textContent = "Matikan Rute";
                            btn.classList.add('bg-brand-primary/10', 'text-brand-primary');
                        }
                    },
                    (error) => {
                        alert("Gagal mendeteksi lokasi GPS Anda.");
                        btnText.textContent = "Petunjuk Arah";
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            };

            window.showAmenityRoute = async function(idx, destLat, destLng, name, type) {
                clearRoutes();
                const timeEl = document.getElementById(`amenity-time-${idx}`);
                document.querySelectorAll('[id^="amenity-time-"]').forEach(el => el.classList.add('hidden'));

                if (timeEl) {
                    timeEl.textContent = 'Memuat rute...';
                    timeEl.classList.remove('hidden');
                }

                try {
                    const route = await getRoute(point.lat, point.lng, destLat, destLng);
                    const polyline = L.geoJSON(route.geometry, {
                        style: {
                            color: '#10B981', // Emerald
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
                    const fallbackLine = L.polyline([[point.lat, point.lng], [destLat, destLng]], {
                        color: '#10B981', dashArray: '5, 10', weight: 4
                    }).addTo(miniMap);
                    routeLines.push(fallbackLine);

                    const straightDist = distanceMeters(point.lat, point.lng, destLat, destLng);
                    const distKm = (straightDist / 1000).toFixed(1);
                    const walkTime = Math.round((straightDist / 1.39) / 60);

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

            function distanceMeters(lat1, lng1, lat2, lng2) {
                const earthRadius = 6371000.0;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLng / 2) * Math.sin(dLng / 2);
                return earthRadius * 2 * Math.asin(Math.min(1.0, Math.sqrt(a)));
            }

            const price = {{ (float) $property->price }};
            const dpInput = document.getElementById('dpInput');
            const dpInputDisplay = document.getElementById('dpInputDisplay');
            const dpSlider = document.getElementById('dpSlider');
            const dpPercentLabel = document.getElementById('dpPercentLabel');
            const termSlider = document.getElementById('termSlider');
            const termInput = document.getElementById('termInput');
            const termLabel = document.getElementById('termLabel');
            const out = document.getElementById('installment');

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

                const pct = Math.min(100, Math.max(0, Math.round((num / price) * 100)));
                dpSlider.value = pct;
                dpPercentLabel.textContent = `${pct}%`;
                calc();
            }

            function handleDpSliderInput() {
                const pct = parseInt(dpSlider.value, 10);
                const num = Math.round((pct / 100) * price);
                dpInput.value = num;
                dpInputDisplay.value = formatNumberString(num);
                dpPercentLabel.textContent = `${pct}%`;
                calc();
            }

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

            function copyToClipboard() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    const copyText = document.getElementById('copyText');
                    const copyIcon = document.getElementById('copyIcon');
                    if (copyText) copyText.textContent = 'Tersalin!';
                    if (copyIcon) {
                        copyIcon.className = 'ti ti-check text-2xl text-emerald-500 mb-1';
                    }
                    setTimeout(() => {
                        if (copyText) copyText.textContent = 'Salin Link';
                        if (copyIcon) {
                            copyIcon.className = 'ti ti-copy text-2xl mb-1';
                        }
                    }, 2000);
                }).catch(err => console.error(err));
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
                console.error(e);
            }

            // Slider and Lightbox State Logic
            (function () {
                const totalSlides = {{ $existingImages->count() }};
                if (totalSlides === 0) return;

                const lightboxSlider = document.getElementById('lightboxSlider');
                const lightboxModal = document.getElementById('lightboxModal');
                const lightboxCounter = document.getElementById('lightboxCounter');

                let currentLightboxIndex = 0;

                window.openLightbox = function (index) {
                    if (!lightboxModal) return;
                    currentLightboxIndex = index;
                    lightboxModal.style.display = 'flex';
                    updateLightboxSlide();
                };

                window.closeLightbox = function (e) {
                    if (e && e.target && typeof e.target.closest === 'function') {
                        if (e.target.tagName === 'IMG' || e.target.closest('.lightbox-nav-btn')) {
                            return;
                        }
                    }
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

            // Visit Scheduling JS Functions
            window.openScheduleModal = function () {
                const modal = document.getElementById('scheduleModal');
                if (modal) modal.style.display = 'flex';
            };

            window.closeScheduleModal = function () {
                const modal = document.getElementById('scheduleModal');
                if (modal) modal.style.display = 'none';
            };

            window.submitSchedule = function (e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitScheduleBtn');
                const btnText = submitBtn.querySelector('span');
                const originalText = btnText ? btnText.textContent : 'Kirim & Hubungi Penjual';
                
                submitBtn.disabled = true;
                if (btnText) btnText.textContent = 'Mengirim...';
                
                const date = document.getElementById('scheduleDate').value;
                const time = document.getElementById('scheduleTime').value;
                const note = document.getElementById('scheduleNote').value;
                
                fetch('{{ route('properties.schedule', $property->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ date, time, note })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const formattedDate = date.split('-').reverse().join('-');
                        const propertyTitle = @json($property->title);
                        const waNumber = @json($waNumber);
                        
                        let message = `Halo, saya ingin menjadwalkan kunjungan untuk melihat properti "${propertyTitle}" pada:\n`;
                        message += `- Tanggal: ${formattedDate}\n`;
                        message += `- Waktu: ${time} WITA\n`;
                        if (note.trim() !== '') {
                            message += `- Catatan: ${note}\n`;
                        }
                        message += `\nApakah Anda bersedia? Terima kasih!`;
                        
                        if (waNumber) {
                            const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;
                            window.open(waUrl, '_blank');
                        } else {
                            alert('Jadwal kunjungan Anda berhasil dikirim ke Penjual!');
                        }
                        
                        closeScheduleModal();
                        document.getElementById('scheduleForm').reset();
                    } else {
                        alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    if (btnText) btnText.textContent = originalText;
                });
            };
        </script>
    @endpush
</x-layouts.app>