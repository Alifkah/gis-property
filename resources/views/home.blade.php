<x-layouts.app title="Cari Properti Bebas Banjir Samarinda"
    description="Temukan rumah, tanah, dan ruko terbaik di Kota Samarinda dengan analisis geospasial kerawanan banjir, rute terdekat ke sekolah, rumah sakit, dan pasar.">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Hero Section (Split Screen, Light cream background, bright & welcoming) --}}
        <section class="relative pt-12 md:pt-20 pb-16 md:pb-24 grid gap-12 lg:grid-cols-2 items-center">
            {{-- Left: Text content --}}
            <div class="space-y-6 md:space-y-8 text-left">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-primary/5 px-3 py-1 text-xs font-semibold text-brand-primary ring-1 ring-inset ring-brand-primary/10">
                    <span class="size-1.5 rounded-full bg-brand-accent animate-pulse"></span>
                    Portal Geospasial Samarinda
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-extrabold tracking-tight text-balance text-slate-900 leading-tight">
                    Jual Beli Properti Samarinda, Presisi dan Bebas Cemas.
                </h1>
                <p class="text-lg text-slate-600 max-w-[50ch] leading-relaxed">
                    Cari properti strategis di Samarinda dengan peta interaktif bebas banjir dan analisis rute terdekat secara real-time.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('explore') }}" class="btn btn-primary px-7 py-3.5 shadow-md shadow-brand-primary/10 hover:shadow-brand-primary/20">
                        <i class="ti ti-search text-base"></i>
                        <span>Cari Properti</span>
                    </a>
                    <a href="{{ auth()->check() ? route('seller.listings.create') : route('register') }}" class="btn btn-outline px-7 py-3.5">
                        <span>Jual Properti</span>
                    </a>
                </div>
            </div>

            {{-- Right: Visual mock-up (real estate photo with rounded-3xl, shadow-2xl, rotate-2) --}}
            <div class="relative flex justify-center lg:justify-end">
                <div class="relative w-full max-w-lg aspect-[4/3] rounded-3xl shadow-2xl rotate-2 overflow-hidden bg-slate-100 group border-4 border-white">
                    <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80" 
                         alt="Premium House in Samarinda" 
                         class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent pointer-events-none"></div>
                </div>
            </div>
        </section>

        {{-- Search Bar (Separate horizontal section card below Hero) --}}
        <section class="mb-16 -mt-8 relative z-30">
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-100/80">
                <form action="{{ route('explore') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                    {{-- Search keyword --}}
                    <div class="flex-1 min-w-0">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kata Kunci</label>
                        <div class="relative mt-1">
                            <input type="text" name="q" placeholder="Cari lokasi, perumahan, dsb..." class="input pl-9" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-search text-sm"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Type select filter --}}
                    <div class="w-full lg:w-48">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tipe</label>
                        <select name="type" class="select mt-1 bg-white">
                            <option value="">Semua Properti</option>
                            <option value="Rumah">Rumah</option>
                            <option value="Tanah">Tanah</option>
                        </select>
                    </div>

                    {{-- District select filter --}}
                    <div class="w-full lg:w-56">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kecamatan</label>
                        <select name="district" class="select mt-1 bg-white">
                            <option value="">Semua Kecamatan</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->name }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price range select filter --}}
                    <div class="w-full lg:w-52">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Rentang Harga</label>
                        <select name="price" class="select mt-1 bg-white">
                            <option value="">Semua Harga</option>
                            <option value="0-250000000">0 - 250 jt</option>
                            <option value="250000000-750000000">250 jt - 750 jt</option>
                            <option value="750000000-2000000000">750 jt - 2 M</option>
                            <option value="2000000000-999999999999">2 M+</option>
                        </select>
                    </div>

                    {{-- Submit button --}}
                    <div class="w-full lg:w-auto flex items-end">
                        <button type="submit" class="btn btn-accent w-full lg:px-6 shadow-md hover:shadow-brand-accent/20 flex items-center justify-center gap-2">
                            <i class="ti ti-adjustments-horizontal text-base"></i>
                            <span>Cari</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Feature Section (3 Fitur Utama) --}}
        <section class="py-16 md:py-24 border-t border-slate-100">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight font-display">Mengapa Samarinda Properti?</h2>
                <p class="mt-3 text-sm text-slate-500 font-semibold leading-relaxed">Platform terpercaya yang menghadirkan teknologi geospasial untuk kemudahan kepemilikan aset properti di Samarinda.</p>
            </div>
            
            <div class="grid gap-8 md:grid-cols-3">
                <div class="group bg-white rounded-2xl p-8 text-center border border-slate-100 shadow-sm transition duration-300 hover:shadow-md hover:-translate-y-1">
                    <span class="grid size-12 place-items-center rounded-2xl bg-brand-primary/5 text-brand-primary transition group-hover:scale-105 duration-300 mx-auto">
                        <i class="ti ti-map-2 text-2xl"></i>
                    </span>
                    <h3 class="text-lg font-bold text-slate-950 mt-5 font-display">Eksplorasi Spasial</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed font-semibold">Cari dan jelajahi listing properti berbasis peta interaktif dengan filter spasial cepat dan dinamis.</p>
                </div>
                <div class="group bg-white rounded-2xl p-8 text-center border border-slate-100 shadow-sm transition duration-300 hover:shadow-md hover:-translate-y-1">
                    <span class="grid size-12 place-items-center rounded-2xl bg-brand-primary/5 text-brand-primary transition group-hover:scale-105 duration-300 mx-auto">
                        <i class="ti ti-location text-2xl"></i>
                    </span>
                    <h3 class="text-lg font-bold text-slate-950 mt-5 font-display">Proximity Analytics</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed font-semibold">Ketahui jarak rute jalan menuju fasilitas penting terdekat seperti sekolah, rumah sakit, dan pasar.</p>
                </div>
                <div class="group bg-white rounded-2xl p-8 text-center border border-slate-100 shadow-sm transition duration-300 hover:shadow-md hover:-translate-y-1">
                    <span class="grid size-12 place-items-center rounded-2xl bg-brand-primary/5 text-brand-primary transition group-hover:scale-105 duration-300 mx-auto">
                        <i class="ti ti-droplet text-2xl"></i>
                    </span>
                    <h3 class="text-lg font-bold text-slate-950 mt-5 font-display">Mitigasi Risiko Banjir</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed font-semibold">Mencegah kerugian investasi dengan memeriksa batas zona bahaya banjir Kota Samarinda.</p>
                </div>
            </div>
        </section>

        {{-- Recently Viewed Properties (Jika ada) --}}
        <section x-data="{ recentlyViewed: [] }"
            x-init="recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewedProperties') || '[]');"
            x-show="recentlyViewed.length > 0" class="py-16 md:py-24 border-t border-slate-100" x-cloak>
            
            <div class="flex items-center justify-between gap-4 mb-8">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Terakhir Dilihat</h2>
                <button @click="localStorage.removeItem('recentlyViewedProperties'); recentlyViewed = [];"
                    class="btn btn-outline min-h-0 py-1.5 px-3 text-xs flex items-center gap-1.5 border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700 cursor-pointer">
                    <i class="ti ti-trash text-xs"></i>
                    <span>Hapus Riwayat</span>
                </button>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 overflow-x-auto snap-x scrollbar-none flex-nowrap sm:flex-wrap">
                <template x-for="item in recentlyViewed" :key="item.id">
                    <div class="group card overflow-hidden relative transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-white snap-align-start cursor-pointer min-w-[260px] sm:min-w-0">
                        {{-- Pseudo-link stretches over card --}}
                        <a :href="item.url" class="absolute inset-0 z-10"><span class="sr-only" x-text="'Lihat ' + item.title"></span></a>

                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            <img :src="item.imageUrl" :alt="item.title" class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none"></div>
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2 z-20">
                                <span class="inline-flex items-center rounded-full bg-slate-800/90 backdrop-blur-xs px-2.5 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider" x-text="item.type"></span>
                            </div>
                        </div>

                        <div class="p-4 relative">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-base font-bold text-slate-900 leading-snug" x-text="item.title"></div>
                                    <div class="mt-1 truncate text-sm text-slate-500 flex items-center gap-1">
                                        <i class="ti ti-map-pin text-slate-400 text-base"></i>
                                        <span x-text="item.districtName"></span>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <div class="text-lg font-extrabold leading-snug text-brand-accent" x-text="item.price"></div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-semibold text-slate-600 relative z-20">
                                <div class="flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/40 px-2 py-2">
                                    <i class="ti ti-bed text-slate-400 text-base"></i>
                                    <span x-text="item.bedroom"></span>
                                </div>
                                <div class="flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/40 px-2 py-2">
                                    <i class="ti ti-bath text-slate-400 text-base"></i>
                                    <span x-text="item.bathroom"></span>
                                </div>
                                <div class="flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/40 px-2 py-2">
                                    <i class="ti ti-maximize text-slate-400 text-base"></i>
                                    <span x-text="item.landArea + ' m²'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        {{-- Latest Properties Section --}}
        <section class="py-16 md:py-24 border-t border-slate-100">
            <div class="flex items-center justify-between gap-4 mb-8">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Properti Terbaru</h2>
                <a href="{{ route('properties.index') }}" class="btn btn-outline min-h-0 py-1.5 px-3.5 text-xs flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="ti ti-arrow-narrow-right text-sm"></i>
                </a>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($recentProperties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>