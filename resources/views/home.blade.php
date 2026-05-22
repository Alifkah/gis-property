<x-layouts.app>
    <section class="relative overflow-hidden rounded-3xl text-white shadow-2xl" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);">
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
        <div class="absolute -left-12 -top-12 size-64 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute -right-12 -bottom-12 size-64 rounded-full bg-violet-500/20 blur-3xl"></div>

        <div class="relative px-6 py-16 sm:px-10 sm:py-20 z-10">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-300 ring-1 ring-inset ring-indigo-500/20 mb-4">
                    <span class="size-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    Samarinda GIS Portal
                </span>
                <h1 class="text-balance text-3xl font-extrabold tracking-tight text-white sm:text-5xl leading-none">
                    Temukan Properti Terbaik di Samarinda
                </h1>
                <p class="mt-4 text-pretty text-sm font-medium text-slate-300 sm:text-lg max-w-2xl mx-auto">
                    Jelajahi listing properti menggunakan analisis spasial, fasilitas terdekat, dan mitigasi risiko banjir secara interaktif.
                </p>
            </div>

            <div class="mx-auto mt-10 max-w-4xl">
                <div class="p-5 rounded-2xl ring-1 ring-slate-200 shadow-2xl" style="background: #ffffff;">
                    <form action="{{ route('explore') }}" method="GET" class="grid gap-4 sm:grid-cols-4 sm:items-end">
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Jenis Properti</label>
                            <select name="type" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" style="background: #ffffff; color: #0f172a;">
                                <option class="text-slate-900 bg-white" value="">Semua</option>
                                @foreach ($types as $type)
                                    <option class="text-slate-900 bg-white" value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Kecamatan</label>
                            <select name="district" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" style="background: #ffffff; color: #0f172a;">
                                <option class="text-slate-900 bg-white" value="">Semua</option>
                                @foreach ($districts as $district)
                                    <option class="text-slate-900 bg-white" value="{{ $district->name }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Rentang Harga</label>
                            <select name="price" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" style="background: #ffffff; color: #0f172a;">
                                <option class="text-slate-900 bg-white" value="">Semua</option>
                                <option class="text-slate-900 bg-white" value="0-250000000">0 - 250 jt</option>
                                <option class="text-slate-900 bg-white" value="250000000-750000000">250 jt - 750 jt</option>
                                <option class="text-slate-900 bg-white" value="750000000-2000000000">750 jt - 2 M</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary h-11 w-full sm:h-[42px] hover:shadow-lg hover:shadow-indigo-500/25 transition">
                            Cari via Peta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-4 md:grid-cols-3">
        <div class="card p-6">
            <div class="text-sm font-extrabold text-slate-900">Eksplorasi Spasial Interaktif</div>
            <div class="mt-2 text-sm text-slate-600">Pencarian properti berbasis peta dengan filter yang cepat dan intuitif.</div>
        </div>
        <div class="card p-6">
            <div class="text-sm font-extrabold text-slate-900">Smart Proximity Analytics</div>
            <div class="mt-2 text-sm text-slate-600">Lihat fasilitas terdekat dan estimasi jarak untuk pengambilan keputusan.</div>
        </div>
        <div class="card p-6">
            <div class="text-sm font-extrabold text-slate-900">Mitigasi Risiko Banjir</div>
            <div class="mt-2 text-sm text-slate-600">Lapisan zona banjir membantu memilih lokasi yang lebih aman.</div>
        </div>
    </section>

    <section class="mt-12">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="text-lg font-extrabold text-slate-900">Listing Terbaru</div>
                <div class="mt-1 text-sm text-slate-600">Listing properti terbaru dari area Kota Samarinda.</div>
            </div>
            <a href="{{ route('explore') }}" class="btn btn-outline">Lihat Semua</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($recentProperties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    </section>
</x-layouts.app>

