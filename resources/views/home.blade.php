<x-layouts.app>
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl text-white shadow-xl bg-gradient-to-br from-brand-primary via-slate-900 to-brand-primary">
        {{-- High performance decorative SVG background pattern - Topographic / GIS contour style --}}
        <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 500" preserveAspectRatio="none">
                <g fill="none" stroke="currentColor" stroke-width="1.5">
                    <!-- Contour lines -->
                    <path d="M-100,100 C200,50 300,250 500,150 C700,50 800,200 1100,100" />
                    <path d="M-100,150 C200,100 300,300 500,200 C700,100 800,250 1100,150" />
                    <path d="M-100,200 C200,150 300,350 500,250 C700,150 800,300 1100,200" />
                    <path d="M-100,250 C200,200 300,400 500,300 C700,200 800,350 1100,250" />
                    <path d="M-100,300 C200,250 300,450 500,350 C700,250 800,400 1100,300" />
                    <path d="M-100,350 C200,300 300,500 500,400 C700,300 800,450 1100,350" />
                    <path d="M-100,400 C200,350 300,550 500,450 C700,350 800,500 1100,400" />
                    <!-- Grid coordinates indicators -->
                    <line x1="100" y1="0" x2="100" y2="500" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="300" y1="0" x2="300" y2="500" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="500" y1="0" x2="500" y2="500" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="700" y1="0" x2="700" y2="500" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="900" y1="0" x2="900" y2="500" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="0" y1="100" x2="1000" y2="100" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="0" y1="250" x2="1000" y2="250" stroke-opacity="0.2" stroke-dasharray="5 5" />
                    <line x1="0" y1="400" x2="1000" y2="400" stroke-opacity="0.2" stroke-dasharray="5 5" />
                </g>
            </svg>
        </div>
        <div class="absolute inset-0 bg-brand-primary/80 backdrop-blur-[1px] pointer-events-none"></div>
        <div class="absolute -left-12 -top-12 size-72 rounded-full bg-brand-accent/10 blur-3xl"></div>
        <div class="absolute -right-12 -bottom-12 size-72 rounded-full bg-brand-primary/20 blur-3xl"></div>

        <div class="relative px-4 py-16 sm:px-10 sm:py-24 z-10">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1 text-xs font-semibold text-white/95 ring-1 ring-inset ring-white/20 mb-6 shadow-xs backdrop-blur-xs">
                    <span class="size-1.5 rounded-full bg-brand-accent animate-pulse"></span>
                    Portal Geospasial Samarinda
                </span>
                <h1 class="text-balance text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-tight font-display">
                    Jual Beli Properti Samarinda, Presisi dan Bebas Cemas.
                </h1>
                <p class="mt-4 text-pretty text-sm font-semibold text-slate-200/90 sm:mt-5 sm:text-base max-w-2xl mx-auto leading-relaxed">
                    Temukan hunian strategis di zona aman banjir, atau pasarkan aset properti Anda ke audiens yang tepat dengan dukungan teknologi pemetaan cerdas.
                </p>

                {{-- Action Buttons --}}
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('explore') }}" class="btn btn-accent px-6 py-3 font-bold text-sm shadow-lg shadow-brand-accent/20">
                        🔍 Mulai Pencarian
                    </a>
                    <a href="{{ auth()->check() ? route('seller.listings.create') : route('register') }}" class="btn btn-outline border-white/30 bg-white/10 text-white hover:bg-white/20 hover:text-white px-6 py-3 font-bold text-sm backdrop-blur-xs">
                        Daftar Aset Anda
                    </a>
                </div>
            </div>

            {{-- Floating Search Bar --}}
            <div class="mx-auto mt-10 sm:mt-16 max-w-4xl">
                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/40 shadow-2xl bg-white/95 backdrop-blur-md">
                    <form action="{{ route('explore') }}" method="GET" class="grid gap-4 sm:grid-cols-2 md:grid-cols-4 sm:items-end">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Tipe Properti</label>
                            <select name="type" class="select mt-2" style="background: #ffffff; color: #0f172a;">
                                <option value="">Semua</option>
                                <option value="Rumah">Rumah</option>
                                <option value="Tanah">Tanah</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Pilih Kecamatan</label>
                            <select name="district" class="select mt-2" style="background: #ffffff; color: #0f172a;">
                                <option value="">Semua</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->name }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Rentang Harga</label>
                            <select name="price" class="select mt-2" style="background: #ffffff; color: #0f172a;">
                                <option value="">Semua</option>
                                <option value="0-250000000">0 - 250 jt</option>
                                <option value="250000000-750000000">250 jt - 750 jt</option>
                                <option value="750000000-2000000000">750 jt - 2 M</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-accent w-full hover:shadow-lg hover:shadow-brand-accent/25 transition-all flex items-center justify-center gap-2">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <span class="font-bold">Cari</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur Utama --}}
    <section class="mt-6 sm:mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="group card p-6 hover:-translate-y-1 hover:shadow-md hover:border-brand-primary/20 transition duration-300">
            <span class="grid size-12 place-items-center rounded-2xl bg-brand-primary/5 text-brand-primary transition group-hover:scale-105 duration-300">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.89-1.63a1.875 1.875 0 001.108-1.723V1.35c0-.847-.788-1.54-1.618-1.328l-4.708 1.205M9.623 3.328L3.3 5.4a1.875 1.875 0 00-1.29 1.783v12.285c0 .762.583 1.417 1.34 1.328l6.233-1.205m0-14.542L15.5 1.3M9 6.75L15.5 4.5m-.5 10.5L9 15" />
                </svg>
            </span>
            <div class="text-sm font-extrabold text-slate-900 mt-4 font-display">Eksplorasi Spasial Interaktif</div>
            <div class="mt-2 text-xs font-semibold leading-relaxed text-slate-500">Cari dan jelajahi berbagai listing properti berbasis peta interaktif dengan filter pencarian spasial yang dinamis dan super cepat.</div>
        </div>
        <div class="group card p-6 hover:-translate-y-1 hover:shadow-md hover:border-brand-primary/20 transition duration-300">
            <span class="grid size-12 place-items-center rounded-2xl bg-brand-primary/5 text-brand-primary transition group-hover:scale-105 duration-300">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <div class="text-sm font-extrabold text-slate-900 mt-4 font-display">Smart Proximity Analytics</div>
            <div class="mt-2 text-xs font-semibold leading-relaxed text-slate-500">Ketahui fasilitas penting terdekat seperti sekolah, rumah sakit, dan pasar, lengkap dengan kalkulasi jarak tempuh presisi.</div>
        </div>
        <div class="group card p-6 hover:-translate-y-1 hover:shadow-md hover:border-brand-primary/20 transition duration-300">
            <span class="grid size-12 place-items-center rounded-2xl bg-brand-primary/5 text-brand-primary transition group-hover:scale-105 duration-300">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0-4.5 4.5V21h9v-1.5A4.5 4.5 0 0 0 2.25 15ZM12 18.75V21M12 3a9 9 0 0 0-9 9m9-9a9 9 0 0 1 9 9m-9-9v2.25m9 6.75H21M3 12h2.25m11.364-5.636-1.591 1.591M6.393 17.607l1.591-1.591m8.25-8.25-1.591 1.591M7.984 6.393l1.591 1.591" />
                </svg>
            </span>
            <div class="text-sm font-extrabold text-slate-900 mt-4 font-display">Mitigasi Risiko Banjir</div>
            <div class="mt-2 text-xs font-semibold leading-relaxed text-slate-500">Mencegah kerugian finansial dengan memeriksa lapisan zona kerawanan banjir Kota Samarinda sebelum melakukan transaksi.</div>
        </div>
    </section>

    {{-- Recently Viewed Properties --}}
    <section x-data="{ recentlyViewed: [] }" x-init="recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewedProperties') || '[]');" x-show="recentlyViewed.length > 0" class="mt-14" x-cloak>
        <div class="flex items-end justify-between gap-4 border-b border-slate-200/50 pb-4">
            <div>
                <div class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2 font-display">
                    <span class="size-2 rounded-full bg-brand-accent"></span>
                    <span>Terakhir Anda Lihat</span>
                </div>
                <div class="mt-1 text-xs font-semibold text-slate-500">Daftar properti yang baru saja Anda kunjungi di aplikasi ini.</div>
            </div>
            <button @click="localStorage.removeItem('recentlyViewedProperties'); recentlyViewed = [];" class="btn btn-outline py-1.5 px-3 text-xs flex items-center gap-1.5 border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700 cursor-pointer">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Bersihkan Riwayat</span>
            </button>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <template x-for="item in recentlyViewed" :key="item.id">
                <div class="group card overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md relative">
                    <a :href="item.url" class="block">
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            <img :src="item.imageUrl" :alt="item.title" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy" />
                            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-md bg-white/95 px-2 py-0.5 text-[10px] font-bold text-slate-700 uppercase tracking-wider ring-1 ring-slate-200" x-text="item.type"></span>
                            </div>
                        </div>
                    </a>

                    <a :href="item.url" class="block p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-bold text-slate-900" x-text="item.title"></div>
                                <div class="mt-1 truncate text-xs font-semibold text-slate-500" x-text="item.districtName"></div>
                            </div>
                            <div class="shrink-0 text-right">
                                <div class="text-sm font-extrabold text-brand-accent" x-text="item.price"></div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-semibold text-slate-600">
                            <div class="flex items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-2 ring-1 ring-slate-200/60" title="Kamar Tidur">
                                <svg class="size-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 4v16" />
                                    <path d="M2 17h20" />
                                    <path d="M22 8v12" />
                                    <path d="M2 8h20" />
                                    <path d="M6 12h4a2 2 0 0 0 2-2V8H4v2a2 2 0 0 0 2 2Z" />
                                </svg>
                                <span x-text="item.bedroom"></span>
                            </div>
                            <div class="flex items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-2 ring-1 ring-slate-200/60" title="Kamar Mandi">
                                <svg class="size-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-1C4.3 2.5 3 4.3 3.5 5.5l2 2" />
                                    <path d="M3 11h18a2 2 0 0 1 2 2v2a6 6 0 0 1-6 6H7a6 6 0 0 1-6-6v-2a2 2 0 0 1-2-2Z" />
                                    <path d="M7 21v2M17 21v2" />
                                </svg>
                                <span x-text="item.bathroom"></span>
                            </div>
                            <div class="flex items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-2 ring-1 ring-slate-200/60" title="Luas Tanah">
                                <svg class="size-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h7v7H4z" /><path d="M13 13h7v7h-7z" /><path d="M13 4h7v7h-7z" /><path d="M4 13h7v7H4z" />
                                </svg>
                                <span x-text="item.landArea + ' m²'"></span>
                            </div>
                        </div>
                    </a>
                </div>
            </template>
        </div>
    </section>

    {{-- Listing Terbaru --}}
    <section class="mt-14">
        <div class="flex items-end justify-between gap-4 border-b border-slate-200/50 pb-4">
            <div>
                <div class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2 font-display">
                    <span class="size-2 rounded-full bg-brand-accent"></span>
                    <span>Properti Terbaru</span>
                </div>
                <div class="mt-1 text-xs font-semibold text-slate-500">Kumpulan properti terkini yang baru ditambahkan di wilayah Kota Samarinda.</div>
            </div>
            <a href="{{ route('explore') }}" class="btn btn-outline py-2 px-3 text-xs flex items-center gap-1.5">
                <span>Lihat Semua</span>
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($recentProperties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    </section>
</x-layouts.app>
