<x-layouts.app>
    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Semua Properti</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $properties->total() }} properti ditemukan di Kota Samarinda
            </p>
        </div>
        <a href="{{ route('explore') }}" class="btn btn-outline flex items-center gap-2">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6-10l6-3m0 16l5.447-2.724A1 1 0 0021 16.382V5.618a1 1 0 00-1.447-.894L15 7m0 13V7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Lihat di Peta
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
        {{-- Sidebar Filter --}}
        <aside>
            <form method="GET" action="{{ route('properties.index') }}" class="card p-5 grid gap-4 lg:sticky lg:top-24">
                <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Filter</div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Kata Kunci</label>
                    <input name="q" type="text" class="input mt-1" placeholder="Judul properti..." value="{{ request('q') }}" />
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
                            <option value="{{ $district->name }}" @selected(request('district') === $district->name)>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Rentang Harga</label>
                    <select name="price" class="select mt-1">
                        <option value="">Semua Harga</option>
                        <option value="0-250000000" @selected(request('price') === '0-250000000')>0 – 250 juta</option>
                        <option value="250000000-750000000" @selected(request('price') === '250000000-750000000')>250 – 750 juta</option>
                        <option value="750000000-2000000000" @selected(request('price') === '750000000-2000000000')>750 jt – 2 M</option>
                        <option value="2000000000-999999999999" @selected(request('price') === '2000000000-999999999999')>> 2 Miliar</option>
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
                        <button type="button" onclick="activateSearchAlert()" class="btn btn-outline text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 border-brand-primary/20 w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold cursor-pointer">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
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
                        <x-property-card :property="$property" :show-favorite="true" :is-favorited="in_array($property->id, $favoritedIds)" />
                    @endforeach
                </div>

                @if ($properties->hasPages())
                    <div class="mt-6">{{ $properties->links() }}</div>
                @endif
            @endif
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
</x-layouts.app>
