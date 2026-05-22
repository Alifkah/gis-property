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
</x-layouts.app>
