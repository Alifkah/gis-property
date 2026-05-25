<x-layouts.app>
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl text-white shadow-xl" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);">
        {{-- High performance decorative SVG background pattern --}}
        <div class="absolute inset-0 opacity-15 pointer-events-none">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        <div class="absolute -left-12 -top-12 size-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute -right-12 -bottom-12 size-72 rounded-full bg-violet-500/20 blur-3xl"></div>

        <div class="relative px-6 py-20 sm:px-10 sm:py-24 z-10">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-300 ring-1 ring-inset ring-indigo-500/20 mb-4 shadow-3xs">
                    <span class="size-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    Portal Geospasial Samarinda
                </span>
                <h1 class="text-balance text-3xl font-extrabold tracking-tight text-white sm:text-5xl leading-tight">
                    Temukan Properti Terbaik di Samarinda
                </h1>
                <p class="mt-4 text-pretty text-sm font-medium text-slate-300 sm:text-lg max-w-2xl mx-auto">
                    Jelajahi berbagai listing properti secara cerdas menggunakan analisis spasial, pemetaan fasilitas terdekat, dan visualisasi risiko mitigasi banjir secara real-time.
                </p>
            </div>

            <div class="mx-auto mt-12 max-w-4xl">
                <div class="p-6 rounded-2xl ring-1 ring-slate-200/50 shadow-2xl bg-white">
                    <form action="{{ route('explore') }}" method="GET" class="grid gap-4 sm:grid-cols-4 sm:items-end">
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Jenis Properti</label>
                            <select name="type" class="select mt-2" style="background: #ffffff; color: #0f172a;">
                                <option value="">Semua</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Kecamatan</label>
                            <select name="district" class="select mt-2" style="background: #ffffff; color: #0f172a;">
                                <option value="">Semua</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->name }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Rentang Harga</label>
                            <select name="price" class="select mt-2" style="background: #ffffff; color: #0f172a;">
                                <option value="">Semua</option>
                                <option value="0-250000000">0 - 250 jt</option>
                                <option value="250000000-750000000">250 jt - 750 jt</option>
                                <option value="750000000-2000000000">750 jt - 2 M</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary h-11 w-full sm:h-[42px] hover:shadow-lg hover:shadow-indigo-500/25 transition flex items-center justify-center gap-2">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <span>Cari via Peta</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur Utama --}}
    <section class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="group card p-6 hover:-translate-y-1 hover:shadow-md hover:ring-indigo-200/50 transition duration-300">
            <span class="grid size-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-105 duration-300">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.89-1.63a1.875 1.875 0 001.108-1.723V1.35c0-.847-.788-1.54-1.618-1.328l-4.708 1.205M9.623 3.328L3.3 5.4a1.875 1.875 0 00-1.29 1.783v12.285c0 .762.583 1.417 1.34 1.328l6.233-1.205m0-14.542L15.5 1.3M9 6.75L15.5 4.5m-.5 10.5L9 15" />
                </svg>
            </span>
            <div class="text-sm font-extrabold text-slate-900 mt-4">Eksplorasi Spasial Interaktif</div>
            <div class="mt-2 text-xs font-semibold leading-relaxed text-slate-500">Cari dan jelajahi berbagai listing properti berbasis peta interaktif dengan filter pencarian spasial yang dinamis dan super cepat.</div>
        </div>
        <div class="group card p-6 hover:-translate-y-1 hover:shadow-md hover:ring-indigo-200/50 transition duration-300">
            <span class="grid size-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-105 duration-300">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <div class="text-sm font-extrabold text-slate-900 mt-4">Smart Proximity Analytics</div>
            <div class="mt-2 text-xs font-semibold leading-relaxed text-slate-500">Ketahui fasilitas penting terdekat seperti sekolah, rumah sakit, dan pasar, lengkap dengan kalkulasi jarak tempuh presisi.</div>
        </div>
        <div class="group card p-6 hover:-translate-y-1 hover:shadow-md hover:ring-indigo-200/50 transition duration-300">
            <span class="grid size-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-105 duration-300">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0-4.5 4.5V21h9v-1.5A4.5 4.5 0 0 0 2.25 15ZM12 18.75V21M12 3a9 9 0 0 0-9 9m9-9a9 9 0 0 1 9 9m-9-9v2.25m9 6.75H21M3 12h2.25m11.364-5.636-1.591 1.591M6.393 17.607l1.591-1.591m8.25-8.25-1.591 1.591M7.984 6.393l1.591 1.591" />
                </svg>
            </span>
            <div class="text-sm font-extrabold text-slate-900 mt-4">Mitigasi Risiko Banjir</div>
            <div class="mt-2 text-xs font-semibold leading-relaxed text-slate-500">Mencegah kerugian finansial dengan memeriksa lapisan zona kerawanan banjir Kota Samarinda sebelum melakukan transaksi.</div>
        </div>
    </section>

    {{-- Listing Terbaru --}}
    <section class="mt-12">
        <div class="flex items-end justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <div class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="size-2 rounded-full bg-indigo-600"></span>
                    <span>Properti Terbaru</span>
                </div>
                <div class="mt-1 text-xs font-semibold text-slate-500">Kumpulan properti terkini yang baru ditambahkan di wilayah Kota Samarinda.</div>
            </div>
            <a href="{{ route('explore') }}" class="btn btn-outline py-2 px-3 text-xs flex items-center gap-1">
                <span>Lihat Semua</span>
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($recentProperties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    </section>
</x-layouts.app>
