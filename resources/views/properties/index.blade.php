<x-layouts.app title="Semua Listing Properti Samarinda"
    description="Telusuri katalog lengkap rumah dijual, tanah kavling, ruko komersial di seluruh kecamatan Samarinda dengan harga terbaik dan informasi status bebas banjir.">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold text-slate-900 leading-none">Properti</h1>
                <span class="bg-brand-primary/10 text-brand-primary text-xs font-bold px-2.5 py-0.5 rounded-full leading-none">
                    {{ $properties->total() }} Unit
                </span>
            </div>
            <a href="{{ route('explore') }}" class="btn btn-outline flex items-center gap-2 text-xs">
                <i class="ti ti-map-2 text-sm text-brand-primary"></i>
                <span>Lihat di Peta</span>
            </a>
        </div>

        {{-- Search Input (Autosubmits on Enter) --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('properties.index') }}" class="relative rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm flex items-center gap-2">
                @foreach(request()->except('q', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="pl-3 text-slate-400">
                    <i class="ti ti-search text-base"></i>
                </div>
                <input type="text" name="q" placeholder="Cari properti berdasarkan judul, perumahan..." class="w-full bg-transparent py-2 text-sm text-slate-800 focus:outline-hidden" value="{{ request('q') }}" />
                <button type="submit" class="btn btn-primary px-5 py-2 text-xs font-bold shrink-0">Cari</button>
            </form>
        </div>

        {{-- Filter Bar (Horizontal row) --}}
        <div class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100 pb-4">
            {{-- Pill Filters (Semua | Rumah | Tanah | Ruko | Apartemen) --}}
            <div class="flex flex-1 items-center gap-2 overflow-x-auto scrollbar-none py-1 snap-x w-full">
                @php $currentType = request('type'); @endphp
                <a href="{{ request()->fullUrlWithQuery(['type' => '', 'page' => null]) }}" 
                   class="snap-align-start shrink-0 rounded-full border text-xs px-4 py-2 font-semibold transition
                          {{ empty($currentType) ? 'bg-brand-primary text-white border-brand-primary shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }}">Semua</a>
                <a href="{{ request()->fullUrlWithQuery(['type' => 'Rumah', 'page' => null]) }}" 
                   class="snap-align-start shrink-0 rounded-full border text-xs px-4 py-2 font-semibold transition
                          {{ $currentType === 'Rumah' ? 'bg-brand-primary text-white border-brand-primary shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }}">Rumah</a>
                <a href="{{ request()->fullUrlWithQuery(['type' => 'Tanah', 'page' => null]) }}" 
                   class="snap-align-start shrink-0 rounded-full border text-xs px-4 py-2 font-semibold transition
                          {{ $currentType === 'Tanah' ? 'bg-brand-primary text-white border-brand-primary shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }}">Tanah</a>
                <a href="{{ request()->fullUrlWithQuery(['type' => 'Ruko', 'page' => null]) }}" 
                   class="snap-align-start shrink-0 rounded-full border text-xs px-4 py-2 font-semibold transition
                          {{ $currentType === 'Ruko' ? 'bg-brand-primary text-white border-brand-primary shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }}">Ruko</a>
                <a href="{{ request()->fullUrlWithQuery(['type' => 'Apartemen', 'page' => null]) }}" 
                   class="snap-align-start shrink-0 rounded-full border text-xs px-4 py-2 font-semibold transition
                          {{ $currentType === 'Apartemen' ? 'bg-brand-primary text-white border-brand-primary shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }}">Apartemen</a>
            </div>

            {{-- Sort Dropdown on Right --}}
            <form method="GET" action="{{ request()->url() }}" class="w-full sm:w-auto shrink-0 flex items-center justify-end">
                @foreach(request()->except('sort', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="sort" onchange="this.form.submit()" class="select bg-white py-2 px-3 text-xs w-full sm:w-auto font-semibold">
                    <option value="newest" @selected(request('sort') === 'newest' || !request('sort'))>Terbaru</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Harga: Terendah</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga: Tertinggi</option>
                </select>
            </form>
        </div>

        {{-- Property Grid & Empty States --}}
        @if ($properties->isEmpty())
            <div class="card flex flex-col items-center gap-5 p-16 text-center border border-slate-200 bg-white shadow-sm max-w-xl mx-auto my-12">
                <div class="grid size-16 place-items-center rounded-2xl bg-slate-50 text-slate-300 border border-slate-100">
                    <i class="ti ti-building-off text-3xl"></i>
                </div>
                <div class="max-w-xs">
                    <h3 class="text-base font-extrabold text-slate-900">Tidak ada properti ditemukan</h3>
                    <p class="mt-2 text-xs font-semibold text-slate-500 leading-relaxed">Coba ubah kata kunci atau hapus beberapa filter untuk menemukan properti yang Anda cari.</p>
                </div>
                <a href="{{ route('properties.index') }}" class="btn btn-primary px-6 py-2 shadow-xs transition text-xs font-bold">Reset Semua Filter</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" :show-favorite="true"
                        :is-favorited="in_array($property->id, $favoritedIds)" />
                @endforeach
            </div>

            {{-- Custom Pagination --}}
            @if ($properties->hasPages())
                <div class="mt-12 flex items-center justify-between border-t border-slate-100 pt-6">
                    {{-- Previous Page Button --}}
                    @if ($properties->onFirstPage())
                        <span class="btn btn-outline opacity-50 pointer-events-none text-xs">Sebelumnya</span>
                    @else
                        <a href="{{ $properties->previousPageUrl() }}" class="btn btn-outline text-xs">Sebelumnya</a>
                    @endif

                    {{-- Page Numbers --}}
                    <div class="hidden sm:flex items-center gap-1.5">
                        @foreach ($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}" 
                               class="grid size-9 place-items-center rounded-xl text-xs font-bold transition duration-150
                                      {{ $page == $properties->currentPage() 
                                        ? 'bg-brand-primary text-white' 
                                        : 'hover:bg-slate-100 text-slate-700' }}">
                                {{ $page }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Next Page Button --}}
                    @if ($properties->hasMorePages())
                        <a href="{{ $properties->nextPageUrl() }}" class="btn btn-outline text-xs">Selanjutnya</a>
                    @else
                        <span class="btn btn-outline opacity-50 pointer-events-none text-xs">Selanjutnya</span>
                    @endif
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>