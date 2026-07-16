<x-layouts.app title="Properti Tersimpan (Favorit)"
    description="Lihat kembali daftar properti impian Anda yang telah disimpan. Bandingkan harga, luas tanah, dan ketersediaan unit secara langsung.">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Properti Tersimpan</span>
                    @if ($properties->isNotEmpty())
                        <span class="bg-brand-primary/10 text-brand-primary text-xs font-bold px-2.5 py-0.5 rounded-full">
                            {{ $properties->count() }} Unit
                        </span>
                    @endif
                </h1>
                <p class="mt-1.5 text-xs font-semibold text-slate-500">Bandingkan properti impian Anda yang telah disimpan di satu tempat.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-outline flex items-center gap-1.5 text-xs font-bold self-start sm:self-auto">
                <i class="ti ti-arrow-left text-sm text-brand-primary"></i>
                <span>Kembali ke Katalog</span>
            </a>
        </div>

        {{-- Properties Grid / Empty State --}}
        @if ($properties->isEmpty())
            <div class="card flex flex-col items-center gap-5 p-16 text-center border border-slate-200 bg-white shadow-sm max-w-md mx-auto my-12">
                <div class="grid size-16 place-items-center rounded-2xl bg-rose-50 text-rose-500 ring-8 ring-rose-500/5 shadow-inner">
                    <i class="ti ti-heart text-3xl animate-pulse"></i>
                </div>
                <div class="max-w-xs">
                    <h3 class="text-base font-extrabold text-slate-900">Belum ada properti favorit</h3>
                    <p class="mt-2 text-xs font-semibold text-slate-500 leading-relaxed">Simpan properti impian Anda dengan menekan tombol hati ❤️ saat menjelajah katalog properti.</p>
                </div>
                <a href="{{ route('properties.index') }}" class="btn btn-primary px-6 py-2.5 shadow-xs transition text-xs font-bold border-0">Jelajahi Properti Sekarang</a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" :show-favorite="true" :is-favorited="true" />
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
