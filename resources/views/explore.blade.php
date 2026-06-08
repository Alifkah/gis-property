<x-layouts.blank>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div x-data="{ showResults: false, showFilters: false, showLayers: true }" class="flex h-dvh w-full overflow-hidden bg-brand-bg flex-col md:flex-row">

        {{-- Kiri: Hasil Pencarian (desktop sidebar / mobile bottom sheet) --}}
        <aside class="hidden md:flex shrink-0 flex-col border-r border-slate-200/80 bg-white shadow-sm z-[900]"
               style="width:310px;min-width:310px">

            {{-- Header: branding + tombol kembali --}}
            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <a
                    href="{{ route('home') }}"
                    class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200/60 px-3 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 hover:text-brand-primary hover:border-brand-primary/20 shadow-3xs"
                >
                    <svg class="size-4 shrink-0 transition group-hover:-translate-x-0.5 text-slate-500 group-hover:text-brand-primary" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            {{-- Results count --}}
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil Pencarian</div>
                <div id="resultCount" class="mt-1 text-xs font-black text-slate-700">Memuat data...</div>
            </div>

            <div class="flex-1 overflow-y-auto px-3.5 py-4 scrollbar-thin">
                <div id="propertyList" class="grid gap-3"></div>
            </div>
        </aside>
            {{-- Mobile Results Bottom Sheet --}}
        <div x-show="showResults"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 z-[950] bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 md:hidden"
             style="max-height: 80vh;">
            <div class="sticky top-0 bg-white rounded-t-3xl px-4 pt-3 pb-3 border-b border-slate-100 flex items-center justify-between">
                <div class="w-10 h-1 rounded-full bg-slate-200 mx-auto absolute top-2 left-1/2 -translate-x-1/2"></div>
                <div class="text-xs font-black text-slate-900 mt-2">Hasil Pencarian</div>
                <button @click="showResults = false" class="grid size-8 place-items-center rounded-xl text-slate-400 hover:bg-slate-50 mt-2">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto px-3.5 py-4" style="max-height: calc(80vh - 60px);">
                <div id="propertyListMobile" class="grid gap-3"></div>
            </div>
        </div>

        {{-- Mobile Results Overlay Backdrop --}}
        <div x-show="showResults" @click="showResults = false" x-cloak class="fixed inset-0 z-[940] bg-slate-900/40 md:hidden"></div>

        {{-- Mobile Filter Bottom Sheet --}}
        <div x-show="showFilters"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 z-[950] bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 md:hidden"
             style="max-height: 85vh;">
            <div class="sticky top-0 bg-white rounded-t-3xl px-4 pt-3 pb-3 border-b border-slate-100 flex items-center justify-between">
                <div class="w-10 h-1 rounded-full bg-slate-200 mx-auto absolute top-2 left-1/2 -translate-x-1/2"></div>
                <div class="text-xs font-black text-slate-900 mt-2 flex items-center gap-2">
                    <svg class="size-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filter Pencarian
                </div>
                <button @click="showFilters = false" class="grid size-8 place-items-center rounded-xl text-slate-400 hover:bg-slate-50 mt-2">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-col min-h-0 h-[calc(85vh-60px)]" id="mobileFilterContent">
                {{-- Filter content will be moved here via JS --}}
            </div>
        </div>

        {{-- Mobile Filter Backdrop --}}
        <div x-show="showFilters" @click="showFilters = false" x-cloak class="fixed inset-0 z-[940] bg-slate-900/40 md:hidden"></div>

        {{-- Tengah: Peta --}}
        <main class="relative flex-1">
            <div id="map" class="h-full w-full z-0"></div>

            {{-- GPS Alert Banner --}}
            <div id="gpsAlert" style="display:none;" class="absolute top-4 left-1/2 -translate-x-1/2 z-[900] w-[90%] max-w-md bg-rose-50 border border-rose-200 rounded-2xl p-3 shadow-xl flex items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-rose-500 text-white shadow-xs animate-pulse">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <div class="text-xs font-black text-rose-950">GPS Tidak Aktif</div>
                        <div class="text-[10px] font-semibold text-rose-700 leading-tight">Aktifkan GPS Anda untuk melihat petunjuk arah rute jalan langsung dari lokasi Anda.</div>
                    </div>
                </div>
                <button type="button" onclick="trackUserLocation(true)" class="btn bg-rose-600 text-white hover:bg-rose-700 py-1.5 px-3 text-[10px] font-bold rounded-xl shrink-0 shadow-xs cursor-pointer">Aktifkan</button>
            </div>

            {{-- Floating Route Panel --}}
            <div id="routePanel" style="display:none;" class="absolute top-4 right-4 z-[900] w-[280px] bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-slate-200/60 transition duration-300">
                <div class="text-xs font-black text-slate-900 border-b border-slate-100 pb-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg class="size-4.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.89-2.201a.75.75 0 00.407-.672V5.25a.75.75 0 00-1.077-.671L15 6.75l-6-2.25-4.89 2.201A.75.75 0 003.75 5.375v12.235a.75.75 0 001.077.671L9 15.75l6 2.25z" />
                        </svg>
                        <span>Informasi Rute Jalan</span>
                    </div>
                    <button type="button" onclick="clearRoutes()" class="size-6 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 flex items-center justify-center transition cursor-pointer">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3.5 space-y-3">
                    {{-- User -> Property --}}
                    <div id="userRouteInfo" style="display:none;">
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Rute Anda ke Properti</div>
                        <div class="mt-1 flex items-center justify-between text-xs font-extrabold text-slate-800">
                            <span id="userRouteDist">-</span>
                            <span id="userRouteTime" class="text-brand-primary bg-brand-primary/5 px-1.5 py-0.5 rounded text-[10px]">-</span>
                        </div>
                    </div>
                    {{-- Property -> Facility --}}
                    <div>
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider" id="facilityRouteLabel">Fasilitas Terdekat</div>
                        <div class="mt-1 flex items-center justify-between text-xs font-extrabold text-slate-800">
                            <span id="facilityRouteDist">-</span>
                            <span id="facilityRouteTime" class="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded text-[10px]">-</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile FAB Bar --}}
            <div class="absolute bottom-5 left-0 right-0 flex justify-center gap-2 z-[930] md:hidden px-4">
                <button @click="showResults = !showResults; showFilters = false"
                        class="flex items-center gap-1.5 bg-white text-slate-800 text-xs font-bold px-3.5 py-2.5 rounded-2xl shadow-xl border border-slate-200 active:scale-95 transition">
                    <svg class="size-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span id="resultCountMobile">Lihat Hasil</span>
                </button>
                <button @click="showFilters = !showFilters; showResults = false"
                        class="flex items-center gap-1.5 bg-brand-primary text-white text-xs font-bold px-3.5 py-2.5 rounded-2xl shadow-xl active:scale-95 transition">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('home') }}"
                   class="flex items-center gap-1.5 bg-white text-slate-600 text-xs font-bold px-3 py-2.5 rounded-2xl shadow-xl border border-slate-200 active:scale-95 transition">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
            </div>
        </main>


        <div id="layerControlPanel" style="display:none">
            <!-- Card Panel -->
            <div x-show="showLayers" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="w-[220px] bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-slate-200/60 transition duration-300">
                <div class="text-xs font-black text-slate-900 border-b border-slate-100 pb-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <svg class="size-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        <span>Lapisan Peta</span>
                    </div>
                    <button @click="showLayers = false" class="size-6 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 flex items-center justify-center transition cursor-pointer">
                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 grid gap-2.5">
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Zona Banjir</span>
                        <input id="toggleFlood" type="checkbox" class="size-4.5 rounded border-slate-300 accent-brand-primary cursor-pointer" checked />
                    </label>
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Batas Admin</span>
                        <input id="toggleDistricts" type="checkbox" class="size-4.5 rounded border-slate-300 accent-brand-primary cursor-pointer" checked />
                    </label>
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Mode Gelap Peta</span>
                        <input id="toggleDarkMode" type="checkbox" class="size-4.5 rounded border-slate-300 accent-brand-primary cursor-pointer" />
                    </label>
                </div>
                <div class="mt-3.5 pt-3.5 border-t border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Legenda</div>
                    <div class="mt-2 grid gap-1.5">
                        <div class="flex items-center gap-2 text-[11px] font-bold text-slate-600">
                            <span class="size-2.5 rounded-sm bg-emerald-600 ring-1 ring-emerald-700/10 shadow-3xs"></span>
                            <span>Bebas Banjir</span>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] font-bold text-slate-600">
                            <span class="size-2.5 rounded-sm bg-[var(--color-brand-warning)] ring-1 ring-brand-warning/10 shadow-3xs" style="background-color: #9A031E;"></span>
                            <span>Zona Banjir</span>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] font-bold text-slate-600">
                            <span class="size-2.5 rounded-sm bg-[var(--color-brand-accent)] ring-1 ring-brand-accent/10 shadow-3xs" style="background-color: #E36414;"></span>
                            <span>Properti</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Open Button -->
            <button x-show="!showLayers" 
                    x-cloak
                    @click="showLayers = true"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="size-10 bg-white/95 backdrop-blur-md rounded-xl shadow-md border border-slate-200/60 flex items-center justify-center text-slate-700 hover:text-brand-primary hover:bg-white transition cursor-pointer"
                    title="Buka Lapisan Peta">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.75L6.429 9.75 12 13.75l5.571-4L12 5.75zm-5.571 8l5.571 4 5.571-4" />
                </svg>
            </button>
        </div>

        {{-- Kanan: Filter Pencarian (desktop only) --}}
        <aside class="hidden md:flex shrink-0 flex-col bg-white border-l border-slate-200/80 shadow-sm z-10" style="width:310px;min-width:310px">
            <div class="px-4 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="text-xs font-black text-slate-900 flex items-center gap-2">
                    <svg class="size-4.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    <span>Filter Pencarian</span>
                </div>
            </div>
            <div id="desktopFilterParent" class="flex-1 flex flex-col min-h-0 overflow-hidden">
                <div id="filterFormContainer" class="flex-1 flex flex-col min-h-0 overflow-hidden">
                    <div class="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin">
                        <div class="grid gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipe Properti</label>
                                <select id="filterType" class="select mt-1.5">
                                    <option value="">Semua</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kecamatan</label>
                                <select id="filterDistrict" class="select mt-1.5">
                                    <option value="">Semua</option>
                                    @foreach ($districtFeatures['features'] as $feature)
                                        <option value="{{ $feature['properties']['name'] }}">{{ $feature['properties']['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rentang Harga</label>
                                <select id="filterPrice" class="select mt-1.5">
                                    <option value="">Semua</option>
                                    <option value="0-250000000">0 – 250 jt</option>
                                    <option value="250000000-750000000">250 jt – 750 jt</option>
                                    <option value="750000000-2000000000">750 jt – 2 M</option>
                                    <option value="2000000000-999999999999">2 M+</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dekat Fasilitas</label>
                                <select id="filterAmenityType" class="select mt-1.5">
                                    <option value="">Semua</option>
                                    @foreach ($amenityTypes as $amenityType)
                                        <option value="{{ $amenityType }}">{{ $amenityType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="amenityIdGroup" class="opacity-40 transition-opacity">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Fasilitas <span class="text-[9px] font-normal text-slate-400">(pilih tipe dulu)</span></label>
                                <select id="filterAmenityId" class="select mt-1.5" disabled>
                                    <option value="">Semua</option>
                                </select>
                            </div>
                            <div id="amenityRadiusGroup" class="opacity-40 transition-opacity">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Radius Fasilitas <span class="text-[9px] font-normal text-slate-400">(pilih tipe dulu)</span></label>
                                <select id="filterAmenityRadius" class="select mt-1.5" disabled>
                                    <option value="500">500 m</option>
                                    <option value="1000" selected>1 km</option>
                                    <option value="2000">2 km</option>
                                    <option value="5000">5 km</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Listing</label>
                                <select id="filterStatus" class="select mt-1.5">
                                    <option value="">Semua</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Terjual">Terjual</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Urutan Harga</label>
                                <select id="filterSort" class="select mt-1.5">
                                    <option value="desc">Tertinggi</option>
                                    <option value="asc">Terendah</option>
                                </select>
                            </div>
 
                            <div class="flex flex-wrap gap-2 pt-1 border-t border-slate-100 pt-3">
                                <button type="button" class="pill" data-pill="Rumah">Rumah</button>
                                <button type="button" class="pill" data-pill="Tanah">Tanah</button>
                                <button type="button" class="pill" data-pill="BebasBanjir">Bebas Banjir</button>
                            </div>
 
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/60 shadow-3xs mt-2 mb-4">
                                <div class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                                    <svg class="size-4 text-brand-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <span>Cari Sekitar Titik</span>
                                </div>
                                <div class="mt-1 text-[10px] font-semibold text-slate-500">Klik lokasi mana saja di peta untuk menentukan koordinat pusat.</div>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[9px] font-bold text-slate-400 uppercase">Latitude</label>
                                        <input id="centerLat" type="text" class="input mt-1 text-center" readonly />
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold text-slate-400 uppercase">Longitude</label>
                                        <input id="centerLng" type="text" class="input mt-1 text-center" readonly />
                                    </div>
                                </div>
                                <div class="mt-3.5">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Radius Jangkauan</label>
                                    <select id="radiusM" class="select mt-1">
                                        <option value="">Tanpa radius</option>
                                        <option value="500">500 m</option>
                                        <option value="1000" selected>1 km</option>
                                        <option value="2000">2 km</option>
                                        <option value="5000">5 km</option>
                                    </select>
                                </div>
                                <button id="clearCenter" type="button" class="btn btn-outline mt-3 w-full py-2.5 text-xs font-bold">Reset Titik Pusat</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col gap-2 shrink-0">
                        <button id="applyFilters" @click="showFilters = false" class="btn btn-primary w-full py-3 font-bold shadow-md hover:shadow-brand-primary/20 transition">Terapkan Filter</button>
                        @auth
                            <button id="activateSearchAlertBtn" type="button" class="btn btn-outline text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 border-brand-primary/20 w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold cursor-pointer">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span>Aktifkan Alarm Properti</span>
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </aside>

    </div>


    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const districts = @json($districtFeatures);
            const floodZones = @json($floodZoneFeatures);

            const map = L.map('map', { zoomControl: false }).setView([-0.5, 117.15], 12);
            L.control.zoom({ position: 'bottomleft' }).addTo(map);

            // Register Lapisan Peta as a proper Leaflet control so it's always on top
            var LayerPanel = L.Control.extend({
                options: { position: 'topleft' },
                onAdd: function () {
                    var panel = document.getElementById('layerControlPanel');
                    panel.style.display = 'block';
                    L.DomEvent.disableClickPropagation(panel);
                    L.DomEvent.disableScrollPropagation(panel);
                    return panel;
                }
            });
            new LayerPanel().addTo(map);

            const osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            });

            const darkTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            });

            osmTiles.addTo(map);

            document.getElementById('toggleDarkMode').addEventListener('change', (e) => {
                if (e.target.checked) {
                    map.removeLayer(osmTiles);
                    darkTiles.addTo(map);
                } else {
                    map.removeLayer(darkTiles);
                    osmTiles.addTo(map);
                }
            });

            const markerLayer = L.layerGroup().addTo(map);

            const districtLayer = L.geoJSON(districts, {
                style: {
                    color: '#0F4C5C',
                    weight: 2,
                    fillOpacity: 0.06
                }
            }).addTo(map);

            const floodLayer = L.geoJSON(floodZones, {
                style: {
                    color: '#9A031E',
                    weight: 2,
                    fillOpacity: 0.14
                }
            }).addTo(map);

            const markerIcon = L.divIcon({
                className: '',
                html: '<div style="width:16px;height:16px;border-radius:9999px;background:#E36414;border:3.5px solid #ffffff;box-shadow:0 4px 10px rgba(15,23,42,.35)"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            const propertyList = document.getElementById('propertyList');
            const applyFilters = document.getElementById('applyFilters');
            const filterType = document.getElementById('filterType');
            const filterDistrict = document.getElementById('filterDistrict');
            const filterPrice = document.getElementById('filterPrice');
            const filterAmenityType = document.getElementById('filterAmenityType');
            const filterAmenityId = document.getElementById('filterAmenityId');
            const filterAmenityRadius = document.getElementById('filterAmenityRadius');
            const filterStatus = document.getElementById('filterStatus');
            const filterSort = document.getElementById('filterSort');
            const pills = Array.from(document.querySelectorAll('.pill'));
            const centerLat = document.getElementById('centerLat');
            const centerLng = document.getElementById('centerLng');
            const radiusM = document.getElementById('radiusM');
            const clearCenter = document.getElementById('clearCenter');

            // Baca query string dari URL (dikirim dari form search di halaman home)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type')) { filterType.value = urlParams.get('type'); }
            if (urlParams.get('district')) { filterDistrict.value = urlParams.get('district'); }
            if (urlParams.get('price')) { filterPrice.value = urlParams.get('price'); }
            if (urlParams.get('status')) { filterStatus.value = urlParams.get('status'); }

            let selectedPills = new Set();
            let properties = [];
            let meta = { page: 1, per_page: 50, total: 0 };
            let center = null;
            let centerMarker = null;
            let centerCircle = null;
            let markerMap = new Map();

            // GPS and Routing Variables
            window.userLocation = null;
            let userMarker = null;
            let userAccuracyCircle = null;
            let routeLines = [];
            window.allAmenities = [];

            // Add custom Locate control button
            var LocateControl = L.Control.extend({
                options: { position: 'bottomleft' },
                onAdd: function() {
                    var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                    var button = L.DomUtil.create('a', 'leaflet-bar-part', container);
                    button.innerHTML = `<svg class="size-5 text-slate-700" style="margin: 5px auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>`;
                    button.title = "Cari Lokasi Saya";
                    button.style.backgroundColor = '#ffffff';
                    button.style.cursor = 'pointer';
                    button.style.display = 'flex';
                    button.style.alignItems = 'center';
                    button.style.justifyContent = 'center';

                    L.DomEvent.on(button, 'click', function(e) {
                        L.DomEvent.stopPropagation(e);
                        trackUserLocation(true);
                    });
                    return container;
                }
            });
            new LocateControl().addTo(map);

            window.trackUserLocation = function(panTo = false) {
                if (!navigator.geolocation) {
                    alert("Geolokasi tidak didukung oleh browser Anda.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
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
                            userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
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
                            }).addTo(map);
                        }

                        if (panTo) {
                            map.setView([lat, lng], 15, { animate: true });
                        }

                        // Hide GPS warning if it is visible
                        const gpsAlert = document.getElementById('gpsAlert');
                        if (gpsAlert) gpsAlert.style.display = 'none';
                    },
                    (error) => {
                        console.warn("GPS tracking error:", error);
                        // Show GPS alert banner
                        const gpsAlert = document.getElementById('gpsAlert');
                        if (gpsAlert) gpsAlert.style.display = 'flex';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
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

            function findNearestAmenity(propertyLat, propertyLng) {
                if (!window.allAmenities || window.allAmenities.length === 0) return null;
                let nearest = null;
                let minDistance = Infinity;

                window.allAmenities.forEach(amenity => {
                    const d = distanceMeters(propertyLat, propertyLng, amenity.lat, amenity.lng);
                    if (d < minDistance) {
                        minDistance = d;
                        nearest = amenity;
                    }
                });

                if (nearest) {
                    nearest.distance_m = minDistance;
                }
                return nearest;
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

            window.clearRoutes = function() {
                routeLines.forEach(line => map.removeLayer(line));
                routeLines = [];
                document.getElementById('routePanel').style.display = 'none';
            };

            window.showPropertyRoute = async function(propId) {
                const prop = properties.find(p => p.id === propId);
                if (!prop) return;

                // Clear old routes
                window.clearRoutes();

                let drawnBounds = L.latLngBounds();

                // 1. Rute dari Properti ke Fasilitas Terdekat
                const nearestAmenity = findNearestAmenity(prop.lat, prop.lng);
                if (nearestAmenity) {
                    try {
                        const route = await getRoute(prop.lat, prop.lng, nearestAmenity.lat, nearestAmenity.lng);
                        const polyline = L.geoJSON(route.geometry, {
                            style: {
                                color: '#10B981', // Emerald green
                                weight: 5,
                                opacity: 0.8
                            }
                        }).addTo(map);
                        routeLines.push(polyline);
                        drawnBounds.extend(polyline.getBounds());

                        const distKm = (route.distance / 1000).toFixed(1);
                        const driveTime = Math.round(route.duration / 60);
                        const walkTime = Math.round((route.distance / 1.39) / 60);

                        document.getElementById('facilityRouteLabel').textContent = `Fasilitas Terdekat: ${nearestAmenity.name} (${nearestAmenity.type})`;
                        document.getElementById('facilityRouteDist').textContent = `${distKm} km`;
                        document.getElementById('facilityRouteTime').textContent = `${driveTime} mnt berkendara / ${walkTime} mnt jalan kaki`;
                    } catch (err) {
                        console.error("Error routing to amenity:", err);
                        // Fallback straight line
                        const fallbackLine = L.polyline([[prop.lat, prop.lng], [nearestAmenity.lat, nearestAmenity.lng]], {
                            color: '#10B981',
                            dashArray: '5, 10',
                            weight: 4
                        }).addTo(map);
                        routeLines.push(fallbackLine);
                        drawnBounds.extend(fallbackLine.getBounds());
                        
                        const distKm = (nearestAmenity.distance_m / 1000).toFixed(1);
                        const walkTime = Math.round((nearestAmenity.distance_m / 1.39) / 60);
                        document.getElementById('facilityRouteLabel').textContent = `Fasilitas Terdekat: ${nearestAmenity.name} (${nearestAmenity.type})`;
                        document.getElementById('facilityRouteDist').textContent = `${distKm} km (garis lurus)`;
                        document.getElementById('facilityRouteTime').textContent = `~${walkTime} mnt jalan kaki`;
                    }
                }

                // 2. Rute dari User ke Properti
                let hasUserRoute = false;
                const startPoint = window.userLocation || (center ? { lat: center.lat, lng: center.lng } : null);
                
                if (startPoint) {
                    try {
                        const route = await getRoute(startPoint.lat, startPoint.lng, prop.lat, prop.lng);
                        const polyline = L.geoJSON(route.geometry, {
                            style: {
                                color: '#E36414', // Orange/Brand-accent
                                weight: 5,
                                opacity: 0.8
                            }
                        }).addTo(map);
                        routeLines.push(polyline);
                        drawnBounds.extend(polyline.getBounds());

                        const distKm = (route.distance / 1000).toFixed(1);
                        const driveTime = Math.round(route.duration / 60);
                        const walkTime = Math.round((route.distance / 1.39) / 60);

                        document.getElementById('userRouteInfo').style.display = 'block';
                        document.getElementById('userRouteDist').textContent = `${distKm} km`;
                        document.getElementById('userRouteTime').textContent = `${driveTime} mnt berkendara / ${walkTime} mnt jalan kaki`;
                        hasUserRoute = true;
                    } catch (err) {
                        console.error("Error routing from start to property:", err);
                        // Fallback straight line
                        const fallbackLine = L.polyline([[startPoint.lat, startPoint.lng], [prop.lat, prop.lng]], {
                            color: '#E36414',
                            dashArray: '5, 10',
                            weight: 4
                        }).addTo(map);
                        routeLines.push(fallbackLine);
                        drawnBounds.extend(fallbackLine.getBounds());

                        const straightDist = distanceMeters(startPoint.lat, startPoint.lng, prop.lat, prop.lng);
                        const distKm = (straightDist / 1000).toFixed(1);
                        document.getElementById('userRouteInfo').style.display = 'block';
                        document.getElementById('userRouteDist').textContent = `${distKm} km (garis lurus)`;
                        document.getElementById('userRouteTime').textContent = `Rute jalan gagal dimuat`;
                        hasUserRoute = true;
                    }
                } else {
                    document.getElementById('userRouteInfo').style.display = 'none';
                }

                // Show the panel
                document.getElementById('routePanel').style.display = 'block';

                // Fit map bounds to show the routes
                if (routeLines.length > 0) {
                    map.fitBounds(drawnBounds, { padding: [50, 50] });
                }
            };

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            }

            function formatDistance(meters) {
                if (!Number.isFinite(Number(meters))) return null;
                const km = Number(meters) / 1000;
                return `${km.toFixed(1)} km`;
            }

            function imageUrl(type, imageUrl) {
                if (imageUrl) return imageUrl;
                const typeUpper = (type || 'PROPERTI').toUpperCase();
                const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" fill="none"><rect width="400" height="250" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4f46e5" stop-opacity="0.12"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0.04"/></linearGradient></defs><path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/><text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">${typeUpper}</text></svg>`;
                return 'data:image/svg+xml;base64,' + btoa(svg);
            }

            function renderList(items) {
                const resultCount = document.getElementById('resultCount');
                const resultCountMobile = document.getElementById('resultCountMobile');
                const propertyListMobile = document.getElementById('propertyListMobile');

                const countText = items.length > 0
                    ? `${items.length} properti ditemukan`
                    : 'Tidak ada properti';

                if (resultCount) resultCount.textContent = countText;
                if (resultCountMobile) resultCountMobile.textContent = `${items.length} Hasil`;

                const html = items.length === 0
                    ? '<div class="py-12 text-center text-xs font-semibold text-slate-400">Tidak ada properti sesuai filter.</div>'
                    : items.map((p) => {
                    const isSold = p.status === 'Terjual';
                    const badges = isSold
                        ? '<span class="inline-flex items-center rounded-full bg-slate-700 px-2 py-0.5 text-[9px] font-bold text-white shadow-3xs">Terjual</span>'
                        : [
                            p.is_new ? '<span class="inline-flex items-center rounded-full bg-brand-primary px-2 py-0.5 text-[9px] font-bold text-white shadow-3xs">Rumah Baru</span>' : '',
                            p.is_flood_safe ? '<span class="inline-flex items-center rounded-full bg-emerald-600 px-2 py-0.5 text-[9px] font-bold text-white shadow-3xs">Bebas Banjir</span>' : ''
                          ].join('');

                    return `
                        <button type="button" class="w-full text-left card overflow-hidden hover:shadow-md hover:ring-brand-primary/20 transition min-w-0" data-id="${p.id}">
                            <div class="relative overflow-hidden bg-slate-100" style="height:120px">
                                <img src="${imageUrl(p.type, p.image_url)}" alt="${p.title}" class="h-full w-full object-cover transition duration-300 hover:scale-[1.03] ${isSold ? 'opacity-60' : ''}" loading="lazy" />
                                <div class="absolute left-2.5 top-2.5 flex flex-wrap gap-1">${badges}</div>
                            </div>
                            <div class="p-3">
                                <div class="text-xs font-extrabold text-slate-800 truncate">${p.title}</div>
                                <div class="mt-0.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">${p.district_name ?? 'Samarinda'}</div>
                                <div class="mt-2.5 flex items-center justify-between gap-2 border-t border-slate-50 pt-2">
                                    <div class="text-xs font-extrabold ${isSold ? 'text-slate-400' : 'text-brand-primary'}">Rp ${formatCurrency(p.price)}</div>
                                    <span class="text-[10px] font-bold text-slate-500">${p.amenity_distance_m !== null && p.amenity_distance_m !== undefined ? formatDistance(p.amenity_distance_m) : `${p.land_area} m²`}</span>
                                </div>
                            </div>
                        </button>
                    `;
                }).join('');

                propertyList.innerHTML = html;
                if (propertyListMobile) propertyListMobile.innerHTML = html;

                // Attach click events for both lists
                [propertyList, propertyListMobile].forEach((list) => {
                    if (!list) return;
                    Array.from(list.querySelectorAll('button[data-id]')).forEach((el) => {
                        el.addEventListener('click', () => {
                            const id = parseInt(el.dataset.id, 10);
                            const target = items.find((p) => p.id === id);
                            if (!target) return;
                            map.setView([target.lat, target.lng], 15, { animate: true });
                            const marker = markerMap.get(id);
                            if (marker) {
                                marker.openPopup();
                            }
                            window.showPropertyRoute(id);
                        });
                    });
                });
            }

            async function fetchProperties(params) {
                const url = new URL('/api/explore/properties', window.location.origin);
                Object.entries(params).forEach(([key, value]) => {
                    if (value === '' || value === null || value === undefined) return;
                    url.searchParams.set(key, String(value));
                });

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Gagal memuat data properti.');
                }

                return await response.json();
            }

            async function fetchAmenities(type) {
                const url = new URL('/api/explore/amenities', window.location.origin);
                if (type) {
                    url.searchParams.set('type', type);
                }

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Gagal memuat fasilitas.');
                }

                const payload = await response.json();
                return payload.data ?? [];
            }

            async function fetchGeojson(params) {
                const url = new URL('/api/explore/properties.geojson', window.location.origin);
                Object.entries(params).forEach(([key, value]) => {
                    if (value === '' || value === null || value === undefined) return;
                    url.searchParams.set(key, String(value));
                });

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Gagal memuat GeoJSON.');
                }

                return await response.json();
            }

            function renderMarkers(geojson) {
                markerLayer.clearLayers();
                markerMap = new Map();

                const features = geojson?.features ?? [];

                features.forEach((feature) => {
                    const p = feature?.properties ?? {};
                    const geometry = feature?.geometry ?? {};
                    const coords = geometry?.coordinates ?? null;
                    if (!coords || coords.length < 2) return;

                    const lng = coords[0];
                    const lat = coords[1];

                    // Try to find image_url from the already-fetched properties list
                    const listItem = properties.find((item) => item.id === Number(p.id));
                    const imgSrc = imageUrl(p.type, listItem?.image_url ?? null);

                    const popupHtml = `
                        <div style="width:240px; font-family:'Inter', sans-serif">
                            <img src="${imgSrc}" alt="${p.title}" style="width:100%;height:120px;object-fit:cover;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06)" />
                            <div style="padding:12px 0 0 0">
                                <div style="font-size:14px;font-weight:900;color:#0F4C5C">Rp ${formatCurrency(p.price)}</div>
                                <div style="margin-top:4px;font-size:12px;font-weight:800;color:#0f172a;line-height:1.4">${p.title}</div>
                                <a href="/properties/${p.id}" style="margin-top:12px;display:inline-flex;align-items:center;justify-content:center;padding:10px 12px;border-radius:10px;background:#E36414;color:#fff;font-size:11px;font-weight:800;text-decoration:none;width:100%;box-shadow:0 4px 12px rgba(227,100,20,0.15)">Lihat Detail Properti</a>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(markerLayer);
                    marker.bindPopup(popupHtml, { closeButton: true, className: 'rounded-xl' });

                    marker.on('click', () => {
                        window.showPropertyRoute(Number(p.id));
                    });

                    if (Number.isFinite(Number(p.id))) {
                        markerMap.set(Number(p.id), marker);
                    }
                });
            }

            function renderPagination() {
                const current = meta.page ?? 1;
                const perPage = meta.per_page ?? 50;
                const total = meta.total ?? 0;
                const totalPages = Math.max(1, Math.ceil(total / perPage));
                const isPrevDisabled = current <= 1;
                const isNextDisabled = current >= totalPages;

                const html = `
                    <div class="mt-4 flex items-center justify-between gap-2 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200/50 shadow-3xs">
                        <button type="button" id="prevPage" class="btn btn-outline py-1.5 px-3 text-xs font-extrabold ${isPrevDisabled ? 'pointer-events-none opacity-50' : ''}">Prev</button>
                        <div class="text-[10px] font-black text-slate-500">Hal ${current} / ${totalPages}</div>
                        <button type="button" id="nextPage" class="btn btn-outline py-1.5 px-3 text-xs font-extrabold ${isNextDisabled ? 'pointer-events-none opacity-50' : ''}">Next</button>
                    </div>
                `;

                propertyList.insertAdjacentHTML('beforeend', html);

                const prevBtn = document.getElementById('prevPage');
                const nextBtn = document.getElementById('nextPage');
                if (prevBtn && !isPrevDisabled) {
                    prevBtn.addEventListener('click', () => {
                        meta.page = Math.max(1, current - 1);
                        apply();
                    });
                }
                if (nextBtn && !isNextDisabled) {
                    nextBtn.addEventListener('click', () => {
                        meta.page = Math.min(totalPages, current + 1);
                        apply();
                    });
                }
            }

            async function apply() {
                const type = filterType.value;
                const district = filterDistrict.value;
                const price = filterPrice.value;
                const sort = filterSort.value;
                const status = filterStatus.value;
                const pillsLocal = new Set(selectedPills);

                const floodSafe = pillsLocal.has('BebasBanjir') ? 1 : null;
                const remoteType = pillsLocal.has('Rumah') ? 'Rumah' : pillsLocal.has('Tanah') ? 'Tanah' : type;
                const radius = radiusM.value || '';
                const centerLatValue = center ? center.lat : '';
                const centerLngValue = center ? center.lng : '';
                const amenityType = filterAmenityType.value || '';
                const amenityId = filterAmenityId.value || '';
                const amenityRadius = filterAmenityRadius.disabled ? '' : (filterAmenityRadius.value || '');

                const skeletonHtml = Array.from({ length: 4 }).map(() => `
                    <div class="w-full card overflow-hidden animate-pulse border border-slate-100 bg-white rounded-2xl">
                        <div class="bg-slate-200" style="height:120px"></div>
                        <div class="p-3 space-y-2.5">
                            <div class="h-3.5 bg-slate-200 rounded w-3/4"></div>
                            <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                            <div class="pt-2 flex items-center justify-between gap-2">
                                <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                                <div class="h-3 bg-slate-200 rounded w-1/4"></div>
                            </div>
                        </div>
                    </div>
                `).join('');
                propertyList.innerHTML = skeletonHtml;

                try {
                    const [priceMin, priceMax] = price ? price.split('-') : ['', ''];
                    const payload = await fetchProperties({
                        type: remoteType || '',
                        district: district || '',
                        status: status || '',
                        sort,
                        flood_safe: floodSafe,
                        price_min: priceMin,
                        price_max: priceMax,
                        page: meta.page ?? 1,
                        per_page: meta.per_page ?? 50,
                        center_lat: centerLatValue,
                        center_lng: centerLngValue,
                        radius_m: radius,
                        amenity_type: amenityType,
                        amenity_id: amenityId,
                        amenity_radius_m: amenityRadius,
                    });

                    properties = payload.data ?? [];
                    meta = payload.meta ?? meta;

                    renderList(properties);
                    renderPagination();

                    try {
                        const geojson = await fetchGeojson({
                            type: remoteType || '',
                            district: district || '',
                            status: status || '',
                            sort,
                            flood_safe: floodSafe,
                            price_min: priceMin,
                            price_max: priceMax,
                            page: meta.page ?? 1,
                            per_page: meta.per_page ?? 50,
                            center_lat: centerLatValue,
                            center_lng: centerLngValue,
                            radius_m: radius,
                            amenity_type: amenityType,
                            amenity_id: amenityId,
                            amenity_radius_m: amenityRadius,
                        });

                        renderMarkers(geojson);
                    } catch (e) {
                        markerLayer.clearLayers();
                    }
                } catch (e) {
                    propertyList.innerHTML = '<div class="text-sm font-semibold text-rose-600">Gagal memuat data. Coba lagi.</div>';
                    markerLayer.clearLayers();
                }
            }

            pills.forEach((pill) => {
                pill.addEventListener('click', () => {
                    const key = pill.dataset.pill;
                    if (selectedPills.has(key)) {
                        selectedPills.delete(key);
                        pill.classList.remove('pill-active');
                    } else {
                        selectedPills.add(key);
                        pill.classList.add('pill-active');
                    }
                });
            });

            applyFilters.addEventListener('click', () => {
                meta.page = 1;
                apply();
            });

            // Sync filter container position between desktop and mobile bottom sheet
            function checkFiltersPosition() {
                const isMobile = window.innerWidth < 768;
                const container = document.getElementById('filterFormContainer');
                const desktopParent = document.getElementById('desktopFilterParent');
                const mobileParent = document.getElementById('mobileFilterContent');
                
                if (isMobile) {
                    if (container && mobileParent && container.parentNode !== mobileParent) {
                        mobileParent.appendChild(container);
                    }
                } else {
                    if (container && desktopParent && container.parentNode !== desktopParent) {
                        desktopParent.appendChild(container);
                    }
                }
            }
            window.addEventListener('resize', checkFiltersPosition);
            document.addEventListener('DOMContentLoaded', checkFiltersPosition);
            checkFiltersPosition(); // Run immediately

            apply();

            async function syncAmenitySelects() {
                const type = filterAmenityType.value;
                const amenityIdGroup = document.getElementById('amenityIdGroup');
                const amenityRadiusGroup = document.getElementById('amenityRadiusGroup');
                filterAmenityId.innerHTML = '<option value="">Semua</option>';

                if (!type) {
                    filterAmenityId.disabled = true;
                    filterAmenityRadius.disabled = true;
                    filterAmenityRadius.value = '1000';
                    if (amenityIdGroup) { amenityIdGroup.style.opacity = '0.4'; }
                    if (amenityRadiusGroup) { amenityRadiusGroup.style.opacity = '0.4'; }
                    return;
                }

                filterAmenityId.disabled = false;
                filterAmenityRadius.disabled = false;
                if (amenityIdGroup) { amenityIdGroup.style.opacity = '1'; }
                if (amenityRadiusGroup) { amenityRadiusGroup.style.opacity = '1'; }

                try {
                    const amenities = await fetchAmenities(type);
                    filterAmenityId.innerHTML = [
                        '<option value="">Semua</option>',
                        ...amenities.map((a) => `<option value="${a.id}">${a.name}</option>`)
                     ].join('');
                } catch (e) {
                    filterAmenityId.innerHTML = '<option value="">Gagal memuat</option>';
                }
            }

            filterAmenityType.addEventListener('change', () => {
                meta.page = 1;
                syncAmenitySelects().then(apply);
            });

            filterAmenityId.addEventListener('change', () => {
                meta.page = 1;
                apply();
            });

            filterAmenityRadius.addEventListener('change', () => {
                meta.page = 1;
                apply();
            });

            function setCenterPoint(lat, lng) {
                center = { lat, lng };
                centerLat.value = Number(lat).toFixed(6);
                centerLng.value = Number(lng).toFixed(6);

                if (centerMarker) {
                    map.removeLayer(centerMarker);
                }
                if (centerCircle) {
                    map.removeLayer(centerCircle);
                }

                centerMarker = L.marker([lat, lng]).addTo(map);

                const radiusValue = Number(radiusM.value || 0);
                if (radiusValue > 0) {
                    centerCircle = L.circle([lat, lng], { radius: radiusValue, color: '#0F4C5C', fillOpacity: 0.08 }).addTo(map);
                }
            }

            map.on('click', (e) => {
                setCenterPoint(e.latlng.lat, e.latlng.lng);
                meta.page = 1;
                apply();
            });

            radiusM.addEventListener('change', () => {
                if (!center) return;
                setCenterPoint(center.lat, center.lng);
                meta.page = 1;
                apply();
            });

            clearCenter.addEventListener('click', () => {
                center = null;
                centerLat.value = '';
                centerLng.value = '';

                if (centerMarker) {
                    map.removeLayer(centerMarker);
                    centerMarker = null;
                }
                if (centerCircle) {
                    map.removeLayer(centerCircle);
                    centerCircle = null;
                }

                meta.page = 1;
                apply();
            });

            document.getElementById('toggleFlood').addEventListener('change', (e) => {
                if (e.target.checked) {
                    floodLayer.addTo(map);
                } else {
                    map.removeLayer(floodLayer);
                }
            });

            document.getElementById('toggleDistricts').addEventListener('change', (e) => {
                if (e.target.checked) {
                    districtLayer.addTo(map);
                } else {
                    map.removeLayer(districtLayer);
                }
            });

            @auth
            document.getElementById('activateSearchAlertBtn')?.addEventListener('click', function() {
                const type = document.getElementById('filterType').value;
                const district = document.getElementById('filterDistrict').value;
                const priceRange = document.getElementById('filterPrice').value;
                
                let min_price = null;
                let max_price = null;
                if (priceRange) {
                    const parts = priceRange.split('-');
                    min_price = parts[0];
                    max_price = parts[1];
                }

                fetch('{{ route('property-alerts.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        type: type || null,
                        min_price: min_price || null,
                        max_price: max_price || null,
                        district_name: district || null
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Gagal mengaktifkan alarm.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan koneksi.');
                });
            });
            @endauth

            // Jalankan pelacakan GPS awal & preload semua data fasilitas untuk rute jalan
            trackUserLocation(false);
            fetchAmenities().then((data) => {
                window.allAmenities = data;
            }).catch((err) => {
                console.error("Gagal memuat fasilitas untuk rute:", err);
            });
        </script>
        <style>
            .pill { display:inline-flex; align-items:center; justify-content:center; border-radius:12px; padding:7px 11px; font-size:11px; font-weight:800; background:#f8fafc; color:#475569; border:1px solid #e2e8f0; transition:all .2s; cursor:pointer; }
            .pill:hover { background:#f1f5f9; border-color:#cbd5e1 }
            .pill-active { background:#0F4C5C; color:#fff; border-color:#0F4C5C; }
            .pill-active:hover { background:#0b3945; border-color:#0b3945; }

            /* Custom Leaflet overrides */
            .leaflet-popup-content-wrapper {
                border-radius: 16px !important;
                padding: 4px !important;
                box-shadow: 0 10px 30px rgba(15,23,42,0.15) !important;
                border: 1px solid rgba(226,232,240,0.8) !important;
            }
            .leaflet-popup-tip {
                box-shadow: 0 10px 30px rgba(15,23,42,0.15) !important;
            }
            .leaflet-container a.leaflet-popup-close-button {
                top: 12px !important;
                right: 12px !important;
                color: #64748b !important;
                font-size: 16px !important;
                font-weight: bold !important;
            }

            @keyframes pulse-marker {
                0% { transform: scale(0.8); opacity: 0.5; }
                70% { transform: scale(1.5); opacity: 0; }
                100% { transform: scale(0.8); opacity: 0; }
            }
        </style>
    @endpush
</x-layouts.blank>
