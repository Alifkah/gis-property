<x-layouts.app title="Semua Listing Properti Samarinda"
    description="Telusuri katalog lengkap rumah dijual, tanah kavling, ruko komersial di seluruh kecamatan Samarinda dengan harga terbaik dan informasi status bebas banjir.">
    <div x-data="{ mobileFilterOpen: false }">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Semua Properti</h1>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $properties->total() }} properti ditemukan di Kota Samarinda
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="mobileFilterOpen = true" class="btn btn-outline lg:hidden flex items-center gap-2 cursor-pointer">
                    <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('explore') }}" class="btn btn-outline flex items-center gap-2">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6-10l6-3m0 16l5.447-2.724A1 1 0 0021 16.382V5.618a1 1 0 00-1.447-.894L15 7m0 13V7"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Lihat di Peta</span>
                </a>
            </div>
        </div>

    <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
        {{-- Mobile Backdrop Overlay --}}
        <div x-show="mobileFilterOpen" @click="mobileFilterOpen = false" x-cloak class="fixed inset-0 z-[950] bg-slate-900/40 lg:hidden"></div>

        {{-- Sidebar Filter --}}
        <aside class="fixed inset-y-0 left-0 z-[1000] w-72 bg-white shadow-xl transition-transform duration-300 overflow-y-auto h-full lg:static lg:z-auto lg:w-auto lg:shadow-none lg:bg-transparent lg:overflow-y-visible lg:h-auto"
               :class="mobileFilterOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <form method="GET" action="{{ route('properties.index') }}" class="p-5 grid gap-4 lg:bg-white lg:rounded-2xl lg:shadow-xs lg:border lg:border-slate-200/40 lg:ring-1 lg:ring-black/[0.01] lg:sticky lg:top-24 lg:p-5"
                x-data="searchHistoryData()" x-init="initHistory()">
                
                {{-- Mobile Filter Header --}}
                <div class="flex items-center justify-between lg:hidden border-b border-slate-100 pb-3">
                    <span class="text-xs font-black text-slate-900 uppercase">Filter</span>
                    <button type="button" @click="mobileFilterOpen = false" class="size-8 rounded-xl text-slate-400 hover:bg-slate-50 flex items-center justify-center transition cursor-pointer">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 hidden lg:block">Filter</div>

                {{-- Riwayat Pencarian --}}
                <div x-show="searches.length > 0" class="border-b border-slate-100 pb-3" x-cloak>
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Pencarian
                            Terakhir</label>
                        <button type="button" @click="clearSearches()"
                            class="text-[10px] font-bold text-rose-500 hover:text-rose-700 cursor-pointer">Hapus</button>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <template x-for="(s, idx) in searches" :key="idx">
                            <a :href="s.url"
                                class="inline-flex items-center gap-1 rounded-lg bg-slate-50 border border-slate-200 px-2 py-1 text-[10px] font-semibold text-slate-600 hover:bg-brand-primary/5 hover:text-brand-primary hover:border-brand-primary/20 transition truncate max-w-full"
                                :title="s.title">
                                <span class="truncate" x-text="s.label"></span>
                            </a>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Kata Kunci</label>
                    <input name="q" type="text" class="input mt-1" placeholder="Judul properti..."
                        value="{{ request('q') }}" />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Jenis Properti</label>
                    <select name="type" class="select mt-1">
                        <option value="">Semua</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Kecamatan</label>
                    <select name="district" class="select mt-1">
                        <option value="">Semua Kecamatan</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->name }}" @selected(request('district') === $district->name)>
                                {{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Rentang Harga</label>
                    <select name="price" class="select mt-1">
                        <option value="">Semua Harga</option>
                        <option value="0-250000000" @selected(request('price') === '0-250000000')>0 – 250 juta</option>
                        <option value="250000000-750000000" @selected(request('price') === '250000000-750000000')>250 –
                            750 juta</option>
                        <option value="750000000-2000000000" @selected(request('price') === '750000000-2000000000')>750 jt
                            – 2 M</option>
                        <option value="2000000000-999999999999" @selected(request('price') === '2000000000-999999999999')>
                            > 2 Miliar</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Status</label>
                    <select name="status" class="select mt-1">
                        <option value="">Semua</option>
                        <option value="Tersedia" @selected(request('status') === 'Tersedia')>Tersedia</option>
                        <option value="Terjual" @selected(request('status') === 'Terjual')>Terjual</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Urutkan</label>
                    <select name="sort" class="select mt-1">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Harga: Terendah</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga: Tertinggi</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1">Terapkan</button>
                    @if (request()->hasAny(['q', 'type', 'district', 'price', 'status', 'sort']))
                        <a href="{{ route('properties.index') }}" class="btn btn-outline">Reset</a>
                    @endif
                </div>

                @auth
                    <div class="border-t border-slate-100 pt-3">
                        <button type="button" onclick="activateSearchAlert()"
                            class="btn btn-outline text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 border-brand-primary/20 w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span>Aktifkan Alarm Properti Baru</span>
                        </button>
                    </div>
                @endauth
            </form>
        </aside>

        {{-- Grid Properti --}}
        <div>
            @if ($properties->isEmpty())
                <div class="card flex flex-col items-center gap-3 p-10 text-center">
                    <div class="text-sm font-extrabold text-slate-900">Tidak ada properti yang sesuai</div>
                    <div class="text-sm text-slate-500">Coba ubah filter pencarian kamu.</div>
                    <a href="{{ route('properties.index') }}" class="btn btn-outline mt-2">Reset Filter</a>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($properties as $property)
                        <x-property-card :property="$property" :show-favorite="true"
                            :is-favorited="in_array($property->id, $favoritedIds)" />
                    @endforeach
                </div>

                @if ($properties->hasPages())
                    <div class="mt-6">{{ $properties->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>

    @auth
        @push('scripts')
            <script>
                function activateSearchAlert() {
                    const type = document.querySelector('select[name="type"]').value;
                    const district = document.querySelector('select[name="district"]').value;
                    const priceRange = document.querySelector('select[name="price"]').value;

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
                }
            </script>
        @endpush
    @endauth

    @push('scripts')
        <script>
            function searchHistoryData() {
                return {
                    searches: [],
                    initHistory() {
                        this.searches = JSON.parse(localStorage.getItem('recentSearches') || '[]');

                        // Parse current URL params
                        const params = new URLSearchParams(window.location.search);
                        const q = params.get('q');
                        const type = params.get('type');
                        const district = params.get('district');
                        const price = params.get('price');
                        const status = params.get('status');

                        if (q || type || district || price || status) {
                            let labels = [];
                            if (q) labels.push(`"${q}"`);
                            if (type) labels.push(type);
                            if (district) labels.push(district);
                            if (price) {
                                if (price === '0-250000000') labels.push('0-250 jt');
                                else if (price === '250000000-750000000') labels.push('250-750 jt');
                                else if (price === '750000000-2000000000') labels.push('750 jt-2 M');
                                else if (price === '2000000000-999999999999') labels.push('> 2 M');
                            }
                            if (status) labels.push(status);

                            const label = labels.join(' • ');
                            const url = window.location.search;

                            // Save if not already exists
                            let currentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                            currentSearches = currentSearches.filter(item => item.url !== url);
                            currentSearches.unshift({ label: label, url: window.location.pathname + url, title: label });
                            currentSearches = currentSearches.slice(0, 5); // Keep last 5 searches
                            localStorage.setItem('recentSearches', JSON.stringify(currentSearches));
                            this.searches = currentSearches;
                        }
                    },
                    clearSearches() {
                        localStorage.removeItem('recentSearches');
                        this.searches = [];
                    }
                }
            }
        </script>
    @endpush
</x-layouts.app>